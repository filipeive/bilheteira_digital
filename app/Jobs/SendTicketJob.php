<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\EmailService;
use App\Services\WhatsAppService;
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
        public string $channel = 'all' // 'email' | 'whatsapp' | 'all'
    ) {}

    public function handle(EmailService $email, WhatsAppService $whatsapp): void
    {
        $results = [];

        if (in_array($this->channel, ['email', 'all']) && $this->ticket->buyer_email) {
            $results['email'] = $email->sendTicketConfirmation($this->ticket);
        }

        if (in_array($this->channel, ['whatsapp', 'all']) && $this->ticket->buyer_phone) {
            $results['whatsapp'] = $whatsapp->sendTicketConfirmation($this->ticket);
        }

        AuditService::log('sent_ticket_notification', $this->ticket, [], $results);
        Log::info("SendTicketJob: {$this->ticket->ticket_code}", $results);
    }

    public function failed(\Throwable $e): void
    {
        Log::error("SendTicketJob falhou [{$this->ticket->ticket_code}]: " . $e->getMessage());
        AuditService::log('send_ticket_failed', $this->ticket, [], ['error' => $e->getMessage()]);
    }
}
