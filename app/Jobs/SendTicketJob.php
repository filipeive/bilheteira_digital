<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\EmailService;
use App\Services\SmsService;
use App\Services\AuditService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;
    public int $backoff = 60;

    public function __construct(
        public Ticket $ticket,
        public string $channel = 'all' // 'email' | 'sms' | 'all'
    ) {}

    public function handle(EmailService $email, SmsService $sms): void
    {
        try {
            $results = [];

            if (in_array($this->channel, ['email', 'all']) && $this->ticket->buyer_email) {
                if ($this->ticket->status === 'confirmed') {
                    $results['email'] = $email->sendTicketConfirmation($this->ticket);
                } elseif ($this->ticket->status === 'pending') {
                    $results['email'] = $email->sendPaymentPending($this->ticket);
                }
            }

            if (in_array($this->channel, ['sms', 'whatsapp', 'all']) && $this->ticket->buyer_phone) {
                if ($this->ticket->status === 'confirmed') {
                    $results['sms'] = $sms->sendConfirmation($this->ticket);
                } elseif ($this->ticket->status === 'pending') {
                    $results['sms'] = $sms->sendPaymentPending($this->ticket);
                }
            }

            AuditService::log('sent_ticket_notification', $this->ticket, [], $results);
            Log::info("SendTicketJob: {$this->ticket->ticket_code}", $results);
        } catch (\Throwable $e) {
            Log::error("SendTicketJob failed: " . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error("SendTicketJob falhou [{$this->ticket->ticket_code}]: " . $e->getMessage());
        AuditService::log('send_ticket_failed', $this->ticket, [], ['error' => $e->getMessage()]);
    }
}
