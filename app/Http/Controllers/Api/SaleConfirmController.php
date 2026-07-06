<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketBatch;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * SaleConfirmController
 *
 * Handles physical sale confirmation — transitions a ticket from 'pending'
 * to 'confirmed' when a sales agent scans/types the ticket code at a point of sale.
 *
 * This is separate from the entrance scanner (ValidationController) which
 * transitions 'confirmed' → 'used' at the event entrance.
 */
class SaleConfirmController extends Controller
{
    /**
     * Confirm the sale of a pending ticket.
     *
     * Possible response statuses:
     *   confirmed         — success: ticket moved from pending → confirmed
     *   already_confirmed — ticket was already confirmed (duplicate scan)
     *   already_used      — ticket already used at entrance
     *   cancelled         — ticket has been cancelled, cannot sell
     *   not_found         — no ticket with that code exists
     */
    public function confirm(Request $request): JsonResponse
    {
        $request->validate(['ticket_code' => 'required|string|max:255']);

        $code = $this->extractCode($request->input('ticket_code'));

        $ticket = Ticket::where('ticket_code', $code)->first();

        if (!$ticket) {
            return response()->json([
                'status'  => 'not_found',
                'message' => 'Bilhete não encontrado.',
                'ticket'  => null,
            ], 404);
        }

        $ticketData = [
            'code'        => $ticket->ticket_code,
            'buyer_name'  => $ticket->buyer_name,
            'ticket_type' => $ticket->getTicketTypeLabel(),
            'price'       => number_format($ticket->price, 0, ',', '.'),
            'status'      => $ticket->getStatusLabel(),
        ];

        // Already confirmed
        if ($ticket->status === 'confirmed') {
            return response()->json([
                'status'  => 'already_confirmed',
                'message' => 'Este bilhete já está confirmado.',
                'ticket'  => $ticketData,
            ], 200);
        }

        // Already used at the event
        if ($ticket->status === 'used') {
            return response()->json([
                'status'  => 'already_used',
                'message' => 'Bilhete já foi utilizado no evento.',
                'ticket'  => $ticketData,
            ], 409);
        }

        // Cancelled — cannot sell
        if ($ticket->status === 'cancelled') {
            return response()->json([
                'status'  => 'cancelled',
                'message' => 'Este bilhete foi cancelado.',
                'ticket'  => $ticketData,
            ], 403);
        }

        // ── Confirm the sale (pending → confirmed) ────────────────────
        $oldStatus = $ticket->status;
        $ticket->update(['status' => 'confirmed']);

        // Increment the batch sold counter now that it's a real sale
        if ($ticket->batch_id) {
            TicketBatch::where('id', $ticket->batch_id)->increment('sold');
        }

        // Audit log
        AuditService::log(
            action: 'ticket_sale_confirmed',
            model: $ticket,
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => 'confirmed', 'confirmed_by' => $request->user()?->id]
        );

        return response()->json([
            'status'  => 'confirmed',
            'message' => 'Venda confirmada! Bilhete activado.',
            'ticket'  => array_merge($ticketData, ['status' => 'Confirmado']),
        ], 200);
    }

    /**
     * Display the sale scanner page.
     */
    public function index()
    {
        return view('admin.sale-scanner');
    }

    private function extractCode(string $value): string
    {
        $value = trim($value);

        if (str_contains($value, '|')) {
            return strtoupper(strtok($value, '|'));
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded) && isset($decoded['ticket_code'])) {
            return strtoupper((string) $decoded['ticket_code']);
        }

        return strtoupper($value);
    }
}
