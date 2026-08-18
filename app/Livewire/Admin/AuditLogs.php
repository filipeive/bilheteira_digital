<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogs extends Component
{
    use WithPagination;

    public ?int $eventId = null;
    public string $search = '';
    public string $filterAction = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterAction(): void { $this->resetPage(); }
    public function updatingEventId(): void { $this->resetPage(); }

    #[\Livewire\Attributes\Computed]
    public function events()
    {
        return \App\Models\Event::orderByDesc('is_active')->orderBy('name')->get();
    }

    /**
     * Restore a soft-deleted ticket from the audit log record.
     */
    public function restoreTicketFromLog(int $logId): void
    {
        $log = AuditLog::find($logId);
        if (!$log || $log->model_type !== 'App\\Models\\Ticket') {
            $this->dispatch('notify', type: 'error', message: 'Registo de auditoria inválido.');
            return;
        }

        $ticket = \App\Models\Ticket::onlyTrashed()->find($log->model_id);
        if (!$ticket) {
            $this->dispatch('notify', type: 'error', message: 'O bilhete já foi restaurado ou não existe na lixeira.');
            return;
        }

        // Log restoration in Audit
        \App\Services\AuditService::log(
            action: 'ticket_restored',
            model: $ticket,
            oldValues: [
                'ticket_code' => $ticket->ticket_code,
                'status'      => $ticket->status,
            ],
            newValues: ['restored_by' => auth()->id(), 'context' => 'audit_log']
        );

        $ticket->restore();

        $this->dispatch('notify', type: 'success', message: "Bilhete {$ticket->ticket_code} restaurado com sucesso.");
    }

    /**
     * Recreate/regenerate a ticket from a failed scan audit log record.
     */
    public function regenerateTicket(int $logId): void
    {
        $log = AuditLog::find($logId);
        if (!$log || $log->action !== 'ticket_scan_failed') {
            $this->dispatch('notify', type: 'error', message: 'Registo de auditoria inválido para regeneração.');
            return;
        }

        $code = $log->old_values['scanned_code'] ?? null;
        if (!$code) {
            $this->dispatch('notify', type: 'error', message: 'Nenhum código de bilhete encontrado neste log.');
            return;
        }

        // Check if ticket already exists
        if (\App\Models\Ticket::withTrashed()->where('ticket_code', $code)->exists()) {
            $this->dispatch('notify', type: 'error', message: 'Já existe um bilhete activo ou na lixeira com este código.');
            return;
        }

        // Find event
        $eventId = $log->old_values['event_id'] ?? $this->eventId;
        $event = $eventId ? \App\Models\Event::find($eventId) : (\App\Models\Event::where('is_active', true)->first() ?: \App\Models\Event::first());
        if (!$event) {
            $this->dispatch('notify', type: 'error', message: 'Nenhum evento encontrado para associar o bilhete.');
            return;
        }

        // Recreate the ticket
        $ticket = \App\Models\Ticket::create([
            'event_id' => $event->id,
            'ticket_code' => $code,
            'buyer_name' => 'Recuperado (Portaria)',
            'buyer_phone' => 'N/A',
            'ticket_type' => 'gate',
            'price' => 0,
            'status' => 'confirmed',
            'notes' => 'Gerado a partir do log de falha de leitura #' . $logId,
        ]);

        // Generate signed QR payload
        $qrCodeService = app(\App\Services\QrCodeService::class);
        $ticket->qr_payload = $qrCodeService->generateSignedPayload($ticket);
        $ticket->save();

        // Log the regeneration
        \App\Services\AuditService::log(
            action: 'ticket_created',
            model: $ticket,
            oldValues: [],
            newValues: ['regenerated_from_fail_log' => $logId, 'code' => $code]
        );

        $this->dispatch('notify', type: 'success', message: "Bilhete {$code} regenerado para o evento {$event->name} com sucesso!");
    }

    public function render()
    {
        $logs = AuditLog::with('user')
            ->when($this->search, function($q) {
                $q->where(function($sub) {
                    $sub->whereHas('user', fn($userQuery) =>
                        $userQuery->where('name', 'like', "%{$this->search}%")
                    )
                    ->orWhere('action', 'like', "%{$this->search}%")
                    ->orWhere('old_values', 'like', "%{$this->search}%")
                    ->orWhere('new_values', 'like', "%{$this->search}%");
                });
            })
            ->when(!$this->filterAction && !$this->search, function($q) {
                $q->whereNotIn('action', ['ticket_deleted', 'ticket_cancelled', 'ticket_scan_failed']);
            })
            ->when($this->filterAction, fn($q) => $q->where('action', $this->filterAction))
            ->latest()
            ->paginate(25);

        $actions = AuditLog::distinct()->pluck('action')->sort();

        return view('livewire.admin.audit-logs', compact('logs', 'actions'))
            ->layout('layouts.admin', ['title' => 'Auditoria']);
    }
}
