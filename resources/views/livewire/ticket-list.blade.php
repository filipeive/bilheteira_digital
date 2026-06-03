<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 2.5rem; color: var(--gold);">BILHETES</h1>
            <p style="color: var(--text-muted);">Gestão de todos os bilhetes</p>
        </div>
        <a href="{{ url('/admin/manual') }}" class="btn-gold"><i data-lucide="pen-line" class="w-4 h-4"></i> VENDA MANUAL</a>
    </div>

    <!-- Filters -->
    <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; padding: 16px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 12px;">
            <div>
                <div style="position: relative;">
                    <i data-lucide="search" class="w-4 h-4" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-input" placeholder="Pesquisar por nome, telefone ou código..." style="padding-left: 38px;">
                </div>
            </div>
            <div>
                <select wire:model.live="filterStatus" class="form-input" style="cursor: pointer;">
                    <option value="">Todos os Status</option>
                    <option value="pending">Pendente</option>
                    <option value="confirmed">Confirmado</option>
                    <option value="used">Usado</option>
                    <option value="cancelled">Cancelado</option>
                </select>
            </div>
            <div>
                <select wire:model.live="filterType" class="form-input" style="cursor: pointer;">
                    <option value="">Todos os Tipos</option>
                    <option value="promotional">Promocional</option>
                    <option value="second_lot">2º Lote</option>
                    <option value="gate">No Portão</option>
                    <option value="vip_promotional">VIP 1º Lote</option>
                    <option value="vip_second_lot">VIP 2º Lote</option>
                    <option value="vip">VIP No Portão</option>
                    <option value="free">Gratuito</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('ticket_code')">
                            Código
                            @if ($sortBy === 'ticket_code') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th wire:click="sortBy('buyer_name')">
                            Nome
                            @if ($sortBy === 'buyer_name') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th>Telefone</th>
                        <th wire:click="sortBy('ticket_type')">
                            Tipo
                            @if ($sortBy === 'ticket_type') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th wire:click="sortBy('price')">
                            Preço
                            @if ($sortBy === 'price') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th>Pagamento</th>
                        <th wire:click="sortBy('status')">
                            Status
                            @if ($sortBy === 'status') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th wire:click="sortBy('created_at')">
                            Data
                            @if ($sortBy === 'created_at') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th>Acções</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->tickets as $ticket)
                        <tr>
                            <td>
                                <span class="mono" style="color: var(--gold); font-weight: 600; font-size: 0.85rem;">{{ $ticket->ticket_code }}</span>
                            </td>
                            <td style="color: var(--text-primary); font-weight: 500;">{{ $ticket->buyer_name }}</td>
                            <td class="mono" style="font-size: 0.85rem;">{{ $ticket->buyer_phone }}</td>
                            <td><span class="badge badge-gold">{{ $ticket->getTicketTypeLabel() }}</span></td>
                            <td class="mono">{{ number_format($ticket->price, 0, ',', '.') }} MT</td>
                            <td style="font-size: 0.8rem;">
                                {{ strtoupper($ticket->payment_method) }}
                                @if ($ticket->payment_ref)
                                    <br><span style="color: var(--text-muted); font-size: 0.7rem;">{{ $ticket->payment_ref }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $ticket->getStatusColor() }}">{{ $ticket->getStatusLabel() }}</span>
                                @if ($ticket->used_at)
                                    <br>
                                    <span style="font-size: 0.7rem; color: var(--text-muted);">
                                        {{ $ticket->used_at->format('d/m H:i') }}
                                        @if($ticket->scanner)
                                            <br>por {{ $ticket->scanner->name }}
                                        @endif
                                    </span>
                                @endif
                            </td>
                            <td style="font-size: 0.8rem;">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div style="display: flex; gap: 6px; flex-wrap: nowrap;">
                                    <a href="https://wa.me/{{ str_replace('+', '', $ticket->buyer_phone) }}?text={{ urlencode('Aqui está o seu bilhete para o Concerto Renúncia: ' . URL::signedRoute('tickets.download', $ticket)) }}" target="_blank" class="btn-sm" title="Enviar WhatsApp" style="display: inline-flex; align-items: center; justify-content: center; background: rgba(37,211,102,0.1); color: #25D366; border: 1px solid rgba(37,211,102,0.3); text-decoration: none;">
                                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('admin.tickets.download.png', $ticket) }}" class="btn-sm" title="Baixar PNG" style="display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.05); color: var(--text-secondary); border: 1px solid rgba(255,255,255,0.1); text-decoration: none;">
                                        <i data-lucide="image" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('admin.tickets.download', $ticket) }}" class="btn-sm" title="Baixar PDF" style="display: inline-flex; align-items: center; justify-content: center; background: rgba(212,175,55,0.14); color: var(--gold); border: 1px solid rgba(212,175,55,0.3); text-decoration: none;">
                                        <i data-lucide="download" class="w-4 h-4"></i>
                                    </a>
                                    @if ($ticket->isPending())
                                        <button wire:click="confirmTicket('{{ $ticket->id }}')" wire:confirm="Confirmar bilhete {{ $ticket->ticket_code }}?" class="btn-sm btn-confirm" title="Confirmar">
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                    @if ($ticket->isConfirmed())
                                        <button wire:click="validateTicket('{{ $ticket->id }}')" wire:confirm="Validar entrada do bilhete {{ $ticket->ticket_code }}?" class="btn-sm" title="Validar Entrada" style="display: inline-flex; align-items: center; justify-content: center; background: rgba(16,185,129,0.14); color: #10B981; border: 1px solid rgba(16,185,129,0.3);">
                                            <i data-lucide="scan-line" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                    @if ($ticket->isConfirmed() || $ticket->isUsed())
                                        <button wire:click="resendTicket('{{ $ticket->id }}')" wire:confirm="Reenviar bilhete {{ $ticket->ticket_code }}?" class="btn-sm" title="Reenviar" style="display: inline-flex; align-items: center; justify-content: center; background: rgba(59,130,246,0.14); color: var(--accent-blue); border: 1px solid rgba(59,130,246,0.3);">
                                            <i data-lucide="send" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                    @if (!$ticket->isUsed() && !$ticket->isCancelled())
                                        <button wire:click="cancelTicket('{{ $ticket->id }}')" wire:confirm="Cancelar bilhete {{ $ticket->ticket_code }}?" class="btn-sm btn-cancel" title="Cancelar">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                Nenhum bilhete encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="padding: 16px; border-top: 1px solid var(--dark-border);">
            {{ $this->tickets->links('vendor.pagination.admin') }}
        </div>
    </div>

    <style>
        @media (max-width: 1024px) {
            div[style*="grid-template-columns: 2fr 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</div>
