<?php

namespace App\Jobs;

use App\Mail\TicketPendingMail;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPendingTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket
    ) {}

    public function handle(): void
    {
        if (!empty($this->ticket->buyer_email)) {
            Mail::to($this->ticket->buyer_email)->send(new TicketPendingMail($this->ticket));
        }

        // Se houvesse integração com API de WhatsApp (Ex: Twilio, Wati), colocaríamos aqui:
        // Se a pessoa colocou telefone/whatsapp
        // if (!empty($this->ticket->buyer_phone)) {
        //    WhatsAppService::sendMessage($this->ticket->buyer_phone, "Olá! O seu bilhete está pendente...");
        // }
    }
}
