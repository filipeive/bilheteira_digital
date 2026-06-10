<?php

namespace App\Jobs;

use App\Services\EmailService;
use App\Services\SmsService;
use App\Services\AuditService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBulkTicketsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $tickets,
        public string $channel = 'all' // 'email' | 'sms' | 'all'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(EmailService $email, SmsService $sms): void
    {
        try {
            if (empty($this->tickets)) {
                return;
            }

            $results = [];
            $firstTicket = $this->tickets[0];
            $status = $firstTicket->status;

            if (in_array($this->channel, ['email', 'all']) && $firstTicket->buyer_email) {
                if ($status === 'confirmed') {
                    $results['email'] = $email->sendBulkTicketConfirmation($this->tickets);
                } elseif ($status === 'pending') {
                    $results['email'] = $email->sendBulkPaymentPending($this->tickets);
                }
            }

            if (in_array($this->channel, ['sms', 'whatsapp', 'all']) && $firstTicket->buyer_phone) {
                if ($status === 'confirmed') {
                    $results['sms'] = $sms->sendBulkConfirmation($this->tickets);
                } elseif ($status === 'pending') {
                    $results['sms'] = $sms->sendBulkPaymentPending($this->tickets);
                }
            }

            AuditService::log('sent_bulk_ticket_notification', $firstTicket, [], $results);
            Log::info("SendBulkTicketsJob: " . count($this->tickets) . " tickets", $results);
        } catch (\Throwable $e) {
            Log::error("SendBulkTicketsJob failed: " . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }
}
