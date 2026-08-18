<x-admin-layout title="Personalizar Plataforma & Site">
    <div class="admin-hero" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <span class="badge badge-gold"><i data-lucide="globe" class="w-4 h-4" style="margin-right: 6px;"></i> Plataforma & Site</span>
            <h1 style="font-size: clamp(2.2rem, 5vw, 3.6rem); color: var(--gold); margin-top: 10px;">PERSONALIZAR SITE</h1>
            <p style="color: var(--text-secondary); max-width: 680px;">Altere os textos da página inicial, contactos de suporte, redes sociais e imagens de destaque da plataforma.</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('admin.events.index') }}" class="btn-gold"><i data-lucide="calendar" class="w-4 h-4"></i> Gerir Eventos</a>
            <a href="{{ route('home') }}" target="_blank" class="btn-outline"><i data-lucide="external-link" class="w-4 h-4"></i> Ver Site Público</a>
        </div>
    </div>

    @if (session('status'))
        <div style="margin-bottom: 24px; background: rgba(16,185,129,0.13); border: 1px solid rgba(16,185,129,0.28); color: #34D399; border-radius: 12px; padding: 14px 18px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="check-circle" class="w-5 h-5"></i> {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.site.update') }}" enctype="multipart/form-data">
        @csrf

        <div style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(300px, 0.65fr); gap: 24px; align-items: start;">
            
            {{-- Painel Esquerdo --}}
            <div style="display: flex; flex-direction: column; gap: 24px;">

                {{-- Bloco 1: Plataforma & Hero da Homepage --}}
                <div class="glass-card" style="padding: 24px;">
                    <h3 style="color: var(--gold); font-size: 1.4rem; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="layout-template" class="w-5 h-5"></i>
                        Página Inicial (Homepage Portal)
                    </h3>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label" for="portal_name">Nome da Plataforma / Marca</label>
                        <input id="portal_name" name="portal_name" class="form-input" value="{{ old('portal_name', $settings['portal_name'] ?? 'Alpha Bilheteira') }}" placeholder="Ex: Alpha Bilheteira">
                        @error('portal_name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label" for="hero_label">Selo do Topo / Tagline</label>
                        <input id="hero_label" name="hero_label" class="form-input" value="{{ old('hero_label', $settings['hero_label'] ?? '') }}" placeholder="Ex: A plataforma de bilheteira digital gospel em Moçambique">
                        @error('hero_label') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label" for="hero_title">Título Principal do Hero *</label>
                        <input id="hero_title" name="hero_title" class="form-input" value="{{ old('hero_title', $settings['hero_title'] ?? '') }}" required placeholder="Ex: COMPRE O SEU BILHETE ONLINE">
                        @error('hero_title') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label" for="hero_subtitle">Subtítulo / Descrição da Plataforma</label>
                        <textarea id="hero_subtitle" name="hero_subtitle" class="form-input" rows="3" placeholder="Descrição resumida exibida abaixo do título principal...">{{ old('hero_subtitle', $settings['hero_subtitle'] ?? '') }}</textarea>
                        @error('hero_subtitle') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="announcement_text">Barra de Anúncio / Comunicado no Topo</label>
                        <input id="announcement_text" name="announcement_text" class="form-input" value="{{ old('announcement_text', $settings['announcement_text'] ?? '') }}" placeholder="Ex: 🎉 Concerto Renúncia 2026 em Quelimane — Bilhetes Promocionais disponíveis!">
                        @error('announcement_text') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Bloco 2: Suporte & Contactos --}}
                <div class="glass-card" style="padding: 24px;">
                    <h3 style="color: var(--gold); font-size: 1.4rem; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="phone-call" class="w-5 h-5"></i>
                        Contacto de Suporte ao Cliente
                    </h3>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label" for="support_phone">Telefone de Atendimento</label>
                            <input id="support_phone" name="support_phone" class="form-input" value="{{ old('support_phone', $settings['support_phone'] ?? '') }}" placeholder="Ex: 87 541 1644">
                            @error('support_phone') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="support_whatsapp">WhatsApp (com indicativo 258)</label>
                            <input id="support_whatsapp" name="support_whatsapp" class="form-input" value="{{ old('support_whatsapp', $settings['support_whatsapp'] ?? '') }}" placeholder="Ex: 258875411644">
                            @error('support_whatsapp') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Bloco 3: Redes Sociais --}}
                <div class="glass-card" style="padding: 24px;">
                    <h3 style="color: var(--gold); font-size: 1.4rem; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="share-2" class="w-5 h-5"></i>
                        Redes Sociais
                    </h3>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label" for="social_facebook">Facebook URL</label>
                            <input id="social_facebook" name="social_facebook" class="form-input" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}" placeholder="https://facebook.com/...">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="social_instagram">Instagram URL</label>
                            <input id="social_instagram" name="social_instagram" class="form-input" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}" placeholder="https://instagram.com/...">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="social_tiktok">TikTok URL</label>
                            <input id="social_tiktok" name="social_tiktok" class="form-input" value="{{ old('social_tiktok', $settings['social_tiktok'] ?? '') }}" placeholder="https://tiktok.com/@...">
                        </div>
                    </div>
                </div>

                {{-- Bloco 4: Texto Institucional / Sobre a Plataforma --}}
                <div class="glass-card" style="padding: 24px;">
                    <h3 style="color: var(--gold); font-size: 1.4rem; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="info" class="w-5 h-5"></i>
                        Texto da Página Sobre / Institucional
                    </h3>
                    <div class="form-group">
                        <textarea id="about_description" name="about_description" class="form-input" rows="5" placeholder="Descrição completa sobre a plataforma e a missão do projeto...">{{ old('about_description', $settings['about_description'] ?? '') }}</textarea>
                        @error('about_description') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div style="margin-top: 10px;">
                    <button type="submit" class="btn-gold" style="padding: 14px 36px; font-size: 1.1rem; width: 100%; display: flex; justify-content: center; align-items: center; gap: 10px;">
                        <i data-lucide="save" class="w-5 h-5"></i> GUARDAR TODAS AS ALTERAÇÕES
                    </button>
                </div>

            </div>

            {{-- Painel Direito (Fundo & Imagens) --}}
            <aside style="display: flex; flex-direction: column; gap: 24px;">
                <div class="glass-card" style="padding: 24px;">
                    <h3 style="color: var(--gold); font-size: 1.2rem; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="image" class="w-5 h-5"></i> Imagem de Fundo (Hero)
                    </h3>
                    @if (!empty($settings['hero_image']))
                        <img src="{{ asset(ltrim($settings['hero_image'], '/')) }}" alt="Hero Background" style="width: 100%; aspect-ratio: 16 / 10; object-fit: cover; border-radius: 12px; margin-bottom: 16px; border: 1px solid rgba(212,175,55,0.2);">
                    @else
                        <div style="width: 100%; aspect-ratio: 16 / 10; border-radius: 12px; margin-bottom: 16px; border: 1px dashed rgba(212,175,55,0.28); display: grid; place-items: center; color: var(--text-muted);">
                            <i data-lucide="image" class="w-8 h-8"></i>
                        </div>
                    @endif
                    <input id="hero_image" name="hero_image" type="file" class="form-input" accept="image/*">
                    <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 10px; line-height: 1.4;">Use uma imagem horizontal em alta resolução. Recomendado 1920x1080px (Máximo 4MB).</p>
                    @error('hero_image') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Atalho para Eventos --}}
                <div class="glass-card" style="padding: 24px; border-left: 4px solid var(--gold);">
                    <h3 style="color: var(--gold); font-size: 1.2rem; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="calendar" class="w-5 h-5"></i> Gestão de Eventos
                    </h3>
                    <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 16px; line-height: 1.5;">
                        Para criar novos eventos, alterar cartazes, preços de bilhetes ou datas, utilize o gestor de eventos.
                    </p>
                    <a href="{{ route('admin.events.index') }}" class="btn-outline" style="width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px;">
                        <i data-lucide="edit-3" class="w-4 h-4"></i> Abrir Gestor de Eventos
                    </a>
                </div>
            </aside>

        </div>
    </form>
</x-admin-layout>
