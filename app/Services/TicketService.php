<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Str;

class TicketService
{
    public function __construct(
        private QrCodeService $qrCodeService
    ) {}

    /**
     * Create a new ticket.
     */
    public function createTicket(array $data): Ticket
    {
        $ticket = new Ticket([
            'event_id' => $data['event_id'],
            'buyer_name' => $data['buyer_name'],
            'buyer_phone' => $data['buyer_phone'],
            'buyer_email' => $data['buyer_email'] ?? null,
            'ticket_type' => $data['ticket_type'],
            'price' => $data['price'],
            'payment_ref' => $data['payment_ref'] ?? null,
            'payment_method' => $data['payment_method'] ?? 'mpesa',
            'status' => $data['status'] ?? 'pending',
            'notes' => $data['notes'] ?? null,
        ]);

        $ticket->id = (string) Str::uuid();
        $ticket->ticket_code = Ticket::generateCode();
        $ticket->qr_payload = $this->qrCodeService->generateSignedPayload($ticket);

        $ticket->save();

        return $ticket;
    }

    /**
     * Create multiple tickets for a single purchase.
     *
     * @return Ticket[]
     */
    public function createBulkTickets(array $data, int $quantity): array
    {
        $tickets = [];

        for ($i = 0; $i < $quantity; $i++) {
            $tickets[] = $this->createTicket($data);
        }

        return $tickets;
    }

    /**
     * Confirm a pending ticket.
     */
    public function confirmTicket(Ticket $ticket): bool
    {
        if (!$ticket->isPending()) {
            return false;
        }

        $ticket->update(['status' => 'confirmed']);
        
        \App\Jobs\SendTicketJob::dispatch($ticket);
        
        return true;
    }

    /**
     * Cancel a ticket.
     */
    public function cancelTicket(Ticket $ticket): bool
    {
        if ($ticket->isUsed()) {
            return false;
        }

        $ticket->update(['status' => 'cancelled']);
        return true;
    }

    /**
     * Validate and process a scanned ticket code.
     *
     * @return array{status: string, message: string, ticket: Ticket|null}
     */
    public function validateTicket(string $ticketCode, User $scanner): array
    {
        $ticket = Ticket::where('ticket_code', $ticketCode)->first();

        if (!$ticket) {
            return [
                'status' => 'invalid',
                'message' => 'Bilhete não encontrado.',
                'ticket' => null,
            ];
        }

        if ($ticket->isUsed()) {
            return [
                'status' => 'already_used',
                'message' => 'Bilhete já foi utilizado em ' . $ticket->used_at->format('d/m/Y H:i'),
                'ticket' => $ticket,
            ];
        }

        if ($ticket->isCancelled()) {
            return [
                'status' => 'cancelled',
                'message' => 'Este bilhete foi cancelado.',
                'ticket' => $ticket,
            ];
        }

        if ($ticket->isPending()) {
            return [
                'status' => 'not_confirmed',
                'message' => 'Pagamento ainda não confirmado.',
                'ticket' => $ticket,
            ];
        }

        // Mark as used
        $ticket->markAsUsed($scanner);

        return [
            'status' => 'valid',
            'message' => 'ENTRADA AUTORIZADA',
            'ticket' => $ticket->fresh(),
        ];
    }

    /**
     * Get price for a ticket type from the event.
     */
    public function getPrice(Event $event, string $ticketType): int
    {
        $types = $event->getTicketTypePrices();
        return $types[$ticketType]['price'] ?? 0;
    }

    /**
     * Get dashboard stats for an event.
     */
    public function getEventStats(Event $event): array
    {
        $tickets = $event->tickets();

        return [
            'total' => $tickets->count(),
            'confirmed' => (clone $tickets)->where('status', 'confirmed')->count(),
            'pending' => (clone $tickets)->where('status', 'pending')->count(),
            'used' => (clone $tickets)->where('status', 'used')->count(),
            'cancelled' => (clone $tickets)->where('status', 'cancelled')->count(),
            'revenue' => (clone $tickets)->whereIn('status', ['confirmed', 'used'])->sum('price'),
            'by_type' => [
                'promotional' => (clone $tickets)->where('ticket_type', 'promotional')->whereIn('status', ['confirmed', 'used'])->count(),
                'second_lot' => (clone $tickets)->where('ticket_type', 'second_lot')->whereIn('status', ['confirmed', 'used'])->count(),
                'gate' => (clone $tickets)->where('ticket_type', 'gate')->whereIn('status', ['confirmed', 'used'])->count(),
                'vip' => (clone $tickets)->where('ticket_type', 'vip')->whereIn('status', ['confirmed', 'used'])->count(),
                'free' => (clone $tickets)->where('ticket_type', 'free')->whereIn('status', ['confirmed', 'used'])->count(),
            ],
        ];
    }
}
