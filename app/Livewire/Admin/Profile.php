<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public string $name     = '';
    public string $email    = '';
    public string $phone    = '';
    public string $current_password = '';
    public string $new_password     = '';
    public string $new_password_confirmation = '';
    public $avatar = null;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name  = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
    }

    public function saveProfile(): void
    {
        $this->validate([
            'name'   => 'required|min:3',
            'email'  => 'required|email|unique:users,email,' . auth()->id(),
            'phone'  => 'nullable|string|max:20',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $data = ['name' => $this->name, 'email' => $this->email, 'phone' => $this->phone ?: null];

        if ($this->avatar) {
            if (auth()->user()->avatar) {
                Storage::disk('public')->delete(auth()->user()->avatar);
            }
            $data['avatar'] = $this->avatar->store('avatars', 'public');
        }

        auth()->user()->update($data);
        $this->dispatch('notify', type: 'success', message: 'Perfil actualizado.');
    }

    public function changePassword(): void
    {
        $this->validate([
            'current_password'          => 'required',
            'new_password'              => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        if (!Hash::check($this->current_password, auth()->user()->password)) {
            $this->addError('current_password', 'Palavra-passe actual incorrecta.');
            return;
        }

        auth()->user()->update(['password' => Hash::make($this->new_password)]);
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->dispatch('notify', type: 'success', message: 'Palavra-passe alterada com sucesso.');
    }

    public function render()
    {
        return view('livewire.admin.profile')
            ->layout('layouts.admin', ['title' => 'O Meu Perfil']);
    }
}
