<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
    public function __construct(
        private TicketService $ticketService
    ) {}

    /**
     * Validate a ticket by code.
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'ticket_code' => 'required|string|max:255',
        ]);

        $result = $this->ticketService->validateTicket(
            $this->extractTicketCode($request->input('ticket_code')),
            $request->user()
        );

        $statusCode = match ($result['status']) {
            'valid' => 200,
            'already_used' => 409,
            'deleted' => 403,
            'cancelled', 'not_confirmed' => 403,
            'invalid' => 404,
            default => 400,
        };

        $response = [
            'status' => $result['status'],
            'message' => $result['message'],
        ];

        if ($result['ticket']) {
            $response['ticket'] = [
                'code' => $result['ticket']->ticket_code,
                'buyer_name' => $result['ticket']->buyer_name,
                'ticket_type' => $result['ticket']->getTicketTypeLabel(),
                'status' => $result['ticket']->getStatusLabel(),
                'used_at' => $result['ticket']->used_at?->format('d/m/Y H:i'),
            ];
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Restore a deleted ticket and mark it as validated.
     */
    public function restoreAndValidate(Request $request): JsonResponse
    {
        $request->validate([
            'ticket_code' => 'required|string|max:255',
        ]);

        $ticketCode = $this->extractTicketCode($request->input('ticket_code'));
        $ticket = Ticket::onlyTrashed()->where('ticket_code', $ticketCode)->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Bilhete eliminado não encontrado.',
                'ticket' => null,
            ], 404);
        }

        // Restore the ticket
        $ticket->restore();

        // Log the restoration in Audit
        \App\Services\AuditService::log(
            action: 'ticket_restored',
            model: $ticket,
            oldValues: [
                'ticket_code' => $ticket->ticket_code,
                'status'      => $ticket->status,
            ],
            newValues: ['restored_by' => $request->user()->id, 'context' => 'scanner']
        );

        try {
            \App\Models\SystemNotification::add(
                title: 'Bilhete Eliminado Restaurado',
                message: "O bilhete eliminado {$ticket->ticket_code} foi recuperado e validado por {$request->user()->name} na portaria.",
                type: 'warning',
                link: '/admin/audit'
            );
        } catch (\Exception $e) {
            // Ignore
        }

        // Validate the ticket now
        $result = $this->ticketService->validateTicket($ticketCode, $request->user());

        $statusCode = match ($result['status']) {
            'valid' => 200,
            'already_used' => 409,
            'cancelled', 'not_confirmed' => 403,
            'invalid' => 404,
            default => 400,
        };

        $response = [
            'status' => $result['status'],
            'message' => 'Bilhete restaurado e validado com sucesso! ' . $result['message'],
        ];

        if ($result['ticket']) {
            $response['ticket'] = [
                'code' => $result['ticket']->ticket_code,
                'buyer_name' => $result['ticket']->buyer_name,
                'ticket_type' => $result['ticket']->getTicketTypeLabel(),
                'status' => $result['ticket']->getStatusLabel(),
                'used_at' => $result['ticket']->used_at?->format('d/m/Y H:i'),
            ];
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Request administration review for a ticket.
     */
    public function requestReview(Request $request): JsonResponse
    {
        $request->validate([
            'ticket_code' => 'required|string|max:255',
        ]);

        $ticketCode = $this->extractTicketCode($request->input('ticket_code'));
        $ticket = Ticket::withTrashed()->where('ticket_code', $ticketCode)->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bilhete não encontrado.',
            ], 404);
        }

        try {
            \App\Models\SystemNotification::add(
                title: 'Revisão Solicitada: ' . $ticketCode,
                message: "O operador " . ($request->user()->name ?? 'Porteiro') . " solicitou a ativação do bilhete de {$ticket->buyer_name} (Estado: " . $ticket->getStatusLabel() . ").",
                type: 'warning',
                link: '/admin/tickets?search=' . $ticketCode
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao enviar notificação para a administração.',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Solicitação de revisão enviada com sucesso ao painel!',
        ]);
    }

    /**
     * Get list of confirmed ticket codes for offline cache.
     */
    public function confirmedList(): JsonResponse
    {
        $codes = Ticket::where('status', 'confirmed')
            ->pluck('ticket_code')
            ->toArray();

        return response()->json([
            'codes' => $codes,
            'count' => count($codes),
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Scanner page.
     */
    public function index()
    {
        return view('validator.scanner');
    }

    private function extractTicketCode(string $value): string
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
