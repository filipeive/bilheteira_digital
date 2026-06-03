<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <h1 style="font-size: 2rem; color: var(--gold);">UTILIZADORES</h1>
        <a href="{{ url('/admin/users/create') }}" class="btn-gold"><i data-lucide="user-plus" class="w-4 h-4"></i> Novo Utilizador</a>
    </div>

    {{-- Filters --}}
    <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Pesquisar nome ou email..." class="form-input" style="max-width: 300px;">
        <select wire:model.live="filterRole" class="form-select" style="max-width: 180px;">
            <option value="">Todos os perfis</option>
            @foreach($roles as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterStatus" class="form-select" style="max-width: 160px;">
            <option value="">Todos os estados</option>
            <option value="active">Activos</option>
            <option value="inactive">Inactivos</option>
        </select>
    </div>

    {{-- Desktop table --}}
    <div class="hidden md:block" style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; overflow: hidden;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Utilizador</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Perfil</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Acções</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <img src="{{ $user->avatar_url }}" alt="" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                            <span style="color: var(--text-primary); font-weight: 500;">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td><span class="mono" style="font-size: 0.82rem;">{{ $user->email }}</span></td>
                    <td>{{ $user->phone ?? '—' }}</td>
                    <td>
                        <select wire:change="changeRole({{ $user->id }}, $event.target.value)" class="form-select" style="max-width: 140px; padding: 4px 8px; font-size: 0.8rem;">
                            @foreach($roles as $key => $label)
                                <option value="{{ $key }}" {{ $user->role === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge badge-green">Activo</span>
                        @else
                            <span class="badge badge-red">Inactivo</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 6px; justify-content: flex-end;">
                            <a href="{{ url('/admin/users/' . $user->id . '/edit') }}" class="btn-sm btn-confirm" title="Editar"><i data-lucide="pencil" class="w-3 h-3"></i></a>
                            @if($user->id !== auth()->id())
                                <button wire:click="toggleActive({{ $user->id }})" class="btn-sm" style="background: rgba(245,158,11,0.15); color: #FBBF24; border: 1px solid rgba(245,158,11,0.3);" title="{{ $user->is_active ? 'Desactivar' : 'Activar' }}">
                                    <i data-lucide="{{ $user->is_active ? 'user-x' : 'user-check' }}" class="w-3 h-3"></i>
                                </button>
                                <button wire:click="deleteUser({{ $user->id }})" wire:confirm="Tem a certeza que quer eliminar este utilizador?" class="btn-sm btn-cancel" title="Eliminar"><i data-lucide="trash-2" class="w-3 h-3"></i></button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">Nenhum utilizador encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="md:hidden" style="display: flex; flex-direction: column; gap: 12px;">
        @foreach($users as $user)
        <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 10px; padding: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="{{ $user->avatar_url }}" alt="" style="width: 40px; height: 40px; border-radius: 50%;">
                    <div>
                        <p style="font-weight: 600; color: var(--text-primary);">{{ $user->name }}</p>
                        <p class="mono" style="font-size: 0.75rem; color: var(--text-muted);">{{ $user->email }}</p>
                    </div>
                </div>
                @if($user->is_active)
                    <span class="badge badge-green">Activo</span>
                @else
                    <span class="badge badge-red">Inactivo</span>
                @endif
            </div>
            <div style="display: flex; gap: 8px; margin-top: 12px; flex-wrap: wrap;">
                <span class="badge badge-gold">{{ $roles[$user->role] ?? $user->role }}</span>
                @if($user->phone)<span style="font-size: 0.8rem; color: var(--text-secondary);">{{ $user->phone }}</span>@endif
            </div>
            <div style="display: flex; gap: 8px; margin-top: 12px;">
                <a href="{{ url('/admin/users/' . $user->id . '/edit') }}" class="btn-sm btn-confirm">Editar</a>
                @if($user->id !== auth()->id())
                    <button wire:click="toggleActive({{ $user->id }})" class="btn-sm" style="background: rgba(245,158,11,0.15); color: #FBBF24; border: 1px solid rgba(245,158,11,0.3);">{{ $user->is_active ? 'Desactivar' : 'Activar' }}</button>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top: 20px;">{{ $users->links() }}</div>
</div>
