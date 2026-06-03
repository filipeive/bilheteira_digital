<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEventReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Ticket $ticket) {}

    public function handle(WhatsAppService $whatsapp): void
    {
        if ($this->ticket->buyer_phone && !$this->ticket->reminder_sent_at) {
            $whatsapp->sendEventReminder($this->ticket);
            $this->ticket->update(['reminder_sent_at' => now()]);
        }
    }
}
