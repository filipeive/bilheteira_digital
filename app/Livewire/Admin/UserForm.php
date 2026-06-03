<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;

class UserForm extends Component
{
    use WithFileUploads;

    public ?User $user = null;
    public string $name     = '';
    public string $email    = '';
    public string $phone    = '';
    public string $role     = 'operator';
    public string $password = '';
    public bool   $isEdit   = false;
    public $avatar = null;

    protected function rules(): array
    {
        $emailRule = $this->isEdit
            ? "required|email|unique:users,email,{$this->user?->id}"
            : 'required|email|unique:users,email';

        return [
            'name'     => 'required|min:3|max:100',
            'email'    => $emailRule,
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:' . implode(',', array_keys(User::ROLES)),
            'password' => $this->isEdit ? 'nullable|min:8' : 'required|min:8',
            'avatar'   => 'nullable|image|max:2048',
        ];
    }

    public function mount(?User $user = null): void
    {
        if ($user?->exists) {
            $this->isEdit = true;
            $this->user   = $user;
            $this->name   = $user->name;
            $this->email  = $user->email;
            $this->phone  = $user->phone ?? '';
            $this->role   = $user->role;
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'role'  => $this->role,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->avatar) {
            $data['avatar'] = $this->avatar->store('avatars', 'public');
        }

        if ($this->isEdit) {
            $old = $this->user->only(['name', 'email', 'phone', 'role']);
            $this->user->update($data);
            AuditService::log('updated_user', $this->user, $old, $data);
            $this->dispatch('notify', type: 'success', message: 'Utilizador actualizado.');
        } else {
            $data['is_active'] = true;
            $user = User::create($data);
            AuditService::log('created_user', $user, [], $data);
            $this->dispatch('notify', type: 'success', message: 'Utilizador criado com sucesso.');
            $this->reset(['name', 'email', 'phone', 'password', 'avatar']);
        }
    }

    public function render()
    {
        return view('livewire.admin.user-form', ['roles' => User::ROLES])
            ->layout('layouts.admin', ['title' => $this->isEdit ? 'Editar Utilizador' : 'Novo Utilizador']);
    }
}
