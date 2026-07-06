<div x-data="{
    selectedIds: $wire.entangle('selectedIds'),
    getCurrentPageIds() {
        let el = document.getElementById('page-ids-container');
        return el ? JSON.parse(el.getAttribute('data-ids')) : [];
    },
    toggleAll() {
        let pageIds = this.getCurrentPageIds();
        let allSelected = pageIds.every(id => this.selectedIds.includes(id));
        if (allSelected) {
            this.selectedIds = this.selectedIds.filter(id => !pageIds.includes(id));
        } else {
            let newSelected = [...this.selectedIds];
            pageIds.forEach(id => {
                if (!newSelected.includes(id)) newSelected.push(id);
            });
            this.selectedIds = newSelected;
        }
    },
    isAllSelected() {
        let pageIds = this.getCurrentPageIds();
        return pageIds.length > 0 && pageIds.every(id => this.selectedIds.includes(id));
    }
}">
    <div id="page-ids-container" data-ids="{{ json_encode($this->tickets->pluck('id')->map(fn($id) => (string)$id)->toArray()) }}" style="display: none;"></div>
    <!-- Premium Loading Overlay -->
    <div wire:loading wire:target="search, filterStatus, filterType, filterMode, perPage, gotoPage, previousPage, nextPage, setPage" class="loading-overlay" style="display: none;">
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
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr; gap: 12px;">
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
                    <option value="deleted">Eliminados (Lixeira)</option>
                </select>
            </div>
            <div>
                <select wire:model.live="filterType" class="form-input" style="cursor: pointer;">
                    <option value="">Todos os Tipos</option>
                    @foreach($this->ticketTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="filterMode" class="form-input" style="cursor: pointer;">
                    <option value="">Origem (Todas)</option>
                    <option value="personalized">Online / Personalizado</option>
                    <option value="quick_sale">Venda Rápida</option>
                </select>
            </div>
            <div>
                <select wire:model.live="perPage" class="form-input" style="cursor: pointer;">
                    <option value="10">10 / página</option>
                    <option value="20">20 / página</option>
                    <option value="50">50 / página</option>
                    <option value="100">100 / página</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Migrar Lote Expirado --}}
    @php
        $expiredBatch = \App\Models\TicketBatch::where('is_active', true)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->orderBy('sort_order')
            ->first();
    @endphp
    @if($expiredBatch)
    <div style="background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.25); border-radius: 10px; padding: 12px 20px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <span style="color: #EF4444; font-weight: 600; font-size: 0.85rem;">
            <i data-lucide="alert-triangle" class="w-4 h-4" style="display: inline; vertical-align: middle;"></i>
            Lote "{{ $expiredBatch->name }}" expirado em {{ $expiredBatch->ends_at->format('d/m/Y H:i') }}
        </span>
        <button type="button" x-on:click="swalConfirm('Migrar Bilhetes', 'Migrar todos os bilhetes pendentes/confirmados do lote expirado para o próximo lote disponível?', () => $wire.migrateExpiredBatch())" class="btn-sm" style="background: rgba(239, 68, 68, 0.14); color: #EF4444; border-color: rgba(239, 68, 68, 0.3); height: 34px; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="arrow-right-left" class="w-4 h-4"></i> Migrar Bilhetes
        </button>
    </div>
    @endif

    {{-- Bulk Action Bar --}}
    <div x-show="selectedIds.length > 0" x-cloak style="background: rgba(212,160,23,0.08); border: 1px solid rgba(212,160,23,0.25); border-radius: 10px; padding: 12px 20px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; animation: fadeInUp 0.2s ease;">
        <span style="color: var(--gold); font-weight: 600; font-size: 0.85rem;">
            <i data-lucide="check-square" class="w-4 h-4" style="display: inline; vertical-align: middle;"></i>
            <span x-text="selectedIds.length"></span> bilhete(s) seleccionado(s)
        </span>
        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <select wire:model="bulkBatchId" class="form-input" style="padding: 4px 8px; font-size: 0.8rem; width: auto; background: var(--dark-surface); border-color: rgba(212,175,55,0.3); height: 34px; color: var(--text-primary); cursor: pointer;">
                <option value="">Alterar Lote para...</option>
                @foreach($this->batches as $batch)
                    <option value="{{ $batch->id }}">{{ $batch->display_name }} ({{ number_format($batch->price, 0, ',', '.') }} MT)</option>
                @endforeach
            </select>

            <select wire:model="bulkStatus" class="form-input" style="padding: 4px 8px; font-size: 0.8rem; width: auto; background: var(--dark-surface); border-color: rgba(212,175,55,0.3); height: 34px; color: var(--text-primary); cursor: pointer;">
                <option value="">Alterar Estado para...</option>
                <option value="pending">Pendente</option>
                <option value="confirmed">Confirmado</option>
                <option value="used">Usado</option>
                <option value="cancelled">Cancelado</option>
            </select>

            <button type="button" x-on:click="swalConfirm('Aplicar Alterações', 'Aplicar estas alterações em massa para os ' + selectedIds.length + ' bilhetes seleccionados?', () => $wire.bulkEdit())" wire:loading.attr="disabled" class="btn-sm btn-confirm" style="height: 34px; display: inline-flex; align-items: center; gap: 8px;">
                <span wire:loading.remove wire:target="bulkEdit" style="display: inline-flex; align-items: center;">
                    <i data-lucide="save" class="w-4 h-4"></i>
                </span>
                <span wire:loading wire:target="bulkEdit" class="spinner-sm" style="display: none;"></span>
                Aplicar
            </button>

            <div style="width: 1px; height: 24px; background: rgba(255,255,255,0.15); margin: 0 4px;"></div>

            @if ($filterStatus === 'deleted')
                <button type="button" x-on:click="swalConfirm('Restaurar Bilhetes', 'Desejas restaurar os ' + selectedIds.length + ' bilhete(s) seleccionado(s)?', () => $wire.bulkRestore())" wire:loading.attr="disabled" class="btn-sm" style="height: 34px; display: inline-flex; align-items: center; gap: 8px; background: rgba(16, 185, 129, 0.14); color: #10B981; border-color: rgba(16, 185, 129, 0.3);">
                    <span wire:loading.remove wire:target="bulkRestore" style="display: inline-flex; align-items: center;">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </span>
                    <span wire:loading wire:target="bulkRestore" class="spinner-sm" style="display: none; border-color: rgba(16, 185, 129, 0.3); border-right-color: #10B981;"></span>
                    Restaurar seleccionados
                </button>
            @else
                <a href="{{ $this->bulkDownloadUrl }}" target="_blank" class="btn-sm" style="background: rgba(212,175,55,0.14); color: var(--gold); border-color: rgba(212,175,55,0.3); text-decoration: none; height: 34px; display: inline-flex; align-items: center;">
                    <i data-lucide="download" class="w-4 h-4"></i> Baixar ZIP
                </a>
                <button type="button" x-on:click="swalConfirm('Confirmar Bilhetes', 'Confirmar os ' + selectedIds.length + ' bilhete(s) seleccionado(s)?', () => $wire.bulkConfirm())" wire:loading.attr="disabled" class="btn-sm btn-confirm" style="height: 34px; display: inline-flex; align-items: center; gap: 8px;">
                    <span wire:loading.remove wire:target="bulkConfirm" style="display: inline-flex; align-items: center;">
                        <i data-lucide="check" class="w-4 h-4"></i>
                    </span>
                    <span wire:loading wire:target="bulkConfirm" class="spinner-sm" style="display: none;"></span>
                    Confirmar todos
                </button>
                <button type="button" x-on:click="swalConfirm('Cancelar Bilhetes', 'Cancelar os ' + selectedIds.length + ' bilhete(s) seleccionado(s)?', () => $wire.bulkCancel())" wire:loading.attr="disabled" class="btn-sm btn-cancel" style="height: 34px; display: inline-flex; align-items: center; gap: 8px;">
                    <span wire:loading.remove wire:target="bulkCancel" style="display: inline-flex; align-items: center;">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </span>
                    <span wire:loading wire:target="bulkCancel" class="spinner-sm" style="display: none;"></span>
                    Cancelar todos
                </button>
                <button type="button" x-on:click="swalConfirm('Eliminar Bilhetes', 'Eliminar os bilhetes cancelados seleccionados? Esta acção os enviará para a lixeira (Soft Delete) e poderá ser auditada ou revertida.', () => $wire.bulkDelete())" wire:loading.attr="disabled" class="btn-sm btn-cancel" style="height: 34px; display: inline-flex; align-items: center; gap: 8px; background: rgba(239, 68, 68, 0.14); color: #EF4444; border-color: rgba(239, 68, 68, 0.3);">
                    <span wire:loading.remove wire:target="bulkDelete" style="display: inline-flex; align-items: center;">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </span>
                    <span wire:loading wire:target="bulkDelete" class="spinner-sm" style="display: none; border-color: rgba(239, 68, 68, 0.3); border-right-color: #EF4444;"></span>
                    Eliminar cancelados
                </button>
            @endif
            <button type="button" x-on:click="selectedIds = []" class="btn-sm" style="color: var(--text-muted); height: 34px; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="x-circle" class="w-4 h-4"></i> Limpar
            </button>
        </div>
    </div>

    {{-- TABLE VIEW --}}
    @if($viewMode === 'table')
    <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px; cursor: default;">
                            <input type="checkbox" x-on:click="toggleAll()" :checked="isAllSelected()" style="accent-color: var(--gold); cursor: pointer;">
                        </th>
                        <th wire:click="sortBy('ticket_code')">
                            Código
                            @if ($sortBy === 'ticket_code') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th wire:click="sortBy('buyer_name')">
                            Nome
                            @if ($sortBy === 'buyer_name') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th wire:click="sortBy('buyer_phone')">
                            Telefone
                            @if ($sortBy === 'buyer_phone') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
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
                        <th wire:click="sortBy('ticket_mode')">
                            Origem
                            @if ($sortBy === 'ticket_mode') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
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
                        <tr :style="selectedIds.includes('{{ $ticket->id }}') ? 'background: rgba(212,160,23,0.06);' : ''">
                            <td>
                                <input type="checkbox" x-model="selectedIds" value="{{ $ticket->id }}" style="accent-color: var(--gold); cursor: pointer;">
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
                                @if ($ticket->trashed())
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.14); color: #EF4444; border: 1px solid rgba(239, 68, 68, 0.25);">Eliminado</span>
                                @else
                                    <span class="badge badge-{{ $ticket->getStatusColor() }}">{{ $ticket->getStatusLabel() }}</span>
                                @endif
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
                            <td>
                                <span style="font-size: 0.85rem; font-weight: 500; color: {{ $ticket->ticket_mode === 'quick_sale' ? '#10B981' : '#3B82F6' }};">
                                    {{ $ticket->ticket_mode === 'quick_sale' ? 'Venda Rápida' : 'Online' }}
                                </span>
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
                                            @if ($ticket->trashed())
                                                <button type="button" x-on:click="swalConfirm('Restaurar Bilhete', 'Desejas restaurar o bilhete {{ $ticket->ticket_code }}?', () => $wire.restoreTicket('{{ $ticket->id }}'))" wire:loading.attr="disabled" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #10B981; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                                    <i wire:loading.remove wire:target="restoreTicket('{{ $ticket->id }}')" data-lucide="rotate-ccw" class="w-4 h-4" style="color: #10B981;"></i>
                                                    <span wire:loading wire:target="restoreTicket('{{ $ticket->id }}')" class="spinner-sm" style="border-color: rgba(16, 185, 129, 0.3); border-right-color: #10B981; margin: 0; display: none;"></span>
                                                    Restaurar
                                                </button>
                                            @else
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
                                                    <button type="button" x-on:click="swalConfirm('Confirmar Bilhete', 'Confirmar o bilhete {{ $ticket->ticket_code }}?', () => $wire.confirmTicket('{{ $ticket->id }}'))" wire:loading.attr="disabled" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #10B981; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                                        <i wire:loading.remove wire:target="confirmTicket('{{ $ticket->id }}')" data-lucide="check" class="w-4 h-4" style="color: #10B981;"></i>
                                                        <span wire:loading wire:target="confirmTicket('{{ $ticket->id }}')" class="spinner-sm" style="border-color: rgba(16, 185, 129, 0.3); border-right-color: #10B981; margin: 0; display: none;"></span>
                                                        Confirmar
                                                    </button>
                                                @endif

                                                @if ($ticket->isConfirmed())
                                                    <button type="button" x-on:click="swalConfirm('Validar Entrada', 'Validar entrada do bilhete {{ $ticket->ticket_code }}?', () => $wire.validateTicket('{{ $ticket->id }}'))" wire:loading.attr="disabled" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #10B981; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                                        <i wire:loading.remove wire:target="validateTicket('{{ $ticket->id }}')" data-lucide="scan-line" class="w-4 h-4" style="color: #10B981;"></i>
                                                        <span wire:loading wire:target="validateTicket('{{ $ticket->id }}')" class="spinner-sm" style="border-color: rgba(16, 185, 129, 0.3); border-right-color: #10B981; margin: 0; display: none;"></span>
                                                        Validar Entrada
                                                    </button>
                                                @endif

                                                @if ($ticket->isConfirmed() || $ticket->isUsed())
                                                    <button type="button" x-on:click="swalConfirm('Reenviar Bilhete', 'Reenviar o bilhete {{ $ticket->ticket_code }}?', () => $wire.resendTicket('{{ $ticket->id }}'))" wire:loading.attr="disabled" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #3B82F6; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                                        <i wire:loading.remove wire:target="resendTicket('{{ $ticket->id }}')" data-lucide="send" class="w-4 h-4" style="color: #3B82F6;"></i>
                                                        <span wire:loading wire:target="resendTicket('{{ $ticket->id }}')" class="spinner-sm" style="border-color: rgba(59, 130, 246, 0.3); border-right-color: #3B82F6; margin: 0; display: none;"></span>
                                                        Reenviar
                                                    </button>
                                                @endif

                                                @if (!$ticket->isUsed() && !$ticket->isCancelled())
                                                    <button type="button" x-on:click="swalConfirm('Cancelar Bilhete', 'Cancelar o bilhete {{ $ticket->ticket_code }}?', () => $wire.cancelTicket('{{ $ticket->id }}'))" wire:loading.attr="disabled" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #F59E0B; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                                        <i wire:loading.remove wire:target="cancelTicket('{{ $ticket->id }}')" data-lucide="x" class="w-4 h-4" style="color: #F59E0B;"></i>
                                                        <span wire:loading wire:target="cancelTicket('{{ $ticket->id }}')" class="spinner-sm" style="border-color: rgba(245, 158, 11, 0.3); border-right-color: #F59E0B; margin: 0; display: none;"></span>
                                                        Cancelar
                                                    </button>
                                                @endif

                                                <div style="height: 1px; background: var(--dark-border); margin: 4px 0;"></div>

                                                <button type="button" x-on:click="swalConfirm('ELIMINAR Bilhete', 'Tens a certeza que desejas ELIMINAR o bilhete {{ $ticket->ticket_code }}? O bilhete será enviado para a lixeira (Soft Delete) e a acção poderá ser auditada ou revertida.', () => $wire.deleteTicket('{{ $ticket->id }}'))" wire:loading.attr="disabled" class="dropdown-item text-danger" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #EF4444; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                                    <i wire:loading.remove wire:target="deleteTicket('{{ $ticket->id }}')" data-lucide="trash-2" class="w-4 h-4" style="color: #EF4444;"></i>
                                                    <span wire:loading wire:target="deleteTicket('{{ $ticket->id }}')" class="spinner-sm" style="border-color: rgba(239, 68, 68, 0.3); border-right-color: #EF4444; margin: 0; display: none;"></span>
                                                    Eliminar
                                                </button>
                                            @endif
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
            <div :style="selectedIds.includes('{{ $ticket->id }}') ? 'border: 1px solid rgba(212,160,23,0.5); border-radius: 12px; padding: 20px; background: rgba(212,160,23,0.06); transition: all 0.2s;' : 'border: 1px solid var(--dark-border); border-radius: 12px; padding: 20px; background: var(--dark-card); transition: all 0.2s;'">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" x-model="selectedIds" value="{{ $ticket->id }}" style="accent-color: var(--gold); cursor: pointer;">
                        <div>
                            <span class="mono" style="color: var(--gold); font-weight: 600; font-size: 0.95rem;">{{ $ticket->ticket_code }}</span>
                            <div style="color: var(--text-primary); font-weight: 500; font-size: 1.05rem; margin-top: 4px;">{{ $ticket->buyer_name }}</div>
                        </div>
                    </div>
                    @if ($ticket->trashed())
                        <span class="badge" style="background: rgba(239, 68, 68, 0.14); color: #EF4444; border: 1px solid rgba(239, 68, 68, 0.25);">Eliminado</span>
                    @else
                        <span class="badge badge-{{ $ticket->getStatusColor() }}">{{ $ticket->getStatusLabel() }}</span>
                    @endif
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
                        <span style="color: var(--text-muted); display: block; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 2px;">Origem</span>
                        <span style="color: {{ $ticket->ticket_mode === 'quick_sale' ? '#10B981' : '#3B82F6' }}; font-weight: 500;">{{ $ticket->ticket_mode === 'quick_sale' ? 'Venda Rápida' : 'Online' }}</span>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 2px;">Data</span>
                        <span>{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <div style="display: flex; gap: 8px; flex-wrap: wrap; width: 100%;">
                    @if ($ticket->trashed())
                        <button type="button" x-on:click="swalConfirm('Restaurar Bilhete', 'Desejas restaurar o bilhete {{ $ticket->ticket_code }}?', () => $wire.restoreTicket('{{ $ticket->id }}'))" wire:loading.attr="disabled" class="btn-sm" style="flex: 1; justify-content: center; background: rgba(16, 185, 129, 0.14); color: #10B981; border-color: rgba(16, 185, 129, 0.3); height: 38px; display: inline-flex; align-items: center; gap: 8px;">
                            <i wire:loading.remove wire:target="restoreTicket('{{ $ticket->id }}')" data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            <span wire:loading wire:target="restoreTicket('{{ $ticket->id }}')" class="spinner-sm" style="border-color: rgba(16, 185, 129, 0.3); border-right-color: #10B981; margin: 0; display: none;"></span>
                            Restaurar Bilhete
                        </button>
                    @else
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
                                    <button type="button" x-on:click="swalConfirm('Confirmar Bilhete', 'Confirmar o bilhete {{ $ticket->ticket_code }}?', () => $wire.confirmTicket('{{ $ticket->id }}'))" wire:loading.attr="disabled" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #10B981; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                        <i wire:loading.remove wire:target="confirmTicket('{{ $ticket->id }}')" data-lucide="check" class="w-4 h-4" style="color: #10B981;"></i>
                                        <span wire:loading wire:target="confirmTicket('{{ $ticket->id }}')" class="spinner-sm" style="border-color: rgba(16, 185, 129, 0.3); border-right-color: #10B981; margin: 0; display: none;"></span>
                                        Confirmar
                                    </button>
                                @endif

                                @if ($ticket->isConfirmed())
                                    <button type="button" x-on:click="swalConfirm('Validar Entrada', 'Validar entrada do bilhete {{ $ticket->ticket_code }}?', () => $wire.validateTicket('{{ $ticket->id }}'))" wire:loading.attr="disabled" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #10B981; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                        <i wire:loading.remove wire:target="validateTicket('{{ $ticket->id }}')" data-lucide="scan-line" class="w-4 h-4" style="color: #10B981;"></i>
                                        <span wire:loading wire:target="validateTicket('{{ $ticket->id }}')" class="spinner-sm" style="border-color: rgba(16, 185, 129, 0.3); border-right-color: #10B981; margin: 0; display: none;"></span>
                                        Validar Entrada
                                    </button>
                                @endif

                                @if ($ticket->isConfirmed() || $ticket->isUsed())
                                    <button type="button" x-on:click="swalConfirm('Reenviar Bilhete', 'Reenviar o bilhete {{ $ticket->ticket_code }}?', () => $wire.resendTicket('{{ $ticket->id }}'))" wire:loading.attr="disabled" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #3B82F6; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                        <i wire:loading.remove wire:target="resendTicket('{{ $ticket->id }}')" data-lucide="send" class="w-4 h-4" style="color: #3B82F6;"></i>
                                        <span wire:loading wire:target="resendTicket('{{ $ticket->id }}')" class="spinner-sm" style="border-color: rgba(59, 130, 246, 0.3); border-right-color: #3B82F6; margin: 0; display: none;"></span>
                                        Reenviar
                                    </button>
                                @endif

                                @if (!$ticket->isUsed() && !$ticket->isCancelled())
                                    <button type="button" x-on:click="swalConfirm('Cancelar Bilhete', 'Cancelar o bilhete {{ $ticket->ticket_code }}?', () => $wire.cancelTicket('{{ $ticket->id }}'))" wire:loading.attr="disabled" class="dropdown-item" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #F59E0B; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                        <i wire:loading.remove wire:target="cancelTicket('{{ $ticket->id }}')" data-lucide="x" class="w-4 h-4" style="color: #F59E0B;"></i>
                                        <span wire:loading wire:target="cancelTicket('{{ $ticket->id }}')" class="spinner-sm" style="border-color: rgba(245, 158, 11, 0.3); border-right-color: #F59E0B; margin: 0; display: none;"></span>
                                        Cancelar
                                    </button>
                                @endif

                                <div style="height: 1px; background: var(--dark-border); margin: 4px 0;"></div>

                                <button type="button" x-on:click="swalConfirm('ELIMINAR Bilhete', 'Tens a certeza que desejas ELIMINAR o bilhete {{ $ticket->ticket_code }}? O bilhete será enviado para a lixeira (Soft Delete) e a acção poderá ser auditada ou revertida.', () => $wire.deleteTicket('{{ $ticket->id }}'))" wire:loading.attr="disabled" class="dropdown-item text-danger" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; color: #EF4444; font-size: 0.85rem; border: none; background: transparent; cursor: pointer; text-align: left; width: 100%; border-radius: 6px; transition: all 0.2s;">
                                    <i wire:loading.remove wire:target="deleteTicket('{{ $ticket->id }}')" data-lucide="trash-2" class="w-4 h-4" style="color: #EF4444;"></i>
                                    <span wire:loading wire:target="deleteTicket('{{ $ticket->id }}')" class="spinner-sm" style="border-color: rgba(239, 68, 68, 0.3); border-right-color: #EF4444; margin: 0; display: none;"></span>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    @endif
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
                    <label class="form-label">Lote do Bilhete</label>
                    <select wire:model="editingBatchId" class="form-input" style="cursor: pointer;">
                        <option value="">Seleccionar lote...</option>
                        @foreach($this->batches as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->display_name }} ({{ number_format($batch->price, 0, ',', '.') }} MT)</option>
                        @endforeach
                    </select>
                    @error('editingBatchId') <p class="form-error">{{ $message }}</p> @enderror
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
        .loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(13,11,7,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(2px);
        }
        .spinner-sm {
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s linear infinite;
            display: inline-block;
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
