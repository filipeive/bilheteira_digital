<div>
    <h1 style="font-size: 2rem; color: var(--gold); margin-bottom: 24px;">CONFIGURAÇÕES DO SITE</h1>

    {{-- Tabs --}}
    <div style="display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap;">
        <button wire:click="$set('activeTab', 'event')" class="btn-sm" style="{{ $activeTab === 'event' ? 'background: rgba(212,175,55,0.2); color: var(--gold); border: 1px solid var(--gold);' : 'background: var(--dark-card); color: var(--text-secondary); border: 1px solid var(--dark-border);' }} padding: 8px 16px; border-radius: 8px;">
            <i data-lucide="calendar" class="w-4 h-4"></i> Evento
        </button>
        <button wire:click="$set('activeTab', 'social')" class="btn-sm" style="{{ $activeTab === 'social' ? 'background: rgba(212,175,55,0.2); color: var(--gold); border: 1px solid var(--gold);' : 'background: var(--dark-card); color: var(--text-secondary); border: 1px solid var(--dark-border);' }} padding: 8px 16px; border-radius: 8px;">
            <i data-lucide="share-2" class="w-4 h-4"></i> Redes Sociais
        </button>
        <button wire:click="$set('activeTab', 'banner')" class="btn-sm" style="{{ $activeTab === 'banner' ? 'background: rgba(212,175,55,0.2); color: var(--gold); border: 1px solid var(--gold);' : 'background: var(--dark-card); color: var(--text-secondary); border: 1px solid var(--dark-border);' }} padding: 8px 16px; border-radius: 8px;">
            <i data-lucide="image" class="w-4 h-4"></i> Banner
        </button>
    </div>

    <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; padding: 24px; max-width: 700px;">
        {{-- Event tab --}}
        @if($activeTab === 'event')
        <form wire:submit="saveEvent">
            <h3 style="color: var(--gold); font-size: 1.2rem; margin-bottom: 16px;">INFORMAÇÕES DO EVENTO</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div><label class="form-label">Nome do Evento</label><input type="text" wire:model="event_name" class="form-input"></div>
                <div><label class="form-label">Data</label><input type="date" wire:model="event_date" class="form-input"></div>
                <div><label class="form-label">Hora</label><input type="time" wire:model="event_time" class="form-input"></div>
                <div><label class="form-label">Local</label><input type="text" wire:model="event_venue" class="form-input"></div>
                <div><label class="form-label">Cidade</label><input type="text" wire:model="event_city" class="form-input"></div>
                <div><label class="form-label">Contacto 1</label><input type="text" wire:model="event_contact_1" class="form-input"></div>
                <div><label class="form-label">Contacto 2</label><input type="text" wire:model="event_contact_2" class="form-input"></div>
            </div>
            <div style="margin-top: 16px;"><label class="form-label">Descrição</label><textarea wire:model="event_description" class="form-input" rows="3"></textarea></div>
            <button type="submit" class="btn-gold" style="margin-top: 16px;"><i data-lucide="save" class="w-4 h-4"></i> Guardar Evento</button>
        </form>
        @endif

        {{-- Social tab --}}
        @if($activeTab === 'social')
        <form wire:submit="saveSocial">
            <h3 style="color: var(--gold); font-size: 1.2rem; margin-bottom: 16px;">REDES SOCIAIS</h3>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div><label class="form-label">Facebook URL</label><input type="url" wire:model="social_facebook" class="form-input" placeholder="https://facebook.com/..."></div>
                <div><label class="form-label">Instagram URL</label><input type="url" wire:model="social_instagram" class="form-input" placeholder="https://instagram.com/..."></div>
                <div><label class="form-label">TikTok URL</label><input type="url" wire:model="social_tiktok" class="form-input" placeholder="https://tiktok.com/@..."></div>
                <div><label class="form-label">YouTube URL</label><input type="url" wire:model="social_youtube" class="form-input" placeholder="https://youtube.com/..."></div>
                <div><label class="form-label">WhatsApp Link</label><input type="url" wire:model="social_whatsapp" class="form-input" placeholder="https://wa.me/258..."></div>
            </div>
            <button type="submit" class="btn-gold" style="margin-top: 16px;"><i data-lucide="save" class="w-4 h-4"></i> Guardar Redes Sociais</button>
        </form>
        @endif

        {{-- Banner tab --}}
        @if($activeTab === 'banner')
        <form wire:submit="saveBanner">
            <h3 style="color: var(--gold); font-size: 1.2rem; margin-bottom: 16px;">BANNER DO SITE</h3>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div><label class="form-label">Título</label><input type="text" wire:model="banner_title" class="form-input"></div>
                <div><label class="form-label">Subtítulo</label><input type="text" wire:model="banner_subtitle" class="form-input"></div>
                <div><label class="form-label">Imagem de Banner</label><input type="file" wire:model="banner_image" accept="image/*" class="form-input" style="padding: 8px;"></div>
            </div>
            <button type="submit" class="btn-gold" style="margin-top: 16px;"><i data-lucide="save" class="w-4 h-4"></i> Guardar Banner</button>
        </form>
        @endif
    </div>
</div>
