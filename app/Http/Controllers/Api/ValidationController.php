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
