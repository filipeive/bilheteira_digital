@php
    $actionTranslations = [
        'ticket_deleted' => 'Bilhete Eliminado',
        'ticket_restored' => 'Bilhete Restaurado',
        'ticket_created' => 'Bilhete Criado',
        'ticket_updated' => 'Bilhete Actualizado',
        'ticket_validated' => 'Bilhete Validado',
        'ticket_confirmed' => 'Bilhete Confirmado',
        'ticket_cancelled' => 'Bilhete Cancelado',
        'user_created' => 'Utilizador Criado',
        'user_updated' => 'Utilizador Actualizado',
        'user_deleted' => 'Utilizador Eliminado',
        'settings_updated' => 'Configurações Actualizadas',
        'ticket_scan_failed' => 'Falha de Leitura (Não Encontrado)',
    ];
@endphp

<div>
    <h1 style="font-size: 2rem; color: var(--gold); margin-bottom: 24px;">AUDITORIA</h1>

    {{-- Filters --}}
    <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Pesquisar por utilizador, código ou titular..." class="form-input" style="max-width: 320px; width: 100%;">
        <select wire:model.live="filterAction" class="form-select" style="max-width: 220px; width: 100%;">
            <option value="">Todas as acções</option>
            @foreach($actions as $action)
                <option value="{{ $action }}">{{ $actionTranslations[$action] ?? str_replace('_', ' ', $action) }}</option>
            @endforeach
        </select>
    </div>

    {{-- Desktop Table View --}}
    <div class="desktop-only" style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; overflow: hidden;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Utilizador</th>
                    <th>Acção</th>
                    <th>Alvo</th>
                    <th>Detalhes da Modificação</th>
                    <th>IP</th>
                    <th style="text-align: right;">Acções</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                    $ticket = null;
                    if ($log->model_type === 'App\Models\Ticket') {
                        $ticket = \App\Models\Ticket::withTrashed()->find($log->model_id);
                    }
                    $scannedCode = $log->old_values['scanned_code'] ?? null;
                    $codeExists = $scannedCode ? \App\Models\Ticket::withTrashed()->where('ticket_code', $scannedCode)->exists() : false;
                @endphp
                <tr>
                    <td><span class="mono" style="font-size: 0.8rem;">{{ $log->created_at->format('d/m H:i:s') }}</span></td>
                    <td>{{ $log->user?->name ?? '—' }}</td>
                    <td>
                        <span class="badge badge-gold">{{ $actionTranslations[$log->action] ?? str_replace('_', ' ', $log->action) }}</span>
                    </td>
                    <td>
                        @if($log->model_type)
                            <span style="font-size: 0.8rem; color: var(--text-muted);">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @php $changes = $log->getFormattedChanges(); @endphp
                        @if(!empty($changes))
                            <ul style="margin: 0; padding-left: 16px; list-style-type: disc; font-size: 0.85rem; color: var(--text-secondary); display: flex; flex-direction: column; gap: 4px;">
                                @foreach($changes as $change)
                                    <li>{!! $change !!}</li>
                                @endforeach
                            </ul>
                        @else
                            <span style="color: var(--text-muted); font-size: 0.8rem;">—</span>
                        @endif
                    </td>
                    <td><span class="mono" style="font-size: 0.75rem; color: var(--text-muted);">{{ $log->ip_address }}</span></td>
                    <td style="text-align: right;">
                        @if($ticket && $ticket->trashed())
                            <button type="button" 
                                    onclick="swalConfirm('Restaurar Bilhete?', 'Deseja restaurar o bilhete {{ $ticket->ticket_code }}?', () => @this.restoreTicketFromLog({{ $log->id }}))" 
                                    class="btn-sm btn-gold" 
                                    style="font-size: 0.75rem; padding: 4px 8px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid var(--gold); background: transparent; color: var(--gold); border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                                <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i> Restaurar
                            </button>
                        @elseif($log->action === 'ticket_scan_failed' && $scannedCode)
                            @if($codeExists)
                                <span style="font-size: 0.75rem; color: var(--text-muted);">Resolvido (Existe)</span>
                            @else
                                <button type="button" 
                                        onclick="swalConfirm('Regenerar Bilhete?', 'Deseja criar e confirmar um novo bilhete com o código {{ $scannedCode }}?', () => @this.regenerateTicket({{ $log->id }}))" 
                                        class="btn-sm btn-gold" 
                                        style="font-size: 0.75rem; padding: 4px 8px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid var(--gold); background: transparent; color: var(--gold); border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                                    <i data-lucide="plus-circle" style="width: 14px; height: 14px;"></i> Regenerar
                                </button>
                            @endif
                        @elseif($ticket)
                            <span style="font-size: 0.75rem; color: var(--text-muted);">Activo</span>
                        @else
                            <span style="font-size: 0.75rem; color: var(--text-muted);">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">Nenhum registo de auditoria.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards View --}}
    <div class="mobile-only" style="display: none; flex-direction: column; gap: 16px;">
        @forelse($logs as $log)
            @php
                $ticket = null;
                if ($log->model_type === 'App\Models\Ticket') {
                    $ticket = \App\Models\Ticket::withTrashed()->find($log->model_id);
                }
                $scannedCode = $log->old_values['scanned_code'] ?? null;
                $codeExists = $scannedCode ? \App\Models\Ticket::withTrashed()->where('ticket_code', $scannedCode)->exists() : false;
            @endphp
            <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; padding: 16px; display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="badge badge-gold" style="font-size: 0.75rem;">{{ $actionTranslations[$log->action] ?? str_replace('_', ' ', $log->action) }}</span>
                    <span class="mono" style="font-size: 0.75rem; color: var(--text-muted);">{{ $log->created_at->format('d/m H:i:s') }}</span>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 0.85rem;">
                    <div>
                        <span style="color: var(--text-muted); display: block; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 2px;">Utilizador</span>
                        <span style="color: var(--text-primary); font-weight: 500;">{{ $log->user?->name ?? '—' }}</span>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 2px;">IP</span>
                        <span class="mono" style="color: var(--text-primary);">{{ $log->ip_address }}</span>
                    </div>
                </div>

                @if($log->model_type)
                    <div style="font-size: 0.85rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 8px;">
                        <span style="color: var(--text-muted); display: block; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 2px;">Alvo</span>
                        <span style="color: var(--text-secondary);">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</span>
                    </div>
                @endif

                @php $changes = $log->getFormattedChanges(); @endphp
                @if(!empty($changes))
                    <div style="background: rgba(0,0,0,0.2); border-radius: 8px; padding: 12px; font-size: 0.8rem; border: 1px solid rgba(255,255,255,0.03);">
                        <span style="color: var(--text-muted); display: block; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 6px;">Modificações</span>
                        <ul style="margin: 0; padding-left: 14px; list-style-type: disc; color: var(--text-secondary); display: flex; flex-direction: column; gap: 6px;">
                            @foreach($changes as $change)
                                <li>{!! $change !!}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($ticket && $ticket->trashed())
                    <div style="border-top: 1px solid rgba(255,255,255,0.05); padding-top: 8px; display: flex; justify-content: flex-end;">
                        <button type="button" 
                                onclick="swalConfirm('Restaurar Bilhete?', 'Deseja restaurar o bilhete {{ $ticket->ticket_code }}?', () => @this.restoreTicketFromLog({{ $log->id }}))" 
                                class="btn-sm btn-gold" 
                                style="font-size: 0.75rem; padding: 6px 12px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid var(--gold); background: transparent; color: var(--gold); border-radius: 6px; cursor: pointer; transition: all 0.2s; font-weight: 600;">
                            <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i> Restaurar Bilhete
                        </button>
                    </div>
                @elseif($log->action === 'ticket_scan_failed' && $scannedCode)
                    <div style="border-top: 1px solid rgba(255,255,255,0.05); padding-top: 8px; display: flex; justify-content: flex-end;">
                        @if($codeExists)
                            <span style="font-size: 0.75rem; color: var(--text-muted);">Resolvido (Existe)</span>
                        @else
                            <button type="button" 
                                    onclick="swalConfirm('Regenerar Bilhete?', 'Deseja criar e confirmar um novo bilhete com o código {{ $scannedCode }}?', () => @this.regenerateTicket({{ $log->id }}))" 
                                    class="btn-sm btn-gold" 
                                    style="font-size: 0.75rem; padding: 6px 12px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid var(--gold); background: transparent; color: var(--gold); border-radius: 6px; cursor: pointer; transition: all 0.2s; font-weight: 600;">
                                <i data-lucide="plus-circle" style="width: 14px; height: 14px;"></i> Regenerar Bilhete
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div style="text-align: center; padding: 40px; color: var(--text-muted); background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px;">
                Nenhum registo de auditoria.
            </div>
        @endforelse
    </div>

    <div style="margin-top: 20px;">{{ $logs->links() }}</div>

    <style>
        @media (min-width: 1024px) {
            .desktop-only { display: block !important; }
            .mobile-only { display: none !important; }
        }
        @media (max-width: 1023px) {
            .desktop-only { display: none !important; }
            .mobile-only { display: flex !important; }
        }
    </style>
</div>
