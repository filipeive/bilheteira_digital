<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketBatch;
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

    // Bulk editing options
    public ?string $bulkBatchId = null;
    public string $bulkStatus = '';

    // Editing state
    public bool $isEditing = false;
    public ?string $editingTicketId = null;
    public string $editingName = '';
    public string $editingPhone = '';
    public string $editingEmail = '';
    public string $editingStatus = '';
    public ?string $editingBatchId = null;

    public function updatedSearch(): void
    {
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->selectedIds = [];
        $this->selectAll = false;
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
        $this->selectAll = !$this->selectAll;
        if ($this->selectAll) {
            $this->selectedIds = $this->tickets->pluck('id')->map(fn($id) => (string)$id)->toArray();
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

    public function bulkEdit(): void
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('notify', type: 'error', message: 'Nenhum bilhete selecionado.');
            return;
        }

        if (!$this->bulkBatchId && !$this->bulkStatus) {
            $this->dispatch('notify', type: 'error', message: 'Por favor, selecione um lote ou estado para alterar.');
            return;
        }

        $tickets = Ticket::whereIn('id', $this->selectedIds)->get();
        $count = 0;

        foreach ($tickets as $ticket) {
            $oldValues = $ticket->only(['batch_id', 'ticket_type', 'price', 'status']);
            $updateData = [];

            // Change Batch
            if ($this->bulkBatchId) {
                $newBatch = TicketBatch::find($this->bulkBatchId);
                if ($newBatch && $ticket->batch_id != $newBatch->id) {
                    // Decrement old batch
                    if ($ticket->batch_id) {
                        $oldBatch = TicketBatch::find($ticket->batch_id);
                        if ($oldBatch) {
                            $oldBatch->decrement('sold');
                        }
                    }
                    // Increment new batch
                    $newBatch->increment('sold');

                    $updateData['batch_id'] = $newBatch->id;
                    $updateData['ticket_type'] = $newBatch->ticket_type;
                    $updateData['price'] = $newBatch->price;
                }
            }

            // Change Status
            if ($this->bulkStatus) {
                $updateData['status'] = $this->bulkStatus;

                if ($ticket->status === 'used' && $this->bulkStatus !== 'used') {
                    $updateData['used_at'] = null;
                    $updateData['scanned_by'] = null;
                    $updateData['scanned_device'] = null;
                }

                if ($ticket->status !== 'used' && $this->bulkStatus === 'used') {
                    $updateData['used_at'] = now();
                    $updateData['scanned_by'] = auth()->id();
                }
            }

            if (!empty($updateData)) {
                $ticket->update($updateData);
                $newValues = $ticket->fresh()->only(['batch_id', 'ticket_type', 'price', 'status']);

                \App\Services\AuditService::log(
                    action: 'ticket_bulk_updated',
                    model: $ticket,
                    oldValues: $oldValues,
                    newValues: $newValues
                );

                $count++;
            }
        }

        $this->selectedIds = [];
        $this->selectAll = false;
        $this->bulkBatchId = null;
        $this->bulkStatus = '';

        $this->dispatch('notify', type: 'success', message: "Edição em massa concluída para {$count} bilhete(s).");
    }

    public function migrateExpiredBatch(): void
    {
        $event = Event::where('is_active', true)->first();
        if (!$event) {
            $this->dispatch('notify', type: 'error', message: 'Nenhum evento ativo encontrado.');
            return;
        }

        $expiredBatch = TicketBatch::where('event_id', $event->id)
            ->where('is_active', true)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->orderBy('sort_order')
            ->first();

        if (!$expiredBatch) {
            $this->dispatch('notify', type: 'info', message: 'Nenhum lote expirado encontrado.');
            return;
        }

        $nextBatch = TicketBatch::where('event_id', $event->id)
            ->where('is_active', true)
            ->where('ticket_type', $expiredBatch->ticket_type)
            ->where('id', '!=', $expiredBatch->id)
            ->where(function ($q) use ($expiredBatch) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->orderBy('sort_order')
            ->first();

        if (!$nextBatch) {
            $this->dispatch('notify', type: 'error', message: "Não há lote seguinte disponível para o tipo '{$expiredBatch->ticket_type}'.");
            return;
        }

        $tickets = Ticket::where('batch_id', $expiredBatch->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        if ($tickets->isEmpty()) {
            $this->dispatch('notify', type: 'info', message: 'Nenhum bilhete pendente ou confirmado para migrar neste lote.');
            return;
        }

        $count = 0;
        foreach ($tickets as $ticket) {
            $oldValues = $ticket->only(['batch_id', 'ticket_type', 'price', 'status']);
            $updateData = [
                'batch_id' => $nextBatch->id,
                'ticket_type' => $nextBatch->ticket_type,
                'price' => $nextBatch->price,
            ];

            $expiredBatch->decrement('sold');
            $nextBatch->increment('sold');

            $ticket->update($updateData);
            $newValues = $ticket->fresh()->only(['batch_id', 'ticket_type', 'price', 'status']);

            \App\Services\AuditService::log(
                action: 'ticket_batch_migrated',
                model: $ticket,
                oldValues: $oldValues,
                newValues: $newValues
            );

            $count++;
        }

        $this->dispatch('notify', type: 'success', message: "Migração concluída: {$count} bilhete(s) movidos de '{$expiredBatch->name}' para '{$nextBatch->name}'.");
    }

    #[Computed]
    public function batches()
    {
        $event = Event::where('is_active', true)->first();
        if (!$event) return collect();
        return TicketBatch::where('event_id', $event->id)->orderBy('sort_order')->get();
    }

    public function getBulkDownloadUrlProperty(): string
    {
        if (empty($this->selectedIds)) return '#';
        return route('admin.tickets.bulk_download', ['ids' => implode(',', $this->selectedIds)]);
    }

    public function confirmTicket(string $ticketId): void
    {
        $ticket = Ticket::findOrFail($ticketId);
        $oldValues = $ticket->only(['status']);
        app(TicketService::class)->confirmTicket($ticket);
        $newValues = $ticket->fresh()->only(['status']);

        \App\Services\AuditService::log(
            action: 'ticket_confirmed',
            model: $ticket,
            oldValues: $oldValues,
            newValues: $newValues
        );

        $this->dispatch('notify', type: 'success', message: "Bilhete {$ticket->ticket_code} confirmado.");
    }

    public function cancelTicket(string $ticketId): void
    {
        $ticket = Ticket::findOrFail($ticketId);
        $oldValues = $ticket->only(['status']);
        app(TicketService::class)->cancelTicket($ticket);
        $newValues = $ticket->fresh()->only(['status']);

        \App\Services\AuditService::log(
            action: 'ticket_cancelled',
            model: $ticket,
            oldValues: $oldValues,
            newValues: $newValues
        );

        $this->dispatch('notify', type: 'warning', message: "Bilhete {$ticket->ticket_code} cancelado.");
    }

    public function validateTicket(string $ticketId): void
    {
        $ticket = Ticket::findOrFail($ticketId);
        $oldValues = $ticket->only(['status', 'used_at', 'scanned_by']);
        $result = app(TicketService::class)->validateTicket($ticket->ticket_code, auth()->user());
        
        if ($result['status'] === 'valid') {
            $newValues = $ticket->fresh()->only(['status', 'used_at', 'scanned_by']);
            \App\Services\AuditService::log(
                action: 'ticket_validated',
                model: $ticket,
                oldValues: $oldValues,
                newValues: $newValues
            );
            $this->dispatch('notify', type: 'success', message: "Bilhete {$ticket->ticket_code} validado com sucesso.");
        } else {
            $this->dispatch('notify', type: 'error', message: $result['message']);
        }
    }

    public function deleteTicket(string $ticketId): void
    {
        $ticket = Ticket::findOrFail($ticketId);
        $oldValues = $ticket->toArray();
        
        \App\Services\AuditService::log(
            action: 'ticket_deleted',
            model: $ticket,
            oldValues: $oldValues,
            newValues: []
        );

        $ticket->delete();
        
        $this->dispatch('notify', type: 'warning', message: "Bilhete {$ticket->ticket_code} eliminado permanentemente.");
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
        $this->editingBatchId = (string) $ticket->batch_id;
        $this->isEditing = true;
    }

    public function saveTicket(): void
    {
        $this->validate([
            'editingName' => 'required|string|min:3',
            'editingPhone' => 'nullable|string',
            'editingEmail' => 'nullable|email',
            'editingStatus' => 'required|in:pending,confirmed,used,cancelled',
            'editingBatchId' => 'nullable|exists:ticket_batches,id',
        ]);

        $ticket = Ticket::findOrFail($this->editingTicketId);
        $oldValues = $ticket->only(['buyer_name', 'buyer_phone', 'buyer_email', 'status', 'used_at', 'scanned_by', 'batch_id', 'ticket_type', 'price']);
        
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

        // Change Batch atomically
        if ($this->editingBatchId && $ticket->batch_id != $this->editingBatchId) {
            $oldBatch = $ticket->batch;
            $newBatch = TicketBatch::find($this->editingBatchId);

            if ($newBatch && $oldBatch && $oldBatch->id !== $newBatch->id) {
                $oldBatch->decrement('sold');
                $newBatch->increment('sold');

                $updateData['batch_id'] = $newBatch->id;
                $updateData['ticket_type'] = $newBatch->ticket_type;
                $updateData['price'] = $newBatch->price;
            }
        }

        $ticket->update($updateData);
        $newValues = $ticket->fresh()->only(['buyer_name', 'buyer_phone', 'buyer_email', 'status', 'used_at', 'scanned_by', 'batch_id', 'ticket_type', 'price']);

        \App\Services\AuditService::log(
            action: 'ticket_updated',
            model: $ticket,
            oldValues: $oldValues,
            newValues: $newValues
        );

        $this->isEditing = false;
        $this->editingTicketId = null;
        $this->editingBatchId = null;
        
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
