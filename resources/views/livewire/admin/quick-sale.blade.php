<div>
    <h1 style="font-size: 2rem; color: var(--gold); margin-bottom: 24px;">VENDA RÁPIDA</h1>

    @if($showSuccess)
        {{-- Success screen --}}
        <div style="background: var(--dark-card); border: 1px solid rgba(16,185,129,0.3); border-radius: 12px; padding: 32px; max-width: 500px; text-align: center;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i data-lucide="check" class="w-8 h-8" style="color: #34D399;"></i>
            </div>
            <h2 style="font-size: 1.5rem; color: #34D399; margin-bottom: 16px;">BILHETES GERADOS</h2>
            <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px;">
                @foreach($createdTickets as $t)
                <div style="background: var(--dark-bg); border: 1px solid var(--dark-border); border-radius: 8px; padding: 12px; display: flex; justify-content: space-between; align-items: center;">
                    <span class="mono" style="color: var(--gold); font-size: 0.9rem;">{{ $t['code'] }}</span>
                    <span class="badge badge-gold">{{ $t['type'] }}</span>
                </div>
                @endforeach
            </div>
            <div style="display: flex; gap: 8px; flex-direction: column; align-items: center;">
                <a href="{{ route('admin.tickets.bulk_download', ['ids' => collect($createdTickets)->pluck('id')->join(',')]) }}" class="btn-outline" style="width: 100%; display: flex; justify-content: center; gap: 8px;"><i data-lucide="download" class="w-4 h-4"></i> Baixar Todos (ZIP)</a>
                <button wire:click="newSale" class="btn-gold" style="width: 100%;"><i data-lucide="plus" class="w-4 h-4"></i> Nova Venda</button>
            </div>
        </div>
    @else
        {{-- Sale form --}}
        <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; padding: 24px; max-width: 500px;">
            <form wire:submit="sale">
                {{-- Batch selection --}}
                <div style="margin-bottom: 16px;">
                    <label class="form-label">Lote</label>
                    <select wire:model="batchId" class="form-select">
                        <option value="0">— Seleccione um lote —</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->name }} — {{ $batch->price }} MZN ({{ $batch->available }} disp.)</option>
                        @endforeach
                    </select>
                    @error('batchId') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                    <div>
                        <label class="form-label">Quantidade</label>
                        <input type="number" wire:model="quantity" class="form-input" min="1" max="20">
                        @error('quantity') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Pagamento</label>
                        <select wire:model="payment_method" class="form-select">
                            <option value="cash">Dinheiro</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="emola">e-Mola</option>
                            <option value="bank_transfer">Transferência</option>
                            <option value="free">Cortesia</option>
                        </select>
                    </div>
                </div>

                {{-- Toggle quick mode --}}
                <div style="margin-bottom: 16px; padding: 12px; background: var(--dark-bg); border-radius: 8px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" wire:model.live="isQuickMode"> Modo rápido (sem dados pessoais)
                    </label>
                </div>

                @if(!$isQuickMode)
                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px;">
                    <div><label class="form-label">Nome do Comprador</label><input type="text" wire:model="buyer_name" class="form-input" placeholder="Nome completo"></div>
                    <div><label class="form-label">Telefone</label><input type="text" wire:model="buyer_phone" class="form-input" placeholder="+258 84 xxx xxxx"></div>
                    <div><label class="form-label">Email</label><input type="email" wire:model="buyer_email" class="form-input" placeholder="email@exemplo.com"></div>
                </div>
                @endif

                <div style="margin-bottom: 20px;">
                    <label class="form-label">Notas (opcional)</label>
                    <input type="text" wire:model="notes" class="form-input" placeholder="Observações...">
                </div>

                <button type="submit" class="btn-gold" style="width: 100%; padding: 14px; font-size: 1.1rem;">
                    <i data-lucide="shopping-cart" class="w-5 h-5"></i> GERAR BILHETE(S)
                </button>
            </form>
        </div>
    @endif
</div>
