<div>
    <h1 style="font-size: 2rem; color: var(--gold); margin-bottom: 24px;">GESTÃO DE LOTES</h1>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;" class="batch-grid">
        {{-- Form --}}
        <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; padding: 24px;">
            <h3 style="color: var(--gold); font-size: 1.2rem; margin-bottom: 16px;">{{ $editingId ? 'EDITAR LOTE' : 'NOVO LOTE' }}</h3>
            <form wire:submit="save">
                <div style="margin-bottom: 12px;"><label class="form-label">Nome</label><input type="text" wire:model="name" class="form-input" placeholder="Ex: Bilhete Promocional"></div>
                <div style="margin-bottom: 12px;"><label class="form-label">Descrição</label><textarea wire:model="description" class="form-input" rows="2"></textarea></div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                    <div>
                        <label class="form-label">Tipo</label>
                        <input type="text" wire:model="ticket_type" list="ticket-types" class="form-input" placeholder="Selecione ou digite...">
                        <datalist id="ticket-types">
                            <option value="promotional">Promocional</option>
                            <option value="second_lot">2º Lote</option>
                            <option value="gate">No Portão</option>
                            <option value="vip">VIP</option>
                            <option value="vip_promotional">VIP Promocional</option>
                            <option value="vip_second_lot">VIP 2º Lote</option>
                            <option value="free">Gratuito</option>
                            <option value="child">Criança</option>
                        </datalist>
                    </div>
                    <div><label class="form-label">Preço (MZN)</label><input type="number" wire:model="price" class="form-input" min="0"></div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                    <div><label class="form-label">Quantidade</label><input type="number" wire:model="quantity" class="form-input" min="1"></div>
                    <div><label class="form-label">Ordem</label><input type="number" wire:model="sort_order" class="form-input" min="0"></div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                    <div><label class="form-label">Início</label><input type="datetime-local" wire:model="starts_at" class="form-input"></div>
                    <div><label class="form-label">Fim</label><input type="datetime-local" wire:model="ends_at" class="form-input"></div>
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: var(--text-secondary);">
                        <input type="checkbox" wire:model="is_active"> Lote activo
                    </label>
                </div>
                @foreach($errors->all() as $error)<p class="form-error">{{ $error }}</p>@endforeach
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn-gold"><i data-lucide="save" class="w-4 h-4"></i> {{ $editingId ? 'Actualizar' : 'Criar Lote' }}</button>
                    @if($editingId)<button type="button" wire:click="$set('editingId', null)" class="btn-outline">Cancelar</button>@endif
                </div>
            </form>
        </div>

        {{-- List --}}
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @forelse($batches as $batch)
            <div style="background: var(--dark-card); border: 1px solid {{ $batch->is_active ? 'rgba(212,175,55,0.2)' : 'var(--dark-border)' }}; border-radius: 10px; padding: 16px; {{ !$batch->is_active ? 'opacity: 0.6;' : '' }}">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <h4 style="font-size: 1.1rem; color: var(--gold);">{{ $batch->name }}</h4>
                        <p style="font-size: 0.8rem; color: var(--text-muted);">{{ $batch->ticket_type }} · {{ $batch->price }} MZN</p>
                    </div>
                    @if($batch->is_active)<span class="badge badge-green">Activo</span>@else<span class="badge badge-red">Inactivo</span>@endif
                </div>
                {{-- Progress bar --}}
                <div style="margin-top: 10px;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px;">
                        <span>{{ $batch->sold }}/{{ $batch->quantity }} vendidos</span>
                        <span>{{ $batch->percentage_sold }}%</span>
                    </div>
                    <div style="height: 6px; background: rgba(212,175,55,0.1); border-radius: 3px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $batch->percentage_sold }}%; background: linear-gradient(90deg, var(--gold), var(--gold-light)); border-radius: 3px; transition: width 0.3s;"></div>
                    </div>
                </div>
                <div style="display: flex; gap: 6px; margin-top: 10px;">
                    <button wire:click="edit({{ $batch->id }})" class="btn-sm btn-confirm"><i data-lucide="pencil" class="w-3 h-3"></i></button>
                    <button wire:click="toggleActive({{ $batch->id }})" class="btn-sm" style="background: rgba(245,158,11,0.15); color: #FBBF24; border: 1px solid rgba(245,158,11,0.3);"><i data-lucide="{{ $batch->is_active ? 'pause' : 'play' }}" class="w-3 h-3"></i></button>
                    <button wire:click="delete({{ $batch->id }})" wire:confirm="Eliminar este lote?" class="btn-sm btn-cancel"><i data-lucide="trash-2" class="w-3 h-3"></i></button>
                </div>
            </div>
            @empty
            <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 10px; padding: 40px; text-align: center; color: var(--text-muted);">
                <i data-lucide="layers" class="w-12 h-12" style="margin-bottom: 8px; opacity: 0.3;"></i>
                <p>Nenhum lote criado ainda.</p>
            </div>
            @endforelse
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            .batch-grid { grid-template-columns: 1fr !important; }
        }
    </style>
</div>
