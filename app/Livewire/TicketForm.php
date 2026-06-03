<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Ticket;
use App\Services\TicketService;
use Livewire\Component;

class TicketForm extends Component
{
    public Event $event;

    public string $buyer_name = '';
    public string $buyer_phone = '';
    public string $buyer_email = '';
    public string $ticket_type = 'promotional';
    public int $quantity = 1;
    public string $payment_ref = '';
    public string $payment_method = 'mpesa';

    public bool $isSubmitting = false;
    public bool $showSuccess = false;
    public ?array $createdTickets = null;

    protected function rules(): array
    {
        return [
            'buyer_name' => 'required|string|min:3|max:100',
            'buyer_phone' => ['required', 'string', 'min:9', 'max:20'],
            'buyer_email' => 'nullable|email|max:100',
            'ticket_type' => 'required|in:promotional,second_lot,gate,vip,vip_promotional,vip_second_lot',
            'quantity' => 'required|integer|min:1|max:10',
            'payment_ref' => 'required|string|min:3|max:50',
            'payment_method' => 'required|in:mpesa,emola,cash',
        ];
    }

    protected array $messages = [
        'buyer_name.required' => 'O nome é obrigatório.',
        'buyer_name.min' => 'O nome deve ter pelo menos 3 caracteres.',
        'buyer_phone.required' => 'O telemóvel é obrigatório.',
        'payment_ref.required' => 'A referência de pagamento é obrigatória.',
        'quantity.max' => 'Máximo de 10 bilhetes por compra.',
    ];

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function updatedTicketType(): void
    {
        $this->resetValidation('ticket_type');
    }

    public function selectTicket(string $type): void
    {
        $this->ticket_type = $type;
    }

    public function getPrice(): int
    {
        $types = $this->event->getTicketTypePrices();
        return $types[$this->ticket_type]['price'] ?? 0;
    }

    public function getTotalProperty(): int
    {
        return $this->getPrice() * $this->quantity;
    }

    public function submit(): void
    {
        $this->validate();

        $this->isSubmitting = true;

        $ticketService = app(TicketService::class);

        $phone = $this->normalizePhone($this->buyer_phone);

        $data = [
            'event_id' => $this->event->id,
            'buyer_name' => $this->buyer_name,
            'buyer_phone' => $phone,
            'buyer_email' => $this->buyer_email ?: null,
            'ticket_type' => $this->ticket_type,
            'price' => $this->getPrice(),
            'payment_ref' => $this->payment_ref,
            'payment_method' => $this->payment_method,
            'status' => 'pending',
        ];

        $tickets = $ticketService->createBulkTickets($data, $this->quantity);

        $this->createdTickets = collect($tickets)->map(function(Ticket $t) {
            \App\Jobs\SendPendingTicketJob::dispatch($t);
            return [
                'id' => $t->id,
                'code' => $t->ticket_code,
                'qr_payload' => $t->qr_payload,
                'type' => $t->getTicketTypeLabel(),
                'name' => $t->buyer_name,
            ];
        })->toArray();

        $this->showSuccess = true;
        $this->isSubmitting = false;

        $this->dispatch('tickets-created', tickets: $this->createdTickets);
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
        $this->reset(['buyer_name', 'buyer_phone', 'buyer_email', 'ticket_type', 'quantity', 'payment_ref', 'payment_method']);
        $this->showSuccess = false;
        $this->createdTickets = null;
        $this->ticket_type = 'promotional';
        $this->quantity = 1;
    }

    public function resendTicket(): void
    {
        if (!$this->createdTickets) {
            $this->dispatch('notify', type: 'error', message: 'Nenhum bilhete criado para reenviar.');
            return;
        }

        $ticket = Ticket::find($this->createdTickets[0]['id']);

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
        return view('livewire.ticket-form');
    }
}
