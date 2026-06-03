<div>
    <h1 style="font-size: 2rem; color: var(--gold); margin-bottom: 24px;">{{ $isEdit ? 'EDITAR UTILIZADOR' : 'NOVO UTILIZADOR' }}</h1>

    <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; padding: 24px; max-width: 600px;">
        <form wire:submit="save">
            <div style="margin-bottom: 16px;">
                <label class="form-label">Nome</label>
                <input type="text" wire:model="name" class="form-input" placeholder="Nome completo">
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom: 16px;">
                <label class="form-label">Email</label>
                <input type="email" wire:model="email" class="form-input" placeholder="email@exemplo.com">
                @error('email') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom: 16px;">
                <label class="form-label">Telefone</label>
                <input type="text" wire:model="phone" class="form-input" placeholder="+258 84 xxx xxxx">
                @error('phone') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom: 16px;">
                <label class="form-label">Perfil</label>
                <select wire:model="role" class="form-select">
                    @foreach($roles as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('role') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom: 16px;">
                <label class="form-label">{{ $isEdit ? 'Nova Palavra-passe (deixe vazio para manter)' : 'Palavra-passe' }}</label>
                <input type="password" wire:model="password" class="form-input" placeholder="Mínimo 8 caracteres">
                @error('password') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label class="form-label">Avatar</label>
                <input type="file" wire:model="avatar" accept="image/*" class="form-input" style="padding: 8px;">
                @error('avatar') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn-gold"><i data-lucide="save" class="w-4 h-4"></i> {{ $isEdit ? 'Guardar' : 'Criar' }}</button>
                <a href="{{ url('/admin/users') }}" class="btn-outline"><i data-lucide="arrow-left" class="w-4 h-4"></i> Voltar</a>
            </div>
        </form>
    </div>
</div>
