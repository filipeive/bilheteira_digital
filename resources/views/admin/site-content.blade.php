<x-admin-layout title="Conteúdo do Site">
    <div class="admin-hero">
        <div>
            <span class="badge badge-gold"><i data-lucide="globe" class="w-4 h-4" style="margin-right: 6px;"></i> Página inicial</span>
            <h1 style="font-size: clamp(2.2rem, 5vw, 3.6rem); color: var(--gold); margin-top: 10px;">CONTEÚDO DO SITE</h1>
            <p style="color: var(--text-secondary); max-width: 680px;">Altere textos, contactos e imagem de fundo usados na página pública sem editar código.</p>
        </div>
        <a href="{{ route('home') }}" target="_blank" class="btn-outline"><i data-lucide="external-link" class="w-4 h-4"></i> Ver site</a>
    </div>

    @if (session('status'))
        <div style="margin-bottom: 18px; background: rgba(16,185,129,0.13); border: 1px solid rgba(16,185,129,0.28); color: #34D399; border-radius: 12px; padding: 14px 16px;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.site.update') }}" enctype="multipart/form-data" class="admin-panel">
        @csrf

        <div style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(280px, 0.65fr); gap: 22px; align-items: start;">
            <div>
                <div class="form-group">
                    <label class="form-label" for="hero_label">Selo do topo</label>
                    <input id="hero_label" name="hero_label" class="form-input" value="{{ old('hero_label', $settings['hero_label']) }}">
                    @error('hero_label') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="hero_title">Título principal</label>
                    <input id="hero_title" name="hero_title" class="form-input" value="{{ old('hero_title', $settings['hero_title']) }}" required>
                    @error('hero_title') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="hero_artists">Artistas principais</label>
                    <input id="hero_artists" name="hero_artists" class="form-input" value="{{ old('hero_artists', $settings['hero_artists']) }}" required>
                    @error('hero_artists') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="hero_support">Participações / apoio</label>
                    <textarea id="hero_support" name="hero_support" class="form-input" rows="4">{{ old('hero_support', $settings['hero_support']) }}</textarea>
                    @error('hero_support') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label class="form-label" for="support_phone">Telefone de suporte</label>
                        <input id="support_phone" name="support_phone" class="form-input" value="{{ old('support_phone', $settings['support_phone']) }}">
                        @error('support_phone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="support_whatsapp">WhatsApp com indicativo</label>
                        <input id="support_whatsapp" name="support_whatsapp" class="form-input" value="{{ old('support_whatsapp', $settings['support_whatsapp']) }}">
                        @error('support_whatsapp') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <hr style="border: none; border-top: 1px solid rgba(212,175,55,0.12); margin: 24px 0;">

                <h3 style="color: var(--gold); margin-bottom: 16px; font-size: 1.4rem;">Detalhes do Evento</h3>

                <div class="form-group">
                    <label class="form-label" for="event_name">Nome do evento</label>
                    <input id="event_name" name="event_name" class="form-input" value="{{ old('event_name', $event->name ?? '') }}">
                    @error('event_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label class="form-label" for="event_date">Data do evento</label>
                        <input id="event_date" name="event_date" type="datetime-local" class="form-input" value="{{ old('event_date', $event->date?->format('Y-m-d\TH:i') ?? '') }}">
                        @error('event_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="event_venue">Local / Venue</label>
                        <input id="event_venue" name="event_venue" class="form-input" value="{{ old('event_venue', $event->venue ?? '') }}">
                        @error('event_venue') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="event_city">Cidade</label>
                    <input id="event_city" name="event_city" class="form-input" value="{{ old('event_city', $event->city ?? '') }}">
                    @error('event_city') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                </div>

                <hr style="border: none; border-top: 1px solid rgba(212,175,55,0.12); margin: 24px 0;">

                <h3 style="color: var(--gold); margin-bottom: 16px; font-size: 1.4rem;">Galeria de Imagens</h3>
                <div class="form-group">
                    <label class="form-label" for="gallery_images">Adicionar novas imagens</label>
                    <input id="gallery_images" name="gallery_images[]" type="file" multiple class="form-input" accept="image/*">
                    <p style="color: var(--text-muted); font-size: 0.78rem; margin-top: 6px;">Pode seleccionar várias imagens de uma vez.</p>
                </div>
                
                @php $gallery = json_decode($settings['gallery_images'] ?? '[]', true); @endphp
                @if(is_array($gallery) && count($gallery) > 0)
                <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 16px;">
                    @foreach($gallery as $index => $img)
                    <div style="position: relative; width: 100px; height: 100px;" x-data="{ removed: false }" x-show="!removed">
                        <img src="{{ asset($img) }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                        <label @click="removed = true" style="position: absolute; top: -6px; right: -6px; background: var(--accent-red); color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 10px; font-family: sans-serif;">
                            <input type="checkbox" name="remove_gallery[]" value="{{ $index }}" style="display: none;"> X
                        </label>
                    </div>
                    @endforeach
                </div>
                <p style="color: var(--text-muted); font-size: 0.78rem; margin-top: 8px;">Clique no X vermelho para marcar a imagem para apagar. Só terá efeito ao guardar.</p>
                @endif
            </div>

            <aside style="background: rgba(13,11,7,0.58); border: 1px solid var(--dark-border); border-radius: 14px; padding: 18px;">
                <label class="form-label" for="hero_image">Imagem de fundo</label>
                @if (!empty($settings['hero_image']))
                    <img src="{{ asset($settings['hero_image']) }}" alt="" style="width: 100%; aspect-ratio: 16 / 10; object-fit: cover; border-radius: 12px; margin-bottom: 12px; border: 1px solid rgba(212,175,55,0.2);">
                @else
                    <div style="width: 100%; aspect-ratio: 16 / 10; border-radius: 12px; margin-bottom: 12px; border: 1px dashed rgba(212,175,55,0.28); display: grid; place-items: center; color: var(--text-muted);">
                        <i data-lucide="image" class="w-8 h-8"></i>
                    </div>
                @endif
                <input id="hero_image" name="hero_image" type="file" class="form-input" accept="image/*">
                <p style="color: var(--text-muted); font-size: 0.78rem; margin-top: 10px;">Use uma imagem horizontal do evento/cartaz. Máximo 4MB.</p>
                @error('hero_image') <p class="form-error">{{ $message }}</p> @enderror
            </aside>
        </div>

        @if ($errors->any())
            <div style="margin-top: 16px; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #F87171; border-radius: 12px; padding: 14px 16px;">
                <p style="font-weight: 600; margin-bottom: 8px;">Erros de validação:</p>
                <ul style="margin-left: 20px; font-size: 0.85rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="display: flex; justify-content: flex-end; margin-top: 22px;">
            <button type="submit" class="btn-gold"><i data-lucide="save" class="w-4 h-4"></i> Guardar alterações</button>
        </div>
    </form>

    <style>
        .admin-panel {
            background: rgba(35,31,24,0.82);
            border: 1px solid rgba(212,175,55,0.18);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 22px 70px rgba(0,0,0,0.22);
        }
        .admin-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 22px;
            padding: 22px;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(212,175,55,0.12), rgba(16,185,129,0.04));
            border: 1px solid rgba(212,175,55,0.16);
        }
        @media (max-width: 820px) {
            .admin-hero,
            .admin-panel > div {
                grid-template-columns: 1fr !important;
                flex-direction: column;
                align-items: stretch !important;
            }
            div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-admin-layout>
