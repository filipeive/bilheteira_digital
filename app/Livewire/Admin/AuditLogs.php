<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogs extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterAction = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterAction(): void { $this->resetPage(); }

    public function render()
    {
        $logs = AuditLog::with('user')
            ->when($this->search, fn($q) => $q->whereHas('user', fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
            ))
            ->when($this->filterAction, fn($q) => $q->where('action', $this->filterAction))
            ->latest()
            ->paginate(25);

        $actions = AuditLog::distinct()->pluck('action')->sort();

        return view('livewire.admin.audit-logs', compact('logs', 'actions'))
            ->layout('layouts.admin', ['title' => 'Auditoria']);
    }
}
