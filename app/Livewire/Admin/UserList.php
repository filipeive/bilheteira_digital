<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\AuditService;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = '';
    public string $filterStatus = '';

    protected $queryString = ['search', 'filterRole', 'filterStatus'];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterRole(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function toggleActive(int $userId): void
    {
        $user = User::findOrFail($userId);
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', type: 'error', message: 'Não pode desactivar a sua própria conta.');
            return;
        }
        $old = ['is_active' => $user->is_active];
        $user->update(['is_active' => !$user->is_active]);
        AuditService::log('toggled_user_status', $user, $old, ['is_active' => $user->is_active]);

        $status = $user->is_active ? 'activado' : 'desactivado';
        $this->dispatch('notify', type: 'success', message: "Utilizador {$status}.");
    }

    public function changeRole(int $userId, string $role): void
    {
        $user = User::findOrFail($userId);
        if (!in_array($role, array_keys(User::ROLES))) {
            $this->dispatch('notify', type: 'error', message: 'Perfil inválido.');
            return;
        }
        $old = ['role' => $user->role];
        $user->update(['role' => $role]);
        AuditService::log('changed_user_role', $user, $old, ['role' => $role]);
        $this->dispatch('notify', type: 'success', message: 'Perfil actualizado.');
    }

    public function deleteUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', type: 'error', message: 'Não pode eliminar a sua própria conta.');
            return;
        }
        AuditService::log('deleted_user', $user);
        $user->delete();
        $this->dispatch('notify', type: 'success', message: 'Utilizador eliminado.');
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->filterRole, fn($q) => $q->where('role', $this->filterRole))
            ->when($this->filterStatus !== '', fn($q) => $q->where('is_active', $this->filterStatus === 'active'))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.user-list', [
            'users' => $users,
            'roles' => User::ROLES,
        ])->layout('layouts.admin', ['title' => 'Utilizadores']);
    }
}
