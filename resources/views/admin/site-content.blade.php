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

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-top: 14px;">
                    <div class="form-group">
                        <label class="form-label" for="event_doors_open">Abertura (Portas)</label>
                        <input id="event_doors_open" name="event_doors_open" type="time" class="form-input" value="{{ old('event_doors_open', $settings['event_doors_open'] ?? '') }}">
                        @error('event_doors_open') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="event_show_time">Início do Show</label>
                        <input id="event_show_time" name="event_show_time" type="time" class="form-input" value="{{ old('event_show_time', $settings['event_show_time'] ?? '') }}">
                        @error('event_show_time') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="event_end_time">Término Previsto</label>
                        <input id="event_end_time" name="event_end_time" type="time" class="form-input" value="{{ old('event_end_time', $settings['event_end_time'] ?? '') }}">
                        @error('event_end_time') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <hr style="border: none; border-top: 1px solid rgba(212,175,55,0.12); margin: 24px 0;">

                <h3 style="color: var(--gold); margin-bottom: 16px; font-size: 1.4rem;">Sobre o Evento</h3>
                <div class="form-group">
                    <label class="form-label" for="about_description">Descrição da página Sobre</label>
                    <textarea id="about_description" name="about_description" class="form-input" rows="5">{{ old('about_description', $settings['about_description'] ?? '') }}</textarea>
                    @error('about_description') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <hr style="border: none; border-top: 1px solid rgba(212,175,55,0.12); margin: 24px 0;">

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="color: var(--gold); font-size: 1.4rem; margin: 0;">Galeria de Outros Artistas</h3>
                    <button type="button" onclick="addArtistRow()" class="btn-sm btn-gold" style="display: flex; align-items: center; gap: 6px; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--gold); cursor: pointer;"><i data-lucide="plus" class="w-4 h-4"></i> Adicionar Artista</button>
                </div>

                <div id="artists-container" style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;">
                    @php $artists = json_decode($settings['other_artists'] ?? '[]', true) ?? []; @endphp
                    @foreach($artists as $idx => $artist)
                        <div class="artist-row" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(212,175,55,0.15); border-radius: 12px; padding: 16px; position: relative;" id="artist-row-{{ $idx }}">
                            <button type="button" onclick="removeArtistRow({{ $idx }})" style="position: absolute; top: 12px; right: 12px; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #EF4444; border-radius: 6px; padding: 4px 8px; cursor: pointer; font-size: 0.8rem;">Remover</button>
                            
                            <div style="display: grid; grid-template-columns: 80px 1fr; gap: 16px; align-items: start;">
                                <div style="text-align: center;">
                                    @if(!empty($artist['photo']))
                                        <img id="artist-img-{{ $idx }}" src="{{ asset(ltrim($artist['photo'], '/')) }}" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 2px solid var(--gold); margin-bottom: 8px; display: inline-block;">
                                    @else
                                        <div id="artist-preview-{{ $idx }}" style="width: 70px; height: 70px; border-radius: 50%; background: rgba(255,255,255,0.05); border: 1px dashed rgba(212,175,55,0.3); display: grid; place-items: center; color: var(--text-muted); margin-bottom: 8px; margin: 0 auto 8px;">
                                            <i data-lucide="user" class="w-6 h-6"></i>
                                        </div>
                                    @endif
                                    <input type="hidden" name="other_artists_existing_photo[{{ $idx }}]" value="{{ $artist['photo'] }}">
                                    <label style="font-size: 0.75rem; color: var(--gold); cursor: pointer; text-decoration: underline; display: block; margin-top: 4px;">
                                        Foto
                                        <input type="file" name="other_artists_photo[{{ $idx }}]" accept="image/*" style="display: none;" onchange="previewArtistPhoto(this, {{ $idx }})">
                                    </label>
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                    <div>
                                        <label class="form-label" style="font-size: 0.8rem; margin-bottom: 4px;">Nome do Artista</label>
                                        <input type="text" name="other_artists_name[{{ $idx }}]" value="{{ $artist['name'] }}" class="form-input" placeholder="Ex: Minister Asafe Jamal" style="padding: 6px 12px; font-size: 0.9rem;" required>
                                    </div>
                                    <div>
                                        <label class="form-label" style="font-size: 0.8rem; margin-bottom: 4px;">Pequena Bio / Função</label>
                                        <input type="text" name="other_artists_bio[{{ $idx }}]" value="{{ $artist['bio'] }}" class="form-input" placeholder="Ex: Cantor Gospel / Solo" style="padding: 6px 12px; font-size: 0.9rem;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
                    <img src="{{ asset(ltrim($settings['hero_image'], '/')) }}" alt="" style="width: 100%; aspect-ratio: 16 / 10; object-fit: cover; border-radius: 12px; margin-bottom: 12px; border: 1px solid rgba(212,175,55,0.2);">
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

    <script>
        let artistIndex = {{ count($artists) }};
        function addArtistRow() {
            const container = document.getElementById('artists-container');
            const rowHtml = `
                <div class="artist-row" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(212,175,55,0.15); border-radius: 12px; padding: 16px; position: relative;" id="artist-row-${artistIndex}">
                    <button type="button" onclick="removeArtistRow(${artistIndex})" style="position: absolute; top: 12px; right: 12px; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #EF4444; border-radius: 6px; padding: 4px 8px; cursor: pointer; font-size: 0.8rem;">Remover</button>
                    
                    <div style="display: grid; grid-template-columns: 80px 1fr; gap: 16px; align-items: start;">
                        <div style="text-align: center;">
                            <div id="artist-preview-${artistIndex}" style="width: 70px; height: 70px; border-radius: 50%; background: rgba(255,255,255,0.05); border: 1px dashed rgba(212,175,55,0.3); display: grid; place-items: center; color: var(--text-muted); margin: 0 auto 8px;">
                                <i data-lucide="user" class="w-6 h-6"></i>
                            </div>
                            <input type="hidden" name="other_artists_existing_photo[${artistIndex}]" value="">
                            <label style="font-size: 0.75rem; color: var(--gold); cursor: pointer; text-decoration: underline; display: block; margin-top: 4px;">
                                Foto
                                <input type="file" name="other_artists_photo[${artistIndex}]" accept="image/*" style="display: none;" onchange="previewArtistPhoto(this, ${artistIndex})">
                            </label>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <div>
                                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 4px;">Nome do Artista</label>
                                <input type="text" name="other_artists_name[${artistIndex}]" value="" class="form-input" placeholder="Ex: Novo Artista" style="padding: 6px 12px; font-size: 0.9rem;" required>
                            </div>
                            <div>
                                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 4px;">Pequena Bio / Função</label>
                                <input type="text" name="other_artists_bio[${artistIndex}]" value="" class="form-input" placeholder="Ex: Solo" style="padding: 6px 12px; font-size: 0.9rem;">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', rowHtml);
            if (window.lucide) {
                window.lucide.createIcons();
            }
            artistIndex++;
        }

        function removeArtistRow(index) {
            const row = document.getElementById(`artist-row-${index}`);
            if (row) {
                row.remove();
            }
        }

        function previewArtistPhoto(input, index) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById(`artist-preview-${index}`);
                    const parent = input.parentElement.parentElement;
                    
                    let img = document.getElementById(`artist-img-${index}`);
                    if (!img) {
                        img = document.createElement('img');
                        img.id = `artist-img-${index}`;
                        img.style.width = '70px';
                        img.style.height = '70px';
                        img.style.borderRadius = '50%';
                        img.style.objectFit = 'cover';
                        img.style.border = '2px solid var(--gold)';
                        img.style.marginBottom = '8px';
                        img.style.display = 'inline-block';
                        
                        if (preview) {
                            preview.replaceWith(img);
                        } else {
                            parent.prepend(img);
                        }
                    }
                    img.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-admin-layout>
