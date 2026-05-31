<?php

namespace App\Jobs;

use App\Mail\TicketMail;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Services\QrCodeService;

class SendTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket
    ) {}

    public function handle(QrCodeService $qrService): void
    {
        // 1. Generate PDF
        $qrCode = $qrService->generateQrPng($this->ticket, 400);

        $pdf = Pdf::loadView('pdf.ticket', [
            'ticket' => $this->ticket,
            'qrCode' => $qrCode,
        ]);

        $pdfPath = 'tickets/' . $this->ticket->ticket_code . '.pdf';
        Storage::disk('local')->put($pdfPath, $pdf->output());
        $fullPath = Storage::disk('local')->path($pdfPath);

        // 2. Send Email (if provided)
        if ($this->ticket->buyer_email) {
            Mail::to($this->ticket->buyer_email)->send(new TicketMail($this->ticket, $fullPath));
        }

        // 3. Send WhatsApp (placeholder logic)
        if ($this->ticket->buyer_phone) {
            $this->sendWhatsApp($this->ticket, $fullPath);
        }
    }

    protected function sendWhatsApp(Ticket $ticket, string $pdfPath): void
    {
        $token = config('services.whatsapp.token');
        $phoneId = config('services.whatsapp.phone_id');

        if (!$token || !$phoneId) {
            return;
        }

        // Placeholder for WhatsApp API integration
        // Here you would upload the media to WhatsApp, get the media ID,
        // and send a message template with the attached PDF document.
        \Illuminate\Support\Facades\Log::info("WhatsApp ticket sending placeholder for: {$ticket->buyer_phone}");
    }
}
