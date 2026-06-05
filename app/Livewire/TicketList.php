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
    public string $viewMode = 'table';
    public array $selectedIds = [];
    public bool $selectAll = false;

    // Editing state
    public bool $isEditing = false;
    public ?string $editingTicketId = null;
    public string $editingName = '';
    public string $editingPhone = '';
    public string $editingEmail = '';
    public string $editingStatus = '';

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
        $this->selectedIds = [];
        $this->selectAll = false;
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

    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selectedIds = $this->tickets->pluck('id')->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function bulkConfirm(): void
    {
        $tickets = Ticket::whereIn('id', $this->selectedIds)->where('status', 'pending')->get();
        $ticketService = app(TicketService::class);
        $count = 0;
        foreach ($tickets as $ticket) {
            if ($ticketService->confirmTicket($ticket)) $count++;
        }
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'success', message: "{$count} bilhete(s) confirmado(s).");
    }

    public function bulkCancel(): void
    {
        $tickets = Ticket::whereIn('id', $this->selectedIds)->whereNotIn('status', ['used', 'cancelled'])->get();
        $ticketService = app(TicketService::class);
        $count = 0;
        foreach ($tickets as $ticket) {
            if ($ticketService->cancelTicket($ticket)) $count++;
        }
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'warning', message: "{$count} bilhete(s) cancelado(s).");
    }

    public function getBulkDownloadUrlProperty(): string
    {
        if (empty($this->selectedIds)) return '#';
        return route('admin.tickets.bulk_download', ['ids' => implode(',', $this->selectedIds)]);
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

    public function editTicket(string $id): void
    {
        $ticket = Ticket::findOrFail($id);
        $this->editingTicketId = $ticket->id;
        $this->editingName = $ticket->buyer_name;
        $this->editingPhone = $ticket->buyer_phone ?? '';
        $this->editingEmail = $ticket->buyer_email ?? '';
        $this->editingStatus = $ticket->status;
        $this->isEditing = true;
    }

    public function saveTicket(): void
    {
        $this->validate([
            'editingName' => 'required|string|min:3',
            'editingPhone' => 'nullable|string',
            'editingEmail' => 'nullable|email',
            'editingStatus' => 'required|in:pending,confirmed,used,cancelled',
        ]);

        $ticket = Ticket::findOrFail($this->editingTicketId);
        
        $updateData = [
            'buyer_name' => $this->editingName,
            'buyer_phone' => $this->editingPhone ?: null,
            'buyer_email' => $this->editingEmail ?: null,
            'status' => $this->editingStatus,
        ];

        // If status changed from used to confirmed/pending/cancelled, clear scanner info
        if ($ticket->status === 'used' && $this->editingStatus !== 'used') {
            $updateData['used_at'] = null;
            $updateData['scanned_by'] = null;
            $updateData['scanned_device'] = null;
        }
        
        // If status changed to used, mark scan info
        if ($ticket->status !== 'used' && $this->editingStatus === 'used') {
            $updateData['used_at'] = now();
            $updateData['scanned_by'] = auth()->id();
        }

        $ticket->update($updateData);

        $this->isEditing = false;
        $this->editingTicketId = null;
        
        $this->dispatch('notify', type: 'success', message: "Bilhete {$ticket->ticket_code} actualizado com sucesso.");
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
