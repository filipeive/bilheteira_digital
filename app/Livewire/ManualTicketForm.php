<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Ticket;
use App\Services\TicketService;
use Livewire\Component;

class ManualTicketForm extends Component
{
    public string $buyer_name = '';
    public string $buyer_phone = '';
    public string $buyer_email = '';
    public string $ticket_type = 'gate';
    public string $payment_method = 'cash';
    public string $payment_ref = '';
    public int $quantity = 1;
    public string $notes = '';

    public bool $isSubmitting = false;
    public bool $showSuccess = false;
    public string $lastTicketCode = '';
    public string $lastTicketId = '';

    protected function rules(): array
    {
        return [
            'buyer_name' => 'required|string|min:3|max:100',
            'buyer_phone' => ['required', 'string', 'min:9', 'max:20'],
            'buyer_email' => 'nullable|email',
            'ticket_type' => 'required|in:promotional,second_lot,gate,vip,free,vip_promotional,vip_second_lot',
            'payment_method' => 'required|in:mpesa,emola,cash,free',
            'quantity' => 'required|integer|min:1|max:20',
        ];
    }

    public function submit(): void
    {
        $this->validate();
        $this->isSubmitting = true;

        $event = Event::where('is_active', true)->first();
        $ticketService = app(TicketService::class);

        $phone = $this->normalizePhone($this->buyer_phone);

        $price = $this->ticket_type === 'free' ? 0 : $ticketService->getPrice($event, $this->ticket_type);

        $data = [
            'event_id' => $event->id,
            'buyer_name' => $this->buyer_name,
            'buyer_phone' => $phone,
            'buyer_email' => $this->buyer_email ?: null,
            'ticket_type' => $this->ticket_type,
            'price' => $price,
            'payment_ref' => $this->ticket_type === 'free' ? 'CORTESIA' : ($this->payment_ref ?: 'PRESENCIAL'),
            'payment_method' => $this->ticket_type === 'free' ? 'free' : $this->payment_method,
            'status' => 'confirmed',
            'notes' => $this->notes ?: 'Venda presencial',
        ];

        $tickets = $ticketService->createBulkTickets($data, $this->quantity);

        $this->lastTicketCode = $tickets[0]->ticket_code;
        $this->lastTicketId = $tickets[0]->id;
        $this->showSuccess = true;
        $this->isSubmitting = false;

        $this->dispatch('notify', type: 'success', message: "{$this->quantity} bilhete(s) criado(s) com sucesso.");
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (!str_starts_with($digits, '258') && strlen($digits) === 9) {
            $digits = '258' . $digits;
        }

        return $digits;
    }

    public function resetForm(): void
    {
        $this->reset(['buyer_name', 'buyer_phone', 'buyer_email', 'ticket_type', 'payment_method', 'payment_ref', 'quantity', 'notes']);
        $this->showSuccess = false;
        $this->ticket_type = 'gate';
        $this->payment_method = 'cash';
        $this->quantity = 1;
    }

    public function resendTicket(): void
    {
        $ticket = Ticket::find($this->lastTicketId);

        if (!$ticket) {
            $this->dispatch('notify', type: 'error', message: 'Bilhete não encontrado.');
            return;
        }

        if (!$ticket->buyer_email && !$ticket->buyer_phone) {
            $this->dispatch('notify', type: 'error', message: 'Bilhete não tem email nem telefone para envio.');
            return;
        }

        \App\Jobs\SendTicketJob::dispatch($ticket);
        $this->dispatch('notify', type: 'success', message: "Bilhete {$ticket->ticket_code} está a ser reenviado...");
    }

    public function render()
    {
        return view('livewire.manual-ticket-form')
            ->layout('layouts.admin', ['title' => 'Venda Manual']);
    }
}
