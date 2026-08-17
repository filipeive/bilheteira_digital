<div style="position: relative;" x-data="{ open: false }" wire:poll.8s>
    {{-- Toggle Button --}}
    <button @click="open = !open" @click.outside="open = false" style="background: none; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; position: relative; padding: 8px; border-radius: 50%; transition: background-color 0.2s;" class="hover:bg-opacity-10">
        <i data-lucide="bell" style="width: 22px; height: 22px; color: var(--text-secondary);"></i>
        @if($unreadCount > 0)
            <span style="position: absolute; top: 2px; right: 2px; background-color: var(--accent-red); color: white; font-size: 0.65rem; font-weight: 700; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid var(--dark-bg);">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         style="display: none; position: absolute; top: 100%; right: 0; margin-top: 10px; background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; width: 340px; box-shadow: 0 10px 40px rgba(0,0,0,0.5); z-index: 100; overflow: hidden;">
        
        {{-- Header --}}
        <div style="padding: 14px 16px; border-bottom: 1px solid var(--dark-border); display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.15);">
            <h3 style="font-size: 0.9rem; font-weight: 600; color: var(--gold); margin: 0;">Notificações</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" style="background: none; border: none; font-size: 0.75rem; color: var(--text-muted); cursor: pointer; font-weight: 500; transition: color 0.2s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--text-muted)'">
                    Limpar todas
                </button>
            @endif
        </div>

        {{-- List --}}
        <div style="max-height: 320px; overflow-y: auto;">
            @forelse($notifications as $item)
                @php
                    $borderLeft = match($item->type) {
                        'success' => '3px solid var(--accent-green)',
                        'warning' => '3px solid var(--accent-yellow)',
                        'danger'  => '3px solid var(--accent-red)',
                        default   => '3px solid var(--accent-blue)',
                    };
                    $bg = $item->is_read ? 'transparent' : 'rgba(212,160,23,0.04)';
                @endphp
                <div wire:click="markAsRead({{ $item->id }})" 
                     style="padding: 12px 16px; border-bottom: 1px solid rgba(255,255,255,0.03); cursor: pointer; transition: all 0.2s; border-left: {{ $borderLeft }}; background-color: {{ $bg }};"
                     class="dropdown-item">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                        <span style="font-size: 0.8rem; font-weight: 600; color: {{ $item->is_read ? 'var(--text-secondary)' : 'var(--text-primary)' }};">
                            {{ $item->title }}
                        </span>
                        <span style="font-size: 0.65rem; color: var(--text-muted); white-space: nowrap;">
                            {{ $item->created_at->diffForHumans(null, true) }}
                        </span>
                    </div>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0; line-height: 1.3;">
                        {{ $item->message }}
                    </p>
                </div>
            @empty
                <div style="padding: 30px 16px; text-align: center; color: var(--text-muted); font-size: 0.8rem;">
                    <i data-lucide="bell-off" style="width: 24px; height: 24px; margin: 0 auto 8px; opacity: 0.4;"></i>
                    Sem notificações recentes
                </div>
            @endforelse
        </div>
    </div>
</div>
