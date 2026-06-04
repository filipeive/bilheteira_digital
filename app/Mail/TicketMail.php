<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $tickets,
        public array $pdfPaths
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: count($this->tickets) > 1 ? 'Os seus bilhetes: Concerto Renúncia' : 'O seu bilhete: Concerto Renúncia',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket',
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        foreach ($this->pdfPaths as $path) {
            $attachments[] = Attachment::fromPath($path)
                ->as(basename($path))
                ->withMime('application/pdf');
        }
        return $attachments;
    }
}
