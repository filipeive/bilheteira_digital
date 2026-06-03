<?php

namespace App\Services;

use App\Mail\TicketMail;
use App\Mail\TicketPendingMail;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Send ticket confirmation email with PDF attachment.
     */
    public function sendTicketConfirmation(Ticket $ticket): bool
    {
        if (!$ticket->buyer_email) {
            return false;
        }

        try {
            // Generate PDF
            $qrService = app(QrCodeService::class);
            $qrCode = $qrService->generateQrPng($ticket, 400);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ticket', [
                'ticket' => $ticket,
                'qrCode' => $qrCode,
            ]);

            $pdfPath = 'tickets/' . $ticket->ticket_code . '.pdf';
            \Illuminate\Support\Facades\Storage::disk('local')->put($pdfPath, $pdf->output());
            $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($pdfPath);

            Mail::to($ticket->buyer_email)->send(new TicketMail($ticket, $fullPath));

            $ticket->update(['email_sent_at' => now()]);

            Log::info("Email sent to {$ticket->buyer_email} for ticket {$ticket->ticket_code}");
            return true;
        } catch (\Exception $e) {
            Log::error("EmailService::sendTicketConfirmation failed for {$ticket->ticket_code}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send payment pending notification email.
     */
    public function sendPaymentPending(Ticket $ticket): bool
    {
        if (!$ticket->buyer_email) {
            return false;
        }

        try {
            if (class_exists(\App\Mail\TicketPendingMail::class)) {
                Mail::to($ticket->buyer_email)->send(new TicketPendingMail($ticket));
            }

            Log::info("Payment pending email sent to {$ticket->buyer_email} for {$ticket->ticket_code}");
            return true;
        } catch (\Exception $e) {
            Log::error("EmailService::sendPaymentPending failed: " . $e->getMessage());
            return false;
        }
    }
}
