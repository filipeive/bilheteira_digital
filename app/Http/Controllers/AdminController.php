<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\SiteSetting;
use App\Models\Ticket;
use App\Services\QrCodeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function tickets()
    {
        return view('admin.tickets');
    }

    public function siteContent(): View
    {
        return view('admin.site-content', [
            'settings' => SiteSetting::values($this->siteDefaults()),
            'event' => Event::where('is_active', true)->first(),
        ]);
    }

    public function updateSiteContent(Request $request)
    {
        $validated = $request->validate([
            'hero_label' => ['nullable', 'string', 'max:120'],
            'hero_title' => ['required', 'string', 'max:120'],
            'hero_artists' => ['required', 'string', 'max:160'],
            'hero_support' => ['nullable', 'string', 'max:240'],
            'support_phone' => ['nullable', 'string', 'max:40'],
            'support_whatsapp' => ['nullable', 'string', 'max:40'],
            'hero_image' => ['nullable', 'image', 'max:4096', 'mimes:jpeg,png,jpg,gif,webp'],
            'event_name' => ['nullable', 'string', 'max:100'],
            'event_date' => ['nullable', 'date'],
            'event_venue' => ['nullable', 'string', 'max:100'],
            'event_city' => ['nullable', 'string', 'max:50'],
            'event_doors_open' => ['nullable', 'string', 'max:10'],
            'event_show_time' => ['nullable', 'string', 'max:10'],
            'event_end_time' => ['nullable', 'string', 'max:10'],
        ], [
            'hero_image.uploaded' => 'Falha ao carregar a imagem. Tente novamente.',
            'hero_image.max' => 'A imagem deve ter no máximo 4MB.',
            'hero_image.image' => 'O ficheiro deve ser uma imagem válida.',
            'event_date.date' => 'A data deve estar no formato válido.',
        ]);

        foreach (['hero_label', 'hero_title', 'hero_artists', 'hero_support', 'support_phone', 'support_whatsapp', 'event_doors_open', 'event_show_time', 'event_end_time'] as $key) {
            SiteSetting::putValue($key, $validated[$key] ?? null);
        }

        if ($request->hasFile('hero_image')) {
            $path = $request->file('hero_image')->store('site', 'public');
            SiteSetting::putValue('hero_image', 'storage/' . $path);
        }

        $gallery = json_decode(SiteSetting::get('gallery_images', '[]'), true) ?? [];
        if ($request->has('remove_gallery')) {
            foreach ($request->remove_gallery as $index) {
                unset($gallery[$index]);
            }
            $gallery = array_values($gallery);
        }
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $path = $file->store('gallery', 'public');
                $gallery[] = 'storage/' . $path;
            }
        }
        SiteSetting::putValue('gallery_images', json_encode($gallery));

        $event = Event::where('is_active', true)->first();
        if ($event) {
            $event->update([
                'name' => $validated['event_name'] ?? $event->name,
                'date' => $validated['event_date'] ?? $event->date,
                'venue' => $validated['event_venue'] ?? $event->venue,
                'city' => $validated['event_city'] ?? $event->city,
            ]);
        }

        return redirect()->route('admin.site')->with('status', 'Conteúdo da página inicial actualizado.');
    }

    public function downloadTicket(Ticket $ticket, QrCodeService $qrCodeService): Response
    {
        $ticket->load('event');

        $pdf = Pdf::loadView('pdf.ticket-v2', [
            'ticket' => $ticket,
            'qrCode' => $qrCodeService->generateQrPng($ticket, 300),
        ])->setPaper([0, 0, 720, 250], 'portrait');

        return $pdf->download('bilhete-' . $ticket->ticket_code . '.pdf');
    }

    public function previewTicket(Ticket $ticket, QrCodeService $qrCodeService): Response
    {
        $ticket->load('event');

        $pdf = Pdf::loadView('pdf.ticket-v2', [
            'ticket' => $ticket,
            'qrCode' => $qrCodeService->generateQrPng($ticket, 300),
        ])->setPaper([0, 0, 720, 250], 'portrait');

        return $pdf->stream('bilhete-' . $ticket->ticket_code . '.pdf');
    }

    public function bulkDownloadTickets(Request $request, QrCodeService $qrCodeService): Response
    {
        $ids = explode(',', $request->query('ids', ''));
        $tickets = Ticket::with('event')->whereIn('id', $ids)->get();

        if ($tickets->isEmpty()) {
            abort(404, 'Nenhum bilhete encontrado.');
        }

        $zipFile = tempnam(sys_get_temp_dir(), 'tickets_') . '.zip';
        $zip = new \ZipArchive();
        
        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($tickets as $ticket) {
                $pdf = Pdf::loadView('pdf.ticket-v2', [
                    'ticket' => $ticket,
                    'qrCode' => $qrCodeService->generateQrPng($ticket, 300),
                ])->setPaper([0, 0, 720, 250], 'portrait');
                
                $zip->addFromString('bilhete-' . $ticket->ticket_code . '.pdf', $pdf->output());
            }
            $zip->close();
            
            return response()->download($zipFile, 'bilhetes-' . now()->format('dmY_Hi') . '.zip')->deleteFileAfterSend(true);
        }

        abort(500, 'Não foi possível gerar o ficheiro ZIP.');
    }

    public function downloadTicketPng(Ticket $ticket, QrCodeService $qrCodeService): Response
    {
        $ticket->load('event');

        $pdf = Pdf::loadView('pdf.ticket-v2', [
            'ticket' => $ticket,
            'qrCode' => $qrCodeService->generateQrPng($ticket, 300),
        ])->setPaper([0, 0, 720, 250], 'portrait');

        $tempPdf = tempnam(sys_get_temp_dir(), 'ticket_') . '.pdf';
        file_put_contents($tempPdf, $pdf->output());

        try {
            if (class_exists('\Imagick')) {
                $imagick = new \Imagick();
                $imagick->setResolution(300, 300);
                $imagick->readImage($tempPdf . '[0]');
                $imagick->setImageFormat('png');
                $imageContent = $imagick->getImageBlob();
                $imagick->clear();
                $imagick->destroy();
            } else {
                throw new \Exception('Imagick is not installed.');
            }
        } catch (\Exception $e) {
            $tempPngPrefix = sys_get_temp_dir() . '/ticket_png_' . uniqid();
            exec("pdftoppm -png -singlefile -r 250 " . escapeshellarg($tempPdf) . " " . escapeshellarg($tempPngPrefix));
            $pngFile = $tempPngPrefix . '.png';
            if (!file_exists($pngFile)) {
                @unlink($tempPdf);
                abort(500, 'Não foi possível gerar a imagem PNG do bilhete. Por favor, tente baixar o PDF.');
            }
            $imageContent = file_get_contents($pngFile);
            @unlink($pngFile);
        }

        @unlink($tempPdf);

        return response($imageContent)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="bilhete-' . $ticket->ticket_code . '.png"');
    }

    public function exportCsv(): StreamedResponse
    {
        $event = Event::where('is_active', true)->first();

        $tickets = Ticket::with('scanner')->where('event_id', $event->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="bilhetes_' . now()->format('Y-m-d_H-i') . '.csv"',
        ];

        return response()->streamDownload(function () use ($tickets) {
            $file = fopen('php://output', 'w');
            // BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Código', 'Nome', 'Telefone', 'Email', 'Tipo', 'Preço (MT)',
                'Método Pagamento', 'Referência', 'Status', 'Usado em',
                'Validado Por', 'Data Compra',
            ], ';');

            foreach ($tickets as $ticket) {
                fputcsv($file, [
                    $ticket->ticket_code,
                    $ticket->buyer_name,
                    $ticket->buyer_phone,
                    $ticket->buyer_email ?? '-',
                    $ticket->getTicketTypeLabel(),
                    $ticket->price,
                    strtoupper($ticket->payment_method),
                    $ticket->payment_ref ?? '-',
                    $ticket->getStatusLabel(),
                    $ticket->used_at ? $ticket->used_at->format('d/m/Y H:i') : '-',
                    $ticket->scanner ? $ticket->scanner->name : '-',
                    $ticket->created_at->format('d/m/Y H:i'),
                ], ';');
            }

            fclose($file);
        }, 'bilhetes_' . now()->format('Y-m-d_H-i') . '.csv', $headers);
    }

    private function siteDefaults(): array
    {
        return [
            'hero_label' => 'Alpha Produções & Faith apresentam',
            'hero_title' => 'Concerto Renúncia',
            'hero_artists' => 'Abel Last & Nair Nany',
            'hero_support' => 'Minister Asafe Jamal · Echoes of the Spirit · Muana Careva · Adélia Balice',
            'support_phone' => '87 541 1644',
            'support_whatsapp' => '258875411644',
            'hero_image' => '',
            'event_doors_open' => '16:00',
            'event_show_time' => '18:00',
            'event_end_time' => '23:00',
        ];
    }
}
