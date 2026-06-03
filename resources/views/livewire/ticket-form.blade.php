<div id="ticket-form-container" x-data="{ 
    showForm: false,
    ticketType: $wire.entangle('ticket_type'),
    quantity: $wire.entangle('quantity'),
    prices: {
        @foreach ($event->getTicketTypePrices() as $key => $type)
            '{{ $key }}': {{ $type['price'] ?? 0 }},
        @endforeach
    },
    get total() {
        const price = this.prices[this.ticketType] || 0;
        const qty = parseInt(this.quantity) || 1;
        return new Intl.NumberFormat('pt-MZ').format(price * qty);
    }
}" @open-ticket-modal.window="showForm = true; ticketType = $event.detail.type; setTimeout(() => $el.scrollIntoView({behavior: 'smooth', block: 'start'}), 50)" style="margin-top: 40px;">
    
        <div x-show="showForm" x-transition style="max-width: 640px; margin: 0 auto; position: relative;">

            @if ($showSuccess && $createdTickets)
                <!-- Success Modal -->
                <div style="background: var(--dark-card); border: 2px solid var(--gold); border-radius: 16px; padding: 32px; text-align: center;">
                    <div style="width: 64px; height: 64px; border-radius: 18px; margin: 0 auto 16px; display: grid; place-items: center; background: rgba(212,175,55,0.12); color: var(--gold);">
                        <i data-lucide="check-circle" class="w-10 h-10"></i>
                    </div>
                    <h3 style="font-size: 2rem; color: var(--gold); margin-bottom: 8px;">BILHETE REGISTADO!</h3>
                    <p style="color: var(--text-secondary); margin-bottom: 24px;">
                        O seu pedido foi registado com sucesso! Receberá agora uma mensagem (email/WhatsApp) a confirmar a sua reserva. Após o pagamento ser validado, o bilhete com QR Code será enviado pelos mesmos meios.
                    </p>

                    @foreach ($createdTickets as $ticket)
                        <div style="background: var(--dark-bg); border: 1px solid var(--dark-border); border-radius: 12px; padding: 20px; margin-bottom: 12px;">
                            <p style="color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Código do Bilhete</p>
                            <p class="mono" style="font-size: 1.6rem; color: var(--gold); font-weight: 700; margin-bottom: 8px;">{{ $ticket['code'] }}</p>
                            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                                {{ $ticket['name'] }} — {{ $ticket['type'] }}
                            </p>
                        </div>
                    @endforeach

                    <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-top: 24px;">
                        <a href="https://wa.me/?text=O%20meu%20bilhete%20para%20o%20Concerto%20Ren%C3%BAncia%3A%20{{ $createdTickets[0]['code'] }}" target="_blank" class="btn-outline" style="color: #25D366; border-color: rgba(37, 211, 102, 0.3);">
                            <i data-lucide="message-circle" class="w-4 h-4"></i> Partilhar no WhatsApp
                        </a>
                        <button type="button" wire:click="resendTicket" class="btn-outline" style="display: inline-flex; align-items: center; gap: 8px;">
                            <i data-lucide="send" class="w-4 h-4"></i> Reenviar
                        </button>
                        <button type="button" wire:click="resetForm" class="btn-gold" style="font-size: 1rem; padding: 12px 24px;">
                            <i data-lucide="ticket" class="w-4 h-4"></i> Comprar Outro
                        </button>
                    </div>
                </div>
            @else
                <!-- Purchase Form -->
                <div class="glass-card" style="border-color: rgba(212, 175, 55, 0.25);">
                    <h3 style="font-size: 2rem; color: var(--gold); margin-bottom: 24px; text-align: center; padding-right: 20px;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; gap: 10px;"><i data-lucide="pen-line" class="w-7 h-7"></i> COMPRAR BILHETE</span>
                    </h3>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 22px;">
                @foreach (['Escolha', 'Dados', 'Pagamento', 'Confirmação'] as $step)
                    <div style="border: 1px solid rgba(212,175,55,0.22); border-radius: 8px; padding: 8px 6px; text-align: center; color: var(--text-secondary); font-size: 0.72rem; font-weight: 700; text-transform: uppercase; background: rgba(255,255,255,0.03);">{{ $step }}</div>
                @endforeach
            </div>

            <form wire:submit="submit">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <!-- Nome -->
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label" for="buyer_name">Nome Completo *</label>
                        <input type="text" id="buyer_name" wire:model="buyer_name" class="form-input" placeholder="Ex: João Mateus">
                        @error('buyer_name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <!-- Telefone -->
                    <div class="form-group">
                        <label class="form-label" for="buyer_phone">Telemóvel (WhatsApp recomendado) *</label>
                        <input type="tel" id="buyer_phone" wire:model="buyer_phone" class="form-input" placeholder="Ex: 841234567">
                        @error('buyer_phone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label" for="buyer_email">Email (Onde receberá o bilhete)</label>
                        <input type="email" id="buyer_email" wire:model="buyer_email" class="form-input" placeholder="email@exemplo.com">
                        @error('buyer_email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tipo de Bilhete -->
                    <div class="form-group">
                        <label class="form-label" for="ticket_type">Tipo de Bilhete *</label>
                        <select id="ticket_type" x-model="ticketType" class="form-select">
                            @foreach ($event->getTicketTypePrices() as $key => $type)
                                @if ($key !== 'free')
                                    <option value="{{ $key }}">{{ $type['name'] ?? '' }} — {{ number_format($type['price'] ?? 0, 0, ',', '.') }} MT</option>
                                @endif
                            @endforeach
                        </select>
                        @error('ticket_type') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <!-- Quantidade -->
                    <div class="form-group">
                        <label class="form-label" for="quantity">Quantidade</label>
                        <select id="quantity" x-model="quantity" class="form-select">
                            @for ($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Método de Pagamento -->
                    <div class="form-group">
                        <label class="form-label" for="payment_method">Método de Pagamento *</label>
                        <select id="payment_method" wire:model="payment_method" class="form-select">
                            <option value="mpesa">M-Pesa</option>
                            <option value="emola">e-Mola</option>
                            <option value="cash">Dinheiro / Presencial</option>
                        </select>
                    </div>

                    <!-- Referência -->
                    <div class="form-group">
                        <label class="form-label" for="payment_ref">Referência do Pagamento *</label>
                        <input type="text" id="payment_ref" wire:model="payment_ref" class="form-input" placeholder="ID da transacção M-Pesa">
                        @error('payment_ref') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Total -->
                <div style="background: var(--dark-bg); border: 1px solid var(--dark-border); border-radius: 12px; padding: 20px; margin: 20px 0; text-align: center;">
                    <p style="color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; margin-bottom: 4px;">Total a Pagar</p>
                    <p class="mono" style="font-size: 2.2rem; color: var(--gold); font-weight: 700;">
                        <span x-text="total">0</span> MT
                    </p>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-gold" style="width: 100%; font-size: 1.3rem; padding: 16px;" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submit" style="display: inline-flex; align-items: center; gap: 8px;"><i data-lucide="ticket" class="w-5 h-5"></i> RESERVAR BILHETE</span>
                    <span wire:loading wire:target="submit">
                        <svg style="width: 20px; height: 20px; animation: spin 1s linear infinite;" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="31.416" stroke-dashoffset="10"/>
                        </svg>
                        A PROCESSAR...
                    </span>
                </button>

                <p style="text-align: center; color: var(--text-muted); font-size: 0.8rem; margin-top: 12px;">
                    <i data-lucide="alert-triangle" class="w-4 h-4" style="display: inline-block; vertical-align: text-bottom;"></i> O bilhete fica pendente até a confirmação do pagamento pela equipa.
                </p>
            </form>
        </div>
    @endif
        </div>


    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @media (max-width: 640px) {
            .form-group { grid-column: 1 / -1 !important; }
            div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</div>
