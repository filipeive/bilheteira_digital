<div style="position: relative;">
    <!-- Premium Loading Overlay -->
    <div wire:loading.delay wire:target="tickets, search, filterStatus, filterType, toggleSelectAll, selectedIds, bulkEdit, bulkConfirm, bulkCancel" style="position: absolute; inset: 0; background: rgba(13,11,7,0.7); display: flex; align-items: center; justify-content: center; z-index: 999; border-radius: 16px; backdrop-filter: blur(2px);">
        <div style="display: flex; flex-direction: column; align-items: center; gap: 16px; background: rgba(20, 18, 14, 0.95); padding: 28px 48px; border-radius: 16px; border: 1px solid rgba(212,175,55,0.3); box-shadow: 0 20px 60px rgba(0,0,0,0.6);">
            <svg style="animation: spin 1s linear infinite; width: 44px; height: 44px; color: var(--gold);" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span style="color: var(--gold); font-weight: 600; font-size: 0.95rem; letter-spacing: 0.08em; text-transform: uppercase;">A carregar...</span>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 2.5rem; color: var(--gold);">BILHETES</h1>
            <p style="color: var(--text-muted);">Gestão de todos os bilhetes</p>
        </div>
        <div style="display: flex; gap: 8px; align-items: center;">
            {{-- View Mode Toggle --}}
            <div style="display: flex; background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 8px; overflow: hidden;">
                <button wire:click="$set('viewMode', 'table')" style="padding: 8px 12px; border: none; cursor: pointer; display: flex; align-items: center; {{ $viewMode === 'table' ? 'background: var(--gold); color: #0D0B07;' : 'background: transparent; color: var(--text-muted);' }}" title="Vista em Lista">
                    <i data-lucide="list" class="w-4 h-4"></i>
                </button>
                <button wire:click="$set('viewMode', 'grid')" style="padding: 8px 12px; border: none; cursor: pointer; display: flex; align-items: center; {{ $viewMode === 'grid' ? 'background: var(--gold); color: #0D0B07;' : 'background: transparent; color: var(--text-muted);' }}" title="Vista em Grelha">
                    <i data-lucide="layout-grid" class="w-4 h-4"></i>
                </button>
            </div>
            <a href="{{ url('/admin/manual') }}" class="btn-gold"><i data-lucide="pen-line" class="w-4 h-4"></i> VENDA MANUAL</a>
        </div>
    </div>

    {{-- Filters --}}
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

    {{-- Bulk Action Bar --}}
    @if(count($selectedIds) > 0)
    <div style="background: rgba(212,160,23,0.08); border: 1px solid rgba(212,160,23,0.25); border-radius: 10px; padding: 12px 20px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; animation: fadeInUp 0.2s ease;">
        <span style="color: var(--gold); font-weight: 600; font-size: 0.85rem;">
            <i data-lucide="check-square" class="w-4 h-4" style="display: inline; vertical-align: middle;"></i>
            {{ count($selectedIds) }} bilhete(s) seleccionado(s)
        </span>
        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <select wire:model.live="bulkBatchId" class="form-input" style="padding: 4px 8px; font-size: 0.8rem; width: auto; background: var(--dark-surface); border-color: rgba(212,175,55,0.3); height: 34px; color: var(--text-primary); cursor: pointer;">
                <option value="">Alterar Lote para...</option>
                @foreach($this->batches as $batch)
                    <option value="{{ $batch->id }}">{{ $batch->name }} ({{ number_format($batch->price, 0, ',', '.') }} MT)</option>
                @endforeach
            </select>

            <select wire:model.live="bulkStatus" class="form-input" style="padding: 4px 8px; font-size: 0.8rem; width: auto; background: var(--dark-surface); border-color: rgba(212,175,55,0.3); height: 34px; color: var(--text-primary); cursor: pointer;">
                <option value="">Alterar Estado para...</option>
                <option value="pending">Pendente</option>
                <option value="confirmed">Confirmado</option>
                <option value="used">Usado</option>
                <option value="cancelled">Cancelado</option>
            </select>

            <button type="button" wire:click="bulkEdit" onclick="confirm('Aplicar estas alterações em massa para os {{ count($selectedIds) }} bilhetes seleccionados?') || event.stopImmediatePropagation()" class="btn-sm btn-confirm" style="height: 34px;">
                <i data-lucide="save" class="w-4 h-4"></i> Aplicar
            </button>

            <div style="width: 1px; height: 24px; background: rgba(255,255,255,0.15); margin: 0 4px;"></div>

            <a href="{{ $this->bulkDownloadUrl }}" target="_blank" class="btn-sm" style="background: rgba(212,175,55,0.14); color: var(--gold); border-color: rgba(212,175,55,0.3); text-decoration: none; height: 34px; display: inline-flex; align-items: center;">
                <i data-lucide="download" class="w-4 h-4"></i> Baixar ZIP
            </a>
            <button type="button" wire:click="bulkConfirm" onclick="confirm('Confirmar {{ count($selectedIds) }} bilhete(s) seleccionado(s)?') || event.stopImmediatePropagation()" class="btn-sm btn-confirm" style="height: 34px;">
                <i data-lucide="check" class="w-4 h-4"></i> Confirmar todos
            </button>
            <button type="button" wire:click="bulkCancel" onclick="confirm('Cancelar {{ count($selectedIds) }} bilhete(s) seleccionado(s)?') || event.stopImmediatePropagation()" class="btn-sm btn-cancel" style="height: 34px;">
                <i data-lucide="x" class="w-4 h-4"></i> Cancelar todos
            </button>
            <button type="button" wire:click="$set('selectedIds', [])" class="btn-sm" style="color: var(--text-muted); height: 34px;">
                <i data-lucide="x-circle" class="w-4 h-4"></i> Limpar
            </button>
        </div>
    </div>
    @endif

    {{-- TABLE VIEW --}}
    @if($viewMode === 'table')
    <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px; cursor: default;">
                            <input type="checkbox" wire:click="toggleSelectAll" {{ $selectAll ? 'checked' : '' }} style="accent-color: var(--gold); cursor: pointer;">
                        </th>
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
                        <tr style="{{ in_array($ticket->id, $selectedIds) ? 'background: rgba(212,160,23,0.06);' : '' }}">
                            <td>
                                <input type="checkbox" wire:model.live="selectedIds" value="{{ $ticket->id }}" style="accent-color: var(--gold); cursor: pointer;">
                            </td>
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
                                    <a href="{{ route('admin.tickets.download', $ticket) }}" class="btn-sm" title="Baixar PDF" style="background: rgba(212,175,55,0.14); color: var(--gold); border-color: rgba(212,175,55,0.3); text-decoration: none;">
                                        <i data-lucide="download" class="w-4 h-4"></i>
                                    </a>

                                    <button wire:click="editTicket('{{ $ticket->id }}')" class="btn-sm" title="Editar" style="background: rgba(212,160,23,0.14); color: var(--gold); border-color: rgba(212,160,23,0.3);">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>

                                    <div x-data="{ open: false }" style="position: relative;">
                                        <button @click="open = !open" @click.outside="open = false" class="btn-sm" title="Mais Opções" style="background: rgba(255,255,255,0.05); color: var(--text-secondary); border-color: rgba(255,255,255,0.1); padding: 8px;">
                                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                        </button>
                                        <div x-show="open" x-transition style="position: absolute; right: 0; top: 100%; margin-top: 6px; background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.6); z-index: 90; min-width: 190px; padding: 6px; display: flex; flex-direction: column; gap: 2px;" style="display: none;">
                                            
                                            <a href="https://wa.me/{{ str_replace('+', '', $ticket->buyer_phone) }}?text={{ urlencode('Aqui está o seu bilhete para o Concerto Renúncia: ' . URL::signedRoute('tickets.download', $ticket)) }}" target="_blank" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: var(--text-secondary); font-size: 0.85rem; text-decoration: none; border-radius: 6px; transition: all 0.2s;">
                                                <i data-lucide="message-circle" class="w-4 h-4" style="color: #25D366;"></i> WhatsApp
                                            </a>

                                            <a href="{{ route('admin.tickets.download.png', $ticket) }}" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: var(--text-secondary); font-size: 0.85rem; text-decoration: none; border-radius: 6px; transition: all 0.2s;">
                                                <i data-lucide="image" class="w-4 h-4" style="color: var(--gold);"></i> Baixar PNG
                                            </a>

                                            <a href="{{ route('admin.tickets.preview', $ticket) }}" target="_blank" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: var(--text-secondary); font-size: 0.85rem; text-decoration: none; border-radius: 6px; transition: all 0.2s;">
                                                <i data-lucide="eye" class="w-4 h-4" style="color: #3B82F6;"></i> Pré-visualizar
                                            </a>

                                            @if ($ticket->isPending())
                                                <button wire:click="confirmTicket('{{ $ticket->id }}')" wire:confirm="Confirmar bilhete {{ $ticket->ticket_code }}?" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #10B981; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                                    <i data-lucide="check" class="w-4 h-4" style="color: #10B981;"></i> Confirmar
                                                </button>
                                            @endif

                                            @if ($ticket->isConfirmed())
                                                <button wire:click="validateTicket('{{ $ticket->id }}')" wire:confirm="Validar entrada do bilhete {{ $ticket->ticket_code }}?" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #10B981; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                                    <i data-lucide="scan-line" class="w-4 h-4" style="color: #10B981;"></i> Validar Entrada
                                                </button>
                                            @endif

                                            @if ($ticket->isConfirmed() || $ticket->isUsed())
                                                <button wire:click="resendTicket('{{ $ticket->id }}')" wire:confirm="Reenviar bilhete {{ $ticket->ticket_code }}?" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #3B82F6; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                                    <i data-lucide="send" class="w-4 h-4" style="color: #3B82F6;"></i> Reenviar
                                                </button>
                                            @endif

                                            @if (!$ticket->isUsed() && !$ticket->isCancelled())
                                                <button wire:click="cancelTicket('{{ $ticket->id }}')" wire:confirm="Cancelar bilhete {{ $ticket->ticket_code }}?" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #F59E0B; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                                    <i data-lucide="x" class="w-4 h-4" style="color: #F59E0B;"></i> Cancelar
                                                </button>
                                            @endif

                                            <div style="height: 1px; background: var(--dark-border); margin: 4px 0;"></div>

                                            <button wire:click="deleteTicket('{{ $ticket->id }}')" wire:confirm="Tens a certeza que desejas ELIMINAR permanentemente o bilhete {{ $ticket->ticket_code }}? Esta acção é irreversível e o bilhete não poderá ser recuperado." class="dropdown-item text-danger" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #EF4444; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                                <i data-lucide="trash-2" class="w-4 h-4" style="color: #EF4444;"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i data-lucide="inbox" class="w-8 h-8" style="display: block; margin: 0 auto 12px; opacity: 0.4;"></i>
                                Nenhum bilhete encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div style="padding: 16px; border-top: 1px solid var(--dark-border);">
            {{ $this->tickets->links('vendor.pagination.admin') }}
        </div>
    </div>
    @endif

    {{-- GRID VIEW --}}
    @if($viewMode === 'grid')
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px; margin-bottom: 20px;">
        @forelse ($this->tickets as $ticket)
            <div style="border: 1px solid {{ in_array($ticket->id, $selectedIds) ? 'rgba(212,160,23,0.5)' : 'var(--dark-border)' }}; border-radius: 12px; padding: 20px; background: {{ in_array($ticket->id, $selectedIds) ? 'rgba(212,160,23,0.06)' : 'var(--dark-card)' }}; transition: all 0.2s;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" wire:model.live="selectedIds" value="{{ $ticket->id }}" style="accent-color: var(--gold); cursor: pointer;">
                        <div>
                            <span class="mono" style="color: var(--gold); font-weight: 600; font-size: 0.95rem;">{{ $ticket->ticket_code }}</span>
                            <div style="color: var(--text-primary); font-weight: 500; font-size: 1.05rem; margin-top: 4px;">{{ $ticket->buyer_name }}</div>
                        </div>
                    </div>
                    <span class="badge badge-{{ $ticket->getStatusColor() }}">{{ $ticket->getStatusLabel() }}</span>
                </div>

                <div class="mono" style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 12px;">{{ $ticket->buyer_phone }}</div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; font-size: 0.85rem; background: rgba(255,255,255,0.02); padding: 12px; border-radius: 8px;">
                    <div>
                        <span style="color: var(--text-muted); display: block; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 2px;">Tipo</span>
                        <span style="color: var(--gold); font-weight: 500;">{{ $ticket->getTicketTypeLabel() }}</span>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 2px;">Preço</span>
                        <span class="mono">{{ number_format($ticket->price, 0, ',', '.') }} MT</span>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 2px;">Pagamento</span>
                        <span>{{ strtoupper($ticket->payment_method) }}</span>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 2px;">Data</span>
                        <span>{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="{{ route('admin.tickets.download', $ticket) }}" class="btn-sm" style="flex: 1; justify-content: center; background: rgba(212,175,55,0.14); color: var(--gold); border-color: rgba(212,175,55,0.3); text-decoration: none;">
                        <i data-lucide="download" class="w-4 h-4"></i> PDF
                    </a>

                    <button wire:click="editTicket('{{ $ticket->id }}')" class="btn-sm" style="flex: 1; justify-content: center; background: rgba(212,160,23,0.14); color: var(--gold); border-color: rgba(212,160,23,0.3);">
                        <i data-lucide="edit" class="w-4 h-4"></i> Editar
                    </button>

                    <div x-data="{ open: false }" style="position: relative;">
                        <button @click="open = !open" @click.outside="open = false" class="btn-sm" title="Mais Opções" style="background: rgba(255,255,255,0.05); color: var(--text-secondary); border-color: rgba(255,255,255,0.1); padding: 8px; height: 38px; display: flex; align-items: center;">
                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                        </button>
                        <div x-show="open" x-transition style="position: absolute; right: 0; bottom: 100%; margin-bottom: 6px; background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 8px; box-shadow: 0 -10px 30px rgba(0,0,0,0.6); z-index: 90; min-width: 190px; padding: 6px; display: flex; flex-direction: column; gap: 2px;" style="display: none;">
                            
                            <a href="https://wa.me/{{ str_replace('+', '', $ticket->buyer_phone) }}?text={{ urlencode('Aqui está o seu bilhete para o Concerto Renúncia: ' . URL::signedRoute('tickets.download', $ticket)) }}" target="_blank" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: var(--text-secondary); font-size: 0.85rem; text-decoration: none; border-radius: 6px; transition: all 0.2s;">
                                <i data-lucide="message-circle" class="w-4 h-4" style="color: #25D366;"></i> WhatsApp
                            </a>

                            <a href="{{ route('admin.tickets.download.png', $ticket) }}" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: var(--text-secondary); font-size: 0.85rem; text-decoration: none; border-radius: 6px; transition: all 0.2s;">
                                <i data-lucide="image" class="w-4 h-4" style="color: var(--gold);"></i> Baixar PNG
                            </a>

                            <a href="{{ route('admin.tickets.preview', $ticket) }}" target="_blank" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: var(--text-secondary); font-size: 0.85rem; text-decoration: none; border-radius: 6px; transition: all 0.2s;">
                                <i data-lucide="eye" class="w-4 h-4" style="color: #3B82F6;"></i> Pré-visualizar
                            </a>

                            @if ($ticket->isPending())
                                <button wire:click="confirmTicket('{{ $ticket->id }}')" wire:confirm="Confirmar bilhete {{ $ticket->ticket_code }}?" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #10B981; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                    <i data-lucide="check" class="w-4 h-4" style="color: #10B981;"></i> Confirmar
                                </button>
                            @endif

                            @if ($ticket->isConfirmed())
                                <button wire:click="validateTicket('{{ $ticket->id }}')" wire:confirm="Validar entrada do bilhete {{ $ticket->ticket_code }}?" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #10B981; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                    <i data-lucide="scan-line" class="w-4 h-4" style="color: #10B981;"></i> Validar Entrada
                                </button>
                            @endif

                            @if ($ticket->isConfirmed() || $ticket->isUsed())
                                <button wire:click="resendTicket('{{ $ticket->id }}')" wire:confirm="Reenviar bilhete {{ $ticket->ticket_code }}?" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #3B82F6; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                    <i data-lucide="send" class="w-4 h-4" style="color: #3B82F6;"></i> Reenviar
                                </button>
                            @endif

                            @if (!$ticket->isUsed() && !$ticket->isCancelled())
                                <button wire:click="cancelTicket('{{ $ticket->id }}')" wire:confirm="Cancelar bilhete {{ $ticket->ticket_code }}?" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #F59E0B; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                    <i data-lucide="x" class="w-4 h-4" style="color: #F59E0B;"></i> Cancelar
                                </button>
                            @endif

                            <div style="height: 1px; background: var(--dark-border); margin: 4px 0;"></div>

                            <button wire:click="deleteTicket('{{ $ticket->id }}')" wire:confirm="Tens a certeza que desejas ELIMINAR permanentemente o bilhete {{ $ticket->ticket_code }}? Esta acção é irreversível e o bilhete não poderá ser recuperado." class="dropdown-item text-danger" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #EF4444; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                <i data-lucide="trash-2" class="w-4 h-4" style="color: #EF4444;"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px; color: var(--text-muted);">
                <i data-lucide="inbox" class="w-10 h-10" style="display: block; margin: 0 auto 12px; opacity: 0.4;"></i>
                Nenhum bilhete encontrado.
            </div>
        @endforelse
    </div>

    {{-- Grid Pagination --}}
    <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; padding: 16px;">
        {{ $this->tickets->links('vendor.pagination.admin') }}
    </div>
    @endif

    @if($isEditing)
    <div style="position: fixed; inset: 0; background: rgba(13,11,7,0.85); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; animation: fadeIn 0.2s ease;">
        <div style="background: var(--dark-surface); border: 1px solid var(--dark-border); border-radius: 16px; width: 100%; max-width: 500px; padding: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: var(--gold); font-size: 1.5rem; font-family: 'Bebas Neue'; letter-spacing: 1px;">EDITAR BILHETE</h3>
                <button wire:click="$set('isEditing', false)" style="background: none; border: none; color: var(--text-muted); cursor: pointer;">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveTicket">
                <div style="margin-bottom: 16px;">
                    <label class="form-label">Nome do Titular</label>
                    <input type="text" wire:model="editingName" class="form-input" required>
                    @error('editingName') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div style="margin-bottom: 16px;">
                    <label class="form-label">Telefone</label>
                    <input type="text" wire:model="editingPhone" class="form-input">
                    @error('editingPhone') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div style="margin-bottom: 16px;">
                    <label class="form-label">Email</label>
                    <input type="email" wire:model="editingEmail" class="form-input">
                    @error('editingEmail') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div style="margin-bottom: 24px;">
                    <label class="form-label">Estado do Bilhete</label>
                    <select wire:model="editingStatus" class="form-input" style="cursor: pointer;">
                        <option value="pending">Pendente</option>
                        <option value="confirmed">Confirmado</option>
                        <option value="used">Usado (Entrada Validada)</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                    @error('editingStatus') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" wire:click="$set('isEditing', false)" class="btn-outline" style="padding: 8px 16px;">Cancelar</button>
                    <button type="submit" class="btn-gold" style="padding: 8px 20px;"><i data-lucide="save" class="w-4 h-4"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <style>
        @media (max-width: 1024px) {
            div[style*="grid-template-columns: 2fr 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.05) !important;
            color: var(--gold) !important;
        }
        .dropdown-item.text-danger:hover {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #EF4444 !important;
        }
    </style>
</div>
