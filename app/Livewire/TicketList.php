<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Ticket;
use App\Services\TicketService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TicketList extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filterStatus = '';

    #[Url]
    public string $filterType = '';

    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterType(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function confirmTicket(string $ticketId): void
    {
        $ticket = Ticket::findOrFail($ticketId);
        app(TicketService::class)->confirmTicket($ticket);
        $this->dispatch('notify', type: 'success', message: "Bilhete {$ticket->ticket_code} confirmado.");
    }

    public function cancelTicket(string $ticketId): void
    {
        $ticket = Ticket::findOrFail($ticketId);
        app(TicketService::class)->cancelTicket($ticket);
        $this->dispatch('notify', type: 'warning', message: "Bilhete {$ticket->ticket_code} cancelado.");
    }

    public function validateTicket(string $ticketId): void
    {
        $ticket = Ticket::findOrFail($ticketId);
        $result = app(TicketService::class)->validateTicket($ticket->ticket_code, auth()->user());
        
        if ($result['status'] === 'valid') {
            $this->dispatch('notify', type: 'success', message: "Bilhete {$ticket->ticket_code} validado com sucesso.");
        } else {
            $this->dispatch('notify', type: 'error', message: $result['message']);
        }
    }

    public function resendTicket(string $ticketId): void
    {
        $ticket = Ticket::findOrFail($ticketId);

        if (!$ticket->buyer_email && !$ticket->buyer_phone) {
            $this->dispatch('notify', type: 'error', message: "Bilhete não tem email nem telefone para envio.");
            return;
        }

        \App\Jobs\SendTicketJob::dispatch($ticket);
        $this->dispatch('notify', type: 'success', message: "Bilhete {$ticket->ticket_code} está a ser reenviado...");
    }

    #[Computed]
    public function tickets()
    {
        $event = Event::where('is_active', true)->first();

        if (!$event) {
            return Ticket::query()->paginate(20);
        }

        $query = Ticket::with('scanner')->where('event_id', $event->id);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('buyer_name', 'like', "%{$this->search}%")
                    ->orWhere('buyer_phone', 'like', "%{$this->search}%")
                    ->orWhere('ticket_code', 'like', "%{$this->search}%")
                    ->orWhere('buyer_email', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterType) {
            $query->where('ticket_type', $this->filterType);
        }

        return $query->orderBy($this->sortBy, $this->sortDirection)->paginate(20);
    }

    public function render()
    {
        return view('livewire.ticket-list')
            ->layout('layouts.admin', ['title' => 'Bilhetes']);
    }
}
