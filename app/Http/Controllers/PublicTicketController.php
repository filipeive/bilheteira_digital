<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\SiteSetting;
use App\Models\Ticket;
use App\Services\QrCodeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PublicTicketController extends Controller
{
    public function index(): View
    {
        $event = Event::where('is_active', true)->first();

        if (!$event) {
            abort(404, 'Nenhum evento activo encontrado.');
        }

        return view('public.sale', [
            'event' => $event,
            'siteSettings' => $this->siteSettings(),
        ]);
    }

    public function about(): View
    {
        $event = Event::where('is_active', true)->first();

        return view('public.about', [
            'event' => $event,
            'siteSettings' => $this->siteSettings(),
        ]);
    }

    public function lookupPage(): View
    {
        $event = Event::where('is_active', true)->first();

        if (!$event) {
            abort(404, 'Nenhum evento activo encontrado.');
        }

        return view('public.lookup', [
            'event' => $event,
            'siteSettings' => $this->siteSettings(),
        ]);
    }

    public function lookup(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'max:20'],
            'ticket_code' => ['nullable', 'string', 'max:50'],
        ]);

        $event = Event::where('is_active', true)->first();

        if (!$event) {
            abort(404, 'Nenhum evento activo encontrado.');
        }

        if (blank($validated['phone'] ?? null) && blank($validated['ticket_code'] ?? null)) {
            return back()->withErrors(['phone' => 'Digite o número de celular para consultar.'])->withInput();
        }

        $query = Ticket::with('event')->where('event_id', $event->id);
        $lookupMode = 'phone';

        if ($request->user() && filled($validated['ticket_code'] ?? null)) {
            $lookupMode = 'ticket_code';
            $ticketCode = $this->extractTicketCode($validated['ticket_code']);
            $query->where('ticket_code', $ticketCode);
        } else {
            $phone = $this->normalizePhone($validated['phone']);
            $query->whereIn('buyer_phone', array_values(array_unique([
                $phone,
                '+' . $phone,
                preg_replace('/^258/', '', $phone),
            ])));
        }

        return view('public.lookup', [
            'event' => $event,
            'siteSettings' => $this->siteSettings(),
            'lookupPhone' => $validated['phone'],
            'lookupCode' => $validated['ticket_code'] ?? null,
            'lookupMode' => $lookupMode,
            'lookupTickets' => $query->latest()->get(),
        ]);
    }

    public function download(Ticket $ticket, QrCodeService $qrCodeService): Response
    {
        $ticket->load('event');

        $pdf = Pdf::loadView('pdf.ticket-v2', [
            'ticket' => $ticket,
            'qrCode' => $qrCodeService->generateQrPng($ticket, 300),
        ])->setPaper([0, 0, 1300, 500], 'portrait');

        return $pdf->download('bilhete-' . $ticket->ticket_code . '.pdf');
    }

    public function downloadPng(Ticket $ticket, QrCodeService $qrCodeService): Response
    {
        $ticket->load('event');

        $pdf = Pdf::loadView('pdf.ticket-v2', [
            'ticket' => $ticket,
            'qrCode' => $qrCodeService->generateQrPng($ticket, 300),
        ])->setPaper([0, 0, 1300, 500], 'portrait');

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
            // Fallback to pdftoppm if Imagick fails
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

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (!str_starts_with($digits, '258') && strlen($digits) === 9) {
            $digits = '258' . $digits;
        }

        return $digits;
    }

    private function extractTicketCode(string $value): string
    {
        $value = trim($value);

        if (str_contains($value, '|')) {
            return strtoupper(strtok($value, '|'));
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded) && isset($decoded['ticket_code'])) {
            return strtoupper((string) $decoded['ticket_code']);
        }

        return strtoupper($value);
    }

    private function siteSettings(): array
    {
        return SiteSetting::values([
            'hero_label' => 'Alpha Produções & Faith apresentam',
            'hero_title' => 'Concerto Renúncia',
            'hero_artists' => 'Abel Last & Nair Nany',
            'hero_support' => 'Minister Asafe Jamal · Echoes of the Spirit · Muana Careva · Adélia Balice',
            'support_phone' => '87 541 1644',
            'support_whatsapp' => '258875411644',
            'hero_image' => '',
        ]);
    }
}
