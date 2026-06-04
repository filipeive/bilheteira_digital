<div class="w-full" style="margin: 0 auto; max-width: 800px;">
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 2.5rem; color: var(--gold);">VENDA MANUAL</h1>
        <p style="color: var(--text-muted);">Registar bilhetes presenciais ou cortesias</p>
    </div>

    @if ($showSuccess)
        <div style="background: var(--dark-card); border: 2px solid #10B981; border-radius: 12px; padding: 32px; text-align: center;">
            <div style="color: #34D399; margin-bottom: 12px;"><i data-lucide="check-circle" class="w-12 h-12" style="margin: 0 auto;"></i></div>
            <h3 style="font-size: 1.8rem; color: #34D399; margin-bottom: 8px;">BILHETE CRIADO!</h3>
            <p class="mono" style="font-size: 1.4rem; color: var(--gold); margin-bottom: 16px;">{{ $lastTicketCode }}</p>
            <p style="color: var(--text-secondary); margin-bottom: 24px;">{{ $quantity }} bilhete(s) confirmado(s) com sucesso.</p>
            <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                <div style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: center;">
                    @if(count($lastTicketIds) === 1)
                        <a href="https://wa.me/?text={{ urlencode('Aqui está o bilhete para o Concerto Renúncia: ' . URL::signedRoute('tickets.download', $lastTicketIds[0])) }}" target="_blank" class="btn-outline" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; color: #25D366; border-color: rgba(37, 211, 102, 0.3);">
                            <i data-lucide="message-circle" class="w-4 h-4"></i> WHATSAPP
                        </a>
                        <a href="{{ route('admin.tickets.download.png', $lastTicketIds[0]) }}" target="_blank" class="btn-outline" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                            <i data-lucide="image" class="w-4 h-4"></i> PNG
                        </a>
                        <a href="{{ route('admin.tickets.download', $lastTicketIds[0]) }}" target="_blank" class="btn-gold" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                            <i data-lucide="download" class="w-4 h-4"></i> PDF
                        </a>
                    @else
                        <a href="{{ route('admin.tickets.bulk_download', ['ids' => implode(',', $lastTicketIds)]) }}" target="_blank" class="btn-gold" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                            <i data-lucide="download" class="w-4 h-4"></i> BAIXAR TODOS (ZIP)
                        </a>
                    @endif
                </div>
                <button wire:click="resendTicket" class="btn-outline" style="display: inline-flex; align-items: center; gap: 8px;">
                    <i data-lucide="send" class="w-4 h-4"></i> REENVIAR
                </button>
                <button wire:click="resetForm" class="btn-outline" style="color: var(--text-secondary); border-color: var(--dark-border); padding: 12px 24px; border-radius: 8px; font-weight: 700; cursor: pointer;">
                    <i data-lucide="plus" class="w-4 h-4"></i> CRIAR OUTRO
                </button>
            </div>
        </div>
    @else
        <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; padding: 24px;">
            <form wire:submit="submit">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div style="grid-column: 1 / -1;">
                        <label class="form-label">Nome *</label>
                        <input type="text" wire:model="buyer_name" class="form-input" placeholder="Nome do comprador">
                        @error('buyer_name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Telemóvel *</label>
                        <input type="tel" wire:model="buyer_phone" class="form-input" placeholder="841234567">
                        @error('buyer_phone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" wire:model="buyer_email" class="form-input" placeholder="Opcional">
                    </div>

                    <div>
                        <label class="form-label">Tipo</label>
                        <select wire:model="ticket_type" class="form-input" style="cursor: pointer;">
                            <option value="promotional">Promocional — 500 MT</option>
                            <option value="second_lot">2º Lote — 750 MT</option>
                            <option value="gate">No Portão — 1.000 MT</option>
                            <option value="vip_promotional">VIP 1º Lote — 1.000 MT</option>
                            <option value="vip_second_lot">VIP 2º Lote — 1.500 MT</option>
                            <option value="vip">VIP No Portão — 2.000 MT</option>
                            <option value="free">Gratuito / Cortesia</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Quantidade</label>
                        <select wire:model="quantity" class="form-input" style="cursor: pointer;">
                            @for ($i = 1; $i <= 20; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Pagamento</label>
                        <select wire:model="payment_method" class="form-input" style="cursor: pointer;">
                            <option value="cash">Dinheiro</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="emola">e-Mola</option>
                            <option value="free">Gratuito</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Referência</label>
                        <input type="text" wire:model="payment_ref" class="form-input" placeholder="Opcional">
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label class="form-label">Notas</label>
                        <input type="text" wire:model="notes" class="form-input" placeholder="Ex: Cortesia do organizador">
                    </div>
                </div>

                <button type="submit" class="btn-gold" style="width: 100%; margin-top: 20px; font-size: 1.1rem; padding: 14px;" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submit" style="display: inline-flex; align-items: center; gap: 8px;"><i data-lucide="pen-line" class="w-5 h-5"></i> REGISTAR BILHETE</span>
                    <span wire:loading wire:target="submit">A PROCESSAR...</span>
                </button>
            </form>
        </div>
    @endif
</div>

<style>
    @media (max-width: 640px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>