<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketPendingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $tickets
    ) {}

    public function envelope(): Envelope
    {
        $eventName = $this->tickets[0]->event->name ?? 'Alpha Bilheteira';
        return new Envelope(
            subject: count($this->tickets) > 1 ? "A aguardar confirmação dos bilhetes: {$eventName}" : "A aguardar confirmação: {$eventName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-pending',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
