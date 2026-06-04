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
        return new Envelope(
            subject: count($this->tickets) > 1 ? 'A aguardar confirmação dos bilhetes: Concerto Renúncia' : 'A aguardar confirmação: Concerto Renúncia',
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
