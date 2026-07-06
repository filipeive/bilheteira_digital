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

    #[Url]
    public string $filterMode = '';

    #[Url]
    public int $perPage = 20;

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

    public function updatedFilterMode(): void
    {
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function updatedPerPage(): void
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

    public function bulkDelete(): void
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('notify', type: 'error', message: 'Nenhum bilhete selecionado.');
            return;
        }

        // Only allow deleting cancelled tickets
        $tickets = Ticket::whereIn('id', $this->selectedIds)->where('status', 'cancelled')->get();
        $count = 0;

        foreach ($tickets as $ticket) {
            \App\Services\AuditService::log(
                action: 'ticket_deleted',
                model: null,
                oldValues: [
                    'ticket_code' => $ticket->ticket_code,
                    'buyer_name'  => $ticket->buyer_name,
                    'status'      => $ticket->status,
                    'price'       => $ticket->price,
                    'ticket_type' => $ticket->ticket_type,
                ],
                newValues: ['deleted_by' => auth()->id()]
            );
            $ticket->delete();
            $count++;
        }

        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'warning', message: "{$count} bilhete(s) cancelado(s) eliminado(s) com sucesso.");
    }

    public function bulkRestore(): void
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('notify', type: 'error', message: 'Nenhum bilhete selecionado.');
            return;
        }

        $tickets = Ticket::onlyTrashed()->whereIn('id', $this->selectedIds)->get();
        $count = 0;

        foreach ($tickets as $ticket) {
            \App\Services\AuditService::log(
                action: 'ticket_restored',
                model: $ticket,
                oldValues: [
                    'ticket_code' => $ticket->ticket_code,
                    'status'      => $ticket->status,
                ],
                newValues: ['restored_by' => auth()->id()]
            );
            $ticket->restore();
            $count++;
        }

        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'success', message: "{$count} bilhete(s) restaurado(s) com sucesso.");
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
                    // Only adjust sold counters if the ticket is actually "sold" (confirmed or used)
                    if (in_array($ticket->status, ['confirmed', 'used'])) {
                        if ($ticket->batch_id) {
                            $oldBatch = TicketBatch::find($ticket->batch_id);
                            if ($oldBatch) {
                                $oldBatch->decrement('sold');
                            }
                        }
                        $newBatch->increment('sold');
                    }

                    $updateData['batch_id']    = $newBatch->id;
                    $updateData['ticket_type'] = $newBatch->ticket_type;
                    $updateData['price']       = $newBatch->price;
                }
            }

            // Change Status
            if ($this->bulkStatus && $this->bulkStatus !== $ticket->status) {
                $newStatus = $this->bulkStatus;
                $oldStatus = $ticket->status;

                $updateData['status'] = $newStatus;

                // Un-use: clear scan fields
                if ($oldStatus === 'used' && $newStatus !== 'used') {
                    $updateData['used_at'] = null;
                    $updateData['scanned_by'] = null;
                    $updateData['scanned_device'] = null;
                }

                // Mark as used: set scan fields
                if ($oldStatus !== 'used' && $newStatus === 'used') {
                    $updateData['used_at'] = now();
                    $updateData['scanned_by'] = auth()->id();
                }

                // Batch sold counter: adjust only when moving between unconfirmed ↔ confirmed
                if ($ticket->batch_id) {
                    // pending/cancelled → confirmed  →  +1 sold
                    if (!in_array($oldStatus, ['confirmed', 'used']) && in_array($newStatus, ['confirmed', 'used'])) {
                        TicketBatch::where('id', $ticket->batch_id)->increment('sold');
                    }
                    // confirmed/used → pending/cancelled  →  -1 sold
                    if (in_array($oldStatus, ['confirmed', 'used']) && !in_array($newStatus, ['confirmed', 'used'])) {
                        TicketBatch::where('id', $ticket->batch_id)->decrement('sold');
                    }
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

        // Safety guard — only cancelled tickets may be permanently deleted
        if ($ticket->status !== 'cancelled') {
            $this->dispatch('notify', type: 'error', message: 'Apenas bilhetes cancelados podem ser eliminados.');
            return;
        }

        \App\Services\AuditService::log(
            action: 'ticket_deleted',
            model: null,
            oldValues: [
                'ticket_code' => $ticket->ticket_code,
                'buyer_name'  => $ticket->buyer_name,
                'status'      => $ticket->status,
                'price'       => $ticket->price,
                'ticket_type' => $ticket->ticket_type,
            ],
            newValues: ['deleted_by' => auth()->id()]
        );

        $code = $ticket->ticket_code;
        $ticket->delete();

        $this->dispatch('notify', type: 'warning', message: "Bilhete {$code} eliminado permanentemente.");
    }

    public function restoreTicket(string $ticketId): void
    {
        $ticket = Ticket::onlyTrashed()->findOrFail($ticketId);

        \App\Services\AuditService::log(
            action: 'ticket_restored',
            model: $ticket,
            oldValues: [
                'ticket_code' => $ticket->ticket_code,
                'status'      => $ticket->status,
            ],
            newValues: ['restored_by' => auth()->id()]
        );

        $ticket->restore();

        $this->dispatch('notify', type: 'success', message: "Bilhete {$ticket->ticket_code} restaurado com sucesso.");
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
    public function ticketTypes(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('ticket_types_list', 60, function () {
            $typesFromTickets = Ticket::distinct()->pluck('ticket_type')->filter()->map(fn($t) => strtolower($t))->toArray();
            $typesFromBatches = TicketBatch::distinct()->pluck('ticket_type')->filter()->map(fn($t) => strtolower($t))->toArray();
            
            $types = array_unique(array_merge($typesFromTickets, $typesFromBatches));
            
            $map = [
                'first_phase' => 'Primeiro Lote',
                'first_lot'   => 'Primeiro Lote',
                'second_phase' => 'Segundo Lote',
                'second_lot'  => 'Segundo Lote',
                'promotional' => 'Promocional',
                'vip'         => 'VIP',
                'vip_promotional' => 'VIP Promocional',
                'vip_second_lot'  => 'VIP 2º Lote',
                'gate'        => 'No Portão',
                'free'        => 'Gratuito',
                'child'       => 'Criança',
            ];

            $result = [];
            foreach ($types as $type) {
                $result[$type] = $map[$type] ?? ucwords(str_replace(['_', '-'], ' ', $type));
            }

            return $result;
        });
    }

    #[Computed]
    public function tickets()
    {
        $event = Event::where('is_active', true)->first();

        if (!$event) {
            return Ticket::query()->paginate(20);
        }

        if ($this->filterStatus === 'deleted') {
            $query = Ticket::onlyTrashed()->with('scanner')->where('event_id', $event->id);
        } else {
            $query = Ticket::with('scanner')->where('event_id', $event->id);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('buyer_name', 'like', "%{$this->search}%")
                    ->orWhere('buyer_phone', 'like', "%{$this->search}%")
                    ->orWhere('ticket_code', 'like', "%{$this->search}%")
                    ->orWhere('buyer_email', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus && $this->filterStatus !== 'deleted') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterType) {
            $query->where('ticket_type', $this->filterType);
        }

        if ($this->filterMode) {
            $query->where('ticket_mode', $this->filterMode);
        }

        return $query->orderBy($this->sortBy, $this->sortDirection)->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.ticket-list')
            ->layout('layouts.admin', ['title' => 'Bilhetes']);
    }
}
