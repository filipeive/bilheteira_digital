<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px;">
        <div>
            <span class="badge badge-gold"><i data-lucide="settings" class="w-4 h-4" style="margin-right: 6px;"></i> Plataforma & Eventos</span>
            <h1 style="font-size: 2.5rem; color: var(--gold); margin-top: 6px;">CONFIGURAÇÕES DO SITE</h1>
            <p style="color: var(--text-secondary);">Gira as configurações globais da plataforma e personalize dados dos eventos.</p>
        </div>
        <a href="{{ route('admin.events.index') }}" class="btn-gold">
            <i data-lucide="calendar" class="w-4 h-4"></i> Gestor Completo de Eventos
        </a>
    </div>

    {{-- Tabs --}}
    <div style="display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap;">
        <button wire:click="$set('activeTab', 'platform')" class="btn-sm" style="{{ $activeTab === 'platform' ? 'background: rgba(212,175,55,0.2); color: var(--gold); border: 1px solid var(--gold);' : 'background: var(--dark-card); color: var(--text-secondary); border: 1px solid var(--dark-border);' }} padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">
            <i data-lucide="globe" class="w-4 h-4"></i> Plataforma & Portal
        </button>
        <button wire:click="$set('activeTab', 'event')" class="btn-sm" style="{{ $activeTab === 'event' ? 'background: rgba(212,175,55,0.2); color: var(--gold); border: 1px solid var(--gold);' : 'background: var(--dark-card); color: var(--text-secondary); border: 1px solid var(--dark-border);' }} padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">
            <i data-lucide="ticket" class="w-4 h-4"></i> Evento Selecionado
        </button>
        <button wire:click="$set('activeTab', 'social')" class="btn-sm" style="{{ $activeTab === 'social' ? 'background: rgba(212,175,55,0.2); color: var(--gold); border: 1px solid var(--gold);' : 'background: var(--dark-card); color: var(--text-secondary); border: 1px solid var(--dark-border);' }} padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">
            <i data-lucide="share-2" class="w-4 h-4"></i> Suporte & Redes Sociais
        </button>
    </div>

    <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 14px; padding: 28px; max-width: 100%;">
        
        {{-- Tab 1: Plataforma & Portal --}}
        @if($activeTab === 'platform')
        <form wire:submit.prevent="savePlatform">
            <h3 style="color: var(--gold); font-size: 1.3rem; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="layout-template" class="w-5 h-5"></i> CONFIGURAÇÕES DA PLATAFORMA (HOMEPAGE)
            </h3>

            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label class="form-label">Nome da Plataforma / Marca</label>
                    <input type="text" wire:model="portal_name" class="form-input" placeholder="Ex: Alpha Bilheteira">
                </div>

                <div>
                    <label class="form-label">Tagline do Topo</label>
                    <input type="text" wire:model="hero_label" class="form-input" placeholder="Ex: A plataforma de bilheteira digital gospel em Moçambique">
                </div>

                <div>
                    <label class="form-label">Título Principal (Hero)</label>
                    <input type="text" wire:model="hero_title" class="form-input" placeholder="Ex: COMPRE O SEU BILHETE ONLINE">
                </div>

                <div>
                    <label class="form-label">Subtítulo / Descrição da Plataforma</label>
                    <textarea wire:model="hero_subtitle" class="form-input" rows="3" placeholder="Descrição resumida do portal..."></textarea>
                </div>

                <div>
                    <label class="form-label">Barra de Comunicado no Topo</label>
                    <input type="text" wire:model="announcement_text" class="form-input" placeholder="Ex: 🎉 Concerto Renúncia 2026 em Quelimane — Bilhetes disponíveis!">
                </div>
            </div>

            <button type="submit" class="btn-gold" style="margin-top: 24px; padding: 12px 24px;">
                <i data-lucide="save" class="w-4 h-4"></i> Guardar Plataforma
            </button>
        </form>
        @endif

        {{-- Tab 2: Evento Selecionado --}}
        @if($activeTab === 'event')
        <form wire:submit.prevent="saveEvent">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                <h3 style="color: var(--gold); font-size: 1.3rem; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="calendar" class="w-5 h-5"></i> DETALHES DO EVENTO
                </h3>

                {{-- Event Selector --}}
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label class="form-label" style="margin: 0; white-space: nowrap;">Selecionar Evento:</label>
                    <select wire:model.live="selectedEventId" class="form-input" style="cursor: pointer; min-width: 220px; font-weight: 600; color: var(--gold); border-color: rgba(212,175,55,0.3);">
                        @foreach($this->events as $ev)
                            <option value="{{ $ev->id }}">{{ $ev->name }} ({{ $ev->province ?? $ev->city }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($selectedEventId)
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div style="grid-column: 1 / -1;">
                    <label class="form-label">Nome do Evento *</label>
                    <input type="text" wire:model="event_name" class="form-input">
                </div>

                <div>
                    <label class="form-label">Data e Hora de Início</label>
                    <input type="datetime-local" wire:model="event_starts_at" class="form-input">
                </div>

                <div>
                    <label class="form-label">Organizador / Promotor</label>
                    <input type="text" wire:model="event_organizer" class="form-input" placeholder="Ex: Alpha Produções">
                </div>

                <div>
                    <label class="form-label">Local / Venue</label>
                    <input type="text" wire:model="event_venue" class="form-input">
                </div>

                <div>
                    <label class="form-label">Cidade / Província</label>
                    <input type="text" wire:model="event_province" class="form-input" placeholder="Ex: Zambézia">
                </div>

                <div style="grid-column: 1 / -1;">
                    <label class="form-label">Descrição Resumida</label>
                    <textarea wire:model="event_description" class="form-input" rows="4"></textarea>
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px; align-items: center;">
                <button type="submit" class="btn-gold" style="padding: 12px 24px;">
                    <i data-lucide="save" class="w-4 h-4"></i> Guardar Evento
                </button>
                <a href="{{ route('admin.events.index') }}" class="btn-outline" style="text-decoration: none;">
                    <i data-lucide="edit-3" class="w-4 h-4"></i> Editar Capa & Lotes no Gestor
                </a>
            </div>
            @else
            <p style="color: var(--text-muted); padding: 20px; text-align: center;">Nenhum evento registado na plataforma.</p>
            @endif
        </form>
        @endif

        {{-- Tab 3: Redes Sociais & Suporte --}}
        @if($activeTab === 'social')
        <form wire:submit.prevent="saveSocial">
            <h3 style="color: var(--gold); font-size: 1.3rem; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="phone-call" class="w-5 h-5"></i> SUPORTE E REDES SOCIAIS
            </h3>

            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label class="form-label">Telefone de Suporte</label>
                        <input type="text" wire:model="support_phone" class="form-input" placeholder="Ex: 87 541 1644">
                    </div>
                    <div>
                        <label class="form-label">WhatsApp de Suporte (com indicativo 258)</label>
                        <input type="text" wire:model="support_whatsapp" class="form-input" placeholder="Ex: 258875411644">
                    </div>
                </div>

                <div>
                    <label class="form-label">Facebook URL</label>
                    <input type="url" wire:model="social_facebook" class="form-input" placeholder="https://facebook.com/...">
                </div>

                <div>
                    <label class="form-label">Instagram URL</label>
                    <input type="url" wire:model="social_instagram" class="form-input" placeholder="https://instagram.com/...">
                </div>

                <div>
                    <label class="form-label">TikTok URL</label>
                    <input type="url" wire:model="social_tiktok" class="form-input" placeholder="https://tiktok.com/@...">
                </div>

                <div>
                    <label class="form-label">YouTube URL</label>
                    <input type="url" wire:model="social_youtube" class="form-input" placeholder="https://youtube.com/...">
                </div>
            </div>

            <button type="submit" class="btn-gold" style="margin-top: 24px; padding: 12px 24px;">
                <i data-lucide="save" class="w-4 h-4"></i> Guardar Suporte & Redes Sociais
            </button>
        </form>
        @endif

    </div>
</div>
