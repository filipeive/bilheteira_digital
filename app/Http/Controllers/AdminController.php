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
            'portal_name' => ['nullable', 'string', 'max:100'],
            'hero_label' => ['nullable', 'string', 'max:120'],
            'hero_title' => ['required', 'string', 'max:120'],
            'hero_subtitle' => ['nullable', 'string', 'max:300'],
            'hero_artists' => ['nullable', 'string', 'max:160'],
            'hero_support' => ['nullable', 'string', 'max:240'],
            'announcement_text' => ['nullable', 'string', 'max:240'],
            'support_phone' => ['nullable', 'string', 'max:40'],
            'support_whatsapp' => ['nullable', 'string', 'max:40'],
            'social_facebook' => ['nullable', 'string', 'max:200'],
            'social_instagram' => ['nullable', 'string', 'max:200'],
            'social_tiktok' => ['nullable', 'string', 'max:200'],
            'hero_image' => ['nullable', 'image', 'max:4096', 'mimes:jpeg,png,jpg,gif,webp'],
            'event_name' => ['nullable', 'string', 'max:100'],
            'event_date' => ['nullable', 'date'],
            'event_venue' => ['nullable', 'string', 'max:100'],
            'event_city' => ['nullable', 'string', 'max:50'],
            'event_doors_open' => ['nullable', 'string', 'max:10'],
            'event_show_time' => ['nullable', 'string', 'max:10'],
            'event_end_time' => ['nullable', 'string', 'max:10'],
            'about_description' => ['nullable', 'string', 'max:5000'],
            'other_artists_name' => ['nullable', 'array'],
            'other_artists_name.*' => ['nullable', 'string', 'max:120'],
            'other_artists_bio' => ['nullable', 'array'],
            'other_artists_bio.*' => ['nullable', 'string', 'max:500'],
            'other_artists_existing_photo' => ['nullable', 'array'],
            'other_artists_existing_photo.*' => ['nullable', 'string'],
            'other_artists_photo' => ['nullable', 'array'],
            'other_artists_photo.*' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg,gif,webp'],
        ], [
            'hero_image.uploaded' => 'Falha ao carregar a imagem. Tente novamente.',
            'hero_image.max' => 'A imagem deve ter no máximo 4MB.',
            'hero_image.image' => 'O ficheiro deve ser uma imagem válida.',
            'event_date.date' => 'A data deve estar no formato válido.',
        ]);

        foreach ([
            'portal_name', 'hero_label', 'hero_title', 'hero_subtitle', 'hero_artists', 'hero_support',
            'announcement_text', 'support_phone', 'support_whatsapp', 'social_facebook',
            'social_instagram', 'social_tiktok', 'event_doors_open', 'event_show_time',
            'event_end_time', 'about_description'
        ] as $key) {
            SiteSetting::putValue($key, $validated[$key] ?? null);
        }

        if ($request->hasFile('hero_image')) {
            $path = $request->file('hero_image')->store('site', 'public');
            SiteSetting::putValue('hero_image', 'storage/' . $path);
        }

        // Process other artists list
        $otherArtists = [];
        $names = $request->input('other_artists_name', []);
        $bios = $request->input('other_artists_bio', []);
        $existingPhotos = $request->input('other_artists_existing_photo', []);
        $photos = $request->file('other_artists_photo', []);

        for ($i = 0; $i < count($names); $i++) {
            if (empty($names[$i])) continue;
            
            $photoPath = $existingPhotos[$i] ?? '';
            
            if (isset($photos[$i]) && $photos[$i]->isValid()) {
                $path = $photos[$i]->store('artists', 'public');
                $photoPath = 'storage/' . $path;
            }
            
            $otherArtists[] = [
                'name' => $names[$i],
                'bio' => $bios[$i] ?? '',
                'photo' => $photoPath,
            ];
        }
        SiteSetting::putValue('other_artists', json_encode($otherArtists));

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
            'portal_name' => 'Alpha Bilheteira',
            'hero_label' => 'A plataforma de bilheteira digital gospel em Moçambique',
            'hero_title' => 'COMPRE O SEU BILHETE ONLINE',
            'hero_subtitle' => 'Garanta já o seu bilhete oficial para os melhores concertos, festivais e conferências gospel com segurança total via M-Pesa e e-Mola.',
            'hero_artists' => 'Abel Last & Nair Nany',
            'hero_support' => 'Minister Asafe Jamal · Echoes of the Spirit · Muana Careva · Adélia Balice',
            'announcement_text' => '🎉 Concerto Renúncia 2026 em Quelimane — Bilhetes Promocionais com lugares limitados!',
            'support_phone' => '87 541 1644',
            'support_whatsapp' => '258875411644',
            'social_facebook' => 'https://facebook.com/alphaproducoes',
            'social_instagram' => 'https://instagram.com/alphaproducoes',
            'social_tiktok' => 'https://tiktok.com/@alphaproducoes',
            'hero_image' => '',
            'event_doors_open' => '16:00',
            'event_show_time' => '18:00',
            'event_end_time' => '23:00',
            'about_description' => 'A Alpha Bilheteira é a principal plataforma de gestão e venda de bilhetes digitais para eventos cristãos em Moçambique, proporcionando rapidez, segurança e autenticidade via QR Code.',
            'other_artists' => '[]',
        ];
    }

    /**
     * Delete a cancelled ticket permanently.
     * Only cancelled tickets may be deleted to preserve revenue and audit integrity.
     */
    public function deleteTicket(Ticket $ticket): \Illuminate\Http\RedirectResponse
    {
        if ($ticket->status !== 'cancelled') {
            return back()->with('error', 'Apenas bilhetes cancelados podem ser eliminados.');
        }

        \App\Services\AuditService::log(
            action: 'ticket_deleted',
            model: null,
            oldValues: [
                'ticket_code' => $ticket->ticket_code,
                'buyer_name'  => $ticket->buyer_name,
                'status'      => $ticket->status,
                'price'       => $ticket->price,
                'ticket_type' => $ticket->ticket_type,
            ],
            newValues: ['deleted_by' => auth()->id()]
        );

        $ticket->delete();

        return back()->with('success', 'Bilhete cancelado eliminado com sucesso.');
    }
}
