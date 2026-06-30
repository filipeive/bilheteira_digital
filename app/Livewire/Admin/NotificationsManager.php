<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\Ticket;
use App\Jobs\SendTicketJob;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationsManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterAction = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterAction(): void
    {
        $this->resetPage();
    }

    public function resendNotification(int $logId, string $channel): void
    {
        $log = AuditLog::find($logId);
        if (!$log) {
            $this->dispatch('notify', type: 'error', message: 'Registo de notificação não encontrado.');
            return;
        }

        $ticketId = $log->model_id;
        $ticket = Ticket::find($ticketId);

        if (!$ticket) {
            // Tentativa de obter do new_values/old_values se o bilhete foi deletado
            $this->dispatch('notify', type: 'error', message: 'Bilhete associado não existe mais no sistema.');
            return;
        }

        // Despacha o Job para reenvio
        SendTicketJob::dispatch($ticket, $channel);

        $this->dispatch('notify', type: 'success', message: 'Reenvio de notificação (canal: ' . strtoupper($channel) . ') adicionado à fila!');
    }

    public function render()
    {
        $logs = AuditLog::whereIn('action', ['sent_ticket_notification', 'sent_bulk_ticket_notification', 'send_ticket_failed'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('new_values', 'like', "%{$this->search}%")
                        ->orWhere('old_values', 'like', "%{$this->search}%")
                        ->orWhere('model_id', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterAction, fn($q) => $q->where('action', $this->filterAction))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.notifications-manager', compact('logs'))
            ->layout('layouts.admin', ['title' => 'Gestão de Notificações']);
    }
}
