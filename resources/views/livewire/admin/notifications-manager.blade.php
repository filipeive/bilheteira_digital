<div>
    <h1 style="font-size: 2rem; color: var(--gold); margin-bottom: 24px;">GESTÃO DE NOTIFICAÇÕES E EMAILS</h1>

    {{-- Filters --}}
    <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Pesquisar por bilhete, titular, email..." class="form-input" style="max-width: 300px;">
        <select wire:model.live="filterAction" class="form-select" style="max-width: 220px;">
            <option value="">Todos os tipos</option>
            <option value="sent_ticket_notification">Envios Individuais</option>
            <option value="sent_bulk_ticket_notification">Envios em Massa</option>
            <option value="send_ticket_failed">Falhas de Envio</option>
        </select>
    </div>

    {{-- Desktop Table View --}}
    <div class="desktop-only" style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; overflow: hidden;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Destinatário / Bilhete</th>
                    <th>Estado de Envio</th>
                    <th>Detalhes / Erro</th>
                    <th>Ações de Reenvio</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td><span class="mono" style="font-size: 0.8rem;">{{ $log->created_at->format('d/m H:i:s') }}</span></td>
                    <td>
                        @if($log->action === 'sent_ticket_notification')
                            <span class="badge badge-green">Individual</span>
                        @elseif($log->action === 'sent_bulk_ticket_notification')
                            <span class="badge badge-blue">Lote / Massa</span>
                        @else
                            <span class="badge badge-red">Falhou</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $newValues = $log->new_values ?: [];
                            $oldValues = $log->old_values ?: [];
                            $ticketId = $log->model_id;
                            $ticket = \App\Models\Ticket::find($ticketId);
                        @endphp
                        @if($ticket)
                            <div style="font-weight: 600; color: var(--text-primary);">{{ $ticket->buyer_name }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">
                                Code: <span class="mono">{{ $ticket->ticket_code }}</span> | 
                                {{ $ticket->buyer_email ?: 'Sem Email' }} | 
                                {{ $ticket->buyer_phone ?: 'Sem Telefone' }}
                            </div>
                        @else
                            <div style="font-weight: 600; color: var(--text-muted);">Bilhete #{{ $ticketId }} (Eliminado)</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">
                                Destinatário desconhecido
                            </div>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            @if(isset($newValues['email']))
                                @if($newValues['email'])
                                    <span class="badge badge-green" style="font-size: 0.65rem;">Email Enviado</span>
                                @else
                                    <span class="badge badge-red" style="font-size: 0.65rem;">Email Falhou</span>
                                @endif
                            @endif

                            @if(isset($newValues['sms']))
                                @if($newValues['sms'])
                                    <span class="badge badge-green" style="font-size: 0.65rem;">SMS/WA Enviado</span>
                                @else
                                    <span class="badge badge-red" style="font-size: 0.65rem;">SMS/WA Falhou</span>
                                @endif
                            @endif

                            @if($log->action === 'send_ticket_failed')
                                <span class="badge badge-red" style="font-size: 0.65rem;">Erro Geral</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if(isset($newValues['error']))
                            <span style="font-size: 0.8rem; color: var(--accent-red);">{{ $newValues['error'] }}</span>
                        @elseif(isset($newValues['error_email']))
                            <span style="font-size: 0.8rem; color: var(--accent-red);">Email: {{ $newValues['error_email'] }}</span>
                        @elseif(isset($newValues['error_sms']))
                            <span style="font-size: 0.8rem; color: var(--accent-red);">SMS: {{ $newValues['error_sms'] }}</span>
                        @else
                            <span style="color: var(--text-muted); font-size: 0.8rem;">Enviado sem erros</span>
                        @endif
                    </td>
                    <td>
                        @if($ticket)
                            <div style="display: flex; gap: 6px;">
                                @if($ticket->buyer_email)
                                    <button wire:click="resendNotification({{ $log->id }}, 'email')" class="btn-sm btn-confirm" title="Reenviar Email">
                                        <i data-lucide="mail" class="w-3 h-3"></i> Email
                                    </button>
                                @endif
                                @if($ticket->buyer_phone)
                                    <button wire:click="resendNotification({{ $log->id }}, 'sms')" class="btn-sm btn-confirm" title="Reenviar SMS">
                                        <i data-lucide="message-square" class="w-3 h-3"></i> SMS/WA
                                    </button>
                                @endif
                                <button wire:click="resendNotification({{ $log->id }}, 'all')" class="btn-sm btn-gold" title="Reenviar Ambos">
                                    <i data-lucide="refresh-cw" class="w-3 h-3"></i> Ambos
                                </button>
                            </div>
                        @else
                            <span style="color: var(--text-muted); font-size: 0.8rem;">Indisponível</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">Nenhum registo de envio de notificações.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards View --}}
    <div class="mobile-only" style="display: none; flex-direction: column; gap: 16px;">
        @forelse($logs as $log)
            @php
                $newValues = $log->new_values ?: [];
                $ticketId = $log->model_id;
                $ticket = \App\Models\Ticket::find($ticketId);
            @endphp
            <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; padding: 16px; display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    @if($log->action === 'sent_ticket_notification')
                        <span class="badge badge-green">Individual</span>
                    @elseif($log->action === 'sent_bulk_ticket_notification')
                        <span class="badge badge-blue">Lote / Massa</span>
                    @else
                        <span class="badge badge-red">Falhou</span>
                    @endif
                    <span class="mono" style="font-size: 0.75rem; color: var(--text-muted);">{{ $log->created_at->format('d/m H:i:s') }}</span>
                </div>
                
                <div>
                    @if($ticket)
                        <div style="font-weight: 600; color: var(--text-primary); font-size: 0.95rem;">{{ $ticket->buyer_name }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 4px;">
                            Cód: <span class="mono">{{ $ticket->ticket_code }}</span>
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">
                            Email: {{ $ticket->buyer_email ?: '—' }} | Tel: {{ $ticket->buyer_phone ?: '—' }}
                        </div>
                    @else
                        <div style="font-weight: 600; color: var(--text-muted);">Bilhete #{{ $ticketId }} (Eliminado)</div>
                    @endif
                </div>

                <div style="display: flex; gap: 8px; flex-wrap: wrap; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 8px;">
                    @if(isset($newValues['email']))
                        <span class="badge {{ $newValues['email'] ? 'badge-green' : 'badge-red' }}" style="font-size: 0.65rem;">
                            Email: {{ $newValues['email'] ? 'OK' : 'Falhou' }}
                        </span>
                    @endif
                    @if(isset($newValues['sms']))
                        <span class="badge {{ $newValues['sms'] ? 'badge-green' : 'badge-red' }}" style="font-size: 0.65rem;">
                            SMS/WA: {{ $newValues['sms'] ? 'OK' : 'Falhou' }}
                        </span>
                    @endif
                </div>

                @if(isset($newValues['error']) || isset($newValues['error_email']) || isset($newValues['error_sms']))
                    <div style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); border-radius: 8px; padding: 10px; font-size: 0.75rem; color: #F87171;">
                        <strong>Erro:</strong> 
                        {{ $newValues['error'] ?? $newValues['error_email'] ?? $newValues['error_sms'] }}
                    </div>
                @endif

                @if($ticket)
                    <div style="display: flex; gap: 6px; margin-top: 4px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 10px;">
                        @if($ticket->buyer_email)
                            <button wire:click="resendNotification({{ $log->id }}, 'email')" class="btn-sm btn-confirm" style="flex: 1;">
                                <i data-lucide="mail" class="w-3.5 h-3.5"></i> Email
                            </button>
                        @endif
                        @if($ticket->buyer_phone)
                            <button wire:click="resendNotification({{ $log->id }}, 'sms')" class="btn-sm btn-confirm" style="flex: 1;">
                                <i data-lucide="message-square" class="w-3.5 h-3.5"></i> SMS
                            </button>
                        @endif
                        <button wire:click="resendNotification({{ $log->id }}, 'all')" class="btn-sm btn-gold" style="flex: 1;">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Ambos
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div style="text-align: center; padding: 40px; color: var(--text-muted); background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px;">
                Nenhum registo de envio de notificações.
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
