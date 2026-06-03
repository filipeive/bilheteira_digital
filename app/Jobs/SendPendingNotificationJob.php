<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\EmailService;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPendingNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(public Ticket $ticket) {}

    public function handle(EmailService $email, WhatsAppService $whatsapp): void
    {
        if ($this->ticket->buyer_email) {
            $email->sendPaymentPending($this->ticket);
        }
        if ($this->ticket->buyer_phone) {
            $whatsapp->sendPaymentPending($this->ticket);
        }
    }
}
