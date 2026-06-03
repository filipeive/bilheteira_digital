<div>
    <h1 style="font-size: 2rem; color: var(--gold); margin-bottom: 24px;">O MEU PERFIL</h1>

    <div style="display: grid; grid-template-columns: 1fr; gap: 24px; max-width: 600px;">
        {{-- Profile info --}}
        <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; padding: 24px;">
            <h3 style="color: var(--gold); font-size: 1.2rem; margin-bottom: 16px;">INFORMAÇÕES</h3>
            <form wire:submit="saveProfile">
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                    <img src="{{ auth()->user()->avatar_url }}" alt="" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 2px solid var(--gold);">
                    <div>
                        <input type="file" wire:model="avatar" accept="image/*" class="form-input" style="padding: 6px; font-size: 0.8rem;">
                        @error('avatar') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <label class="form-label">Nome</label>
                    <input type="text" wire:model="name" class="form-input">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div style="margin-bottom: 16px;">
                    <label class="form-label">Email</label>
                    <input type="email" wire:model="email" class="form-input">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div style="margin-bottom: 16px;">
                    <label class="form-label">Telefone</label>
                    <input type="text" wire:model="phone" class="form-input" placeholder="+258 84 xxx xxxx">
                    @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="btn-gold"><i data-lucide="save" class="w-4 h-4"></i> Guardar Perfil</button>
            </form>
        </div>

        {{-- Change password --}}
        <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; padding: 24px;">
            <h3 style="color: var(--gold); font-size: 1.2rem; margin-bottom: 16px;">ALTERAR PALAVRA-PASSE</h3>
            <form wire:submit="changePassword">
                <div style="margin-bottom: 16px;">
                    <label class="form-label">Palavra-passe actual</label>
                    <input type="password" wire:model="current_password" class="form-input">
                    @error('current_password') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div style="margin-bottom: 16px;">
                    <label class="form-label">Nova palavra-passe</label>
                    <input type="password" wire:model="new_password" class="form-input">
                    @error('new_password') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div style="margin-bottom: 16px;">
                    <label class="form-label">Confirmar nova palavra-passe</label>
                    <input type="password" wire:model="new_password_confirmation" class="form-input">
                </div>
                <button type="submit" class="btn-gold"><i data-lucide="lock" class="w-4 h-4"></i> Alterar Palavra-passe</button>
            </form>
        </div>
    </div>
</div>
