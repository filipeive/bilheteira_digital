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

    /**
     * Send bulk ticket confirmation email with PDFs attached.
     */
    public function sendBulkTicketConfirmation(array $tickets): bool
    {
        if (empty($tickets) || !$tickets[0]->buyer_email) {
            return false;
        }

        try {
            $qrService = app(QrCodeService::class);
            $pdfPaths = [];

            foreach ($tickets as $ticket) {
                $qrCode = $qrService->generateQrPng($ticket, 400);
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ticket-v2', [
                    'ticket' => $ticket,
                    'qrCode' => $qrCode,
                ])->setPaper([0, 0, 720, 250], 'portrait');

                $pdfPath = 'tickets/' . $ticket->ticket_code . '.pdf';
                \Illuminate\Support\Facades\Storage::disk('local')->put($pdfPath, $pdf->output());
                $pdfPaths[] = \Illuminate\Support\Facades\Storage::disk('local')->path($pdfPath);
                
                $ticket->update(['email_sent_at' => now()]);
            }

            Mail::to($tickets[0]->buyer_email)->send(new TicketMail($tickets, $pdfPaths));

            Log::info("Bulk email sent to {$tickets[0]->buyer_email} for " . count($tickets) . " tickets");
            return true;
        } catch (\Exception $e) {
            Log::error("EmailService::sendBulkTicketConfirmation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send bulk payment pending notification email.
     */
    public function sendBulkPaymentPending(array $tickets): bool
    {
        if (empty($tickets) || !$tickets[0]->buyer_email) {
            return false;
        }

        try {
            Mail::to($tickets[0]->buyer_email)->send(new TicketPendingMail($tickets));
            Log::info("Bulk payment pending email sent to {$tickets[0]->buyer_email} for " . count($tickets) . " tickets");
            return true;
        } catch (\Exception $e) {
            Log::error("EmailService::sendBulkPaymentPending failed: " . $e->getMessage());
            return false;
        }
    }
}
