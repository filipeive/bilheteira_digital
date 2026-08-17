<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class CustomerTicketController extends Controller
{
    /**
     * Formulário de pesquisa de bilhetes para verificação.
     */
    public function verifyForm(Request $request)
    {
        if ($request->has('code') && filled($request->query('code'))) {
            return redirect()->route('tickets.verify', ['code' => trim($request->query('code'))]);
        }

        return view('public.tickets.verify-form');
    }

    /**
     * Verificar bilhete por código (público).
     */
    public function verify(string $code)
    {
        $ticket = Ticket::with('event')
                        ->where('ticket_code', strtoupper(trim($code)))
                        ->first();

        return view('public.tickets.verify', compact('ticket', 'code'));
    }

    /**
     * Formulário "os meus bilhetes" — pesquisa por telefone ou email.
     */
    public function myTickets()
    {
        return view('public.tickets.my-tickets');
    }

    /**
     * Pesquisar bilhetes por telefone ou email.
     */
    public function searchTickets(Request $request)
    {
        $request->validate([
            'phone_or_email' => 'required|string|min:4',
        ]);

        $query = trim($request->phone_or_email);
        $cleanPhone = preg_replace('/\D+/', '', $query);

        $tickets = Ticket::with('event')
            ->where(function ($q) use ($query, $cleanPhone) {
                $q->where('buyer_phone', 'like', "%{$query}%")
                  ->orWhere('buyer_email', 'like', "%{$query}%");

                if (!empty($cleanPhone)) {
                    $q->orWhere('buyer_phone', 'like', "%{$cleanPhone}%");
                }
            })
            ->whereIn('status', ['confirmed', 'used', 'pending'])
            ->orderByDesc('created_at')
            ->get();

        return view('public.tickets.my-tickets', compact('tickets', 'query'));
    }
}
