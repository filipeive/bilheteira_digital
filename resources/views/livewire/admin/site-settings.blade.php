<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px;">
        <div>
            <span class="badge badge-gold"><i data-lucide="settings" class="w-4 h-4" style="margin-right: 6px;"></i> Plataforma & Eventos</span>
            <h1 style="font-size: 2.5rem; color: var(--gold); margin-top: 6px;">CONFIGURAÇÕES DO SITE</h1>
            <p style="color: var(--text-secondary);">Gira as configurações globais da plataforma, personalize o PDF dos bilhetes e dados dos eventos.</p>
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
        <button wire:click="$set('activeTab', 'pdf')" class="btn-sm" style="{{ $activeTab === 'pdf' ? 'background: rgba(212,175,55,0.2); color: var(--gold); border: 1px solid var(--gold);' : 'background: var(--dark-card); color: var(--text-secondary); border: 1px solid var(--dark-border);' }} padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">
            <i data-lucide="file-text" class="w-4 h-4"></i> Layout & PDF do Bilhete
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
        <form wire:submit.prevent="savePlatform" style="max-width: 100%;">
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

        {{-- Tab 2: Layout & PDF do Bilhete --}}
        @if($activeTab === 'pdf')
        <form wire:submit.prevent="savePdf">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                <div>
                    <h3 style="color: var(--gold); font-size: 1.3rem; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="file-text" class="w-5 h-5"></i> CONFIGURAÇÃO DO PDF DO BILHETE
                    </h3>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">Ajuste o tamanho da fonte, marca, cores e termos impressos nos bilhetes PDF.</p>
                </div>
                @if($this->sampleTicket?->id)
                <a href="{{ route('admin.tickets.preview', $this->sampleTicket) }}" target="_blank" class="btn-gold" style="font-size: 0.9rem; padding: 10px 18px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <i data-lucide="eye" class="w-4 h-4"></i> Abrir Pré-visualização PDF ao Vivo
                </a>
                @endif
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 24px;">
                <div>
                    <label class="form-label">Tamanho da Fonte do Título do Evento (CSS)</label>
                    <select wire:model="pdf_event_name_font_size" class="form-select">
                        <option value="14px">14px (Muito Pequeno)</option>
                        <option value="16px">16px (Pequeno)</option>
                        <option value="18px">18px (Médio Pequeno)</option>
                        <option value="20px">20px (Normal / Recomendado)</option>
                        <option value="22px">22px (Médio Grande)</option>
                        <option value="24px">24px (Grande)</option>
                        <option value="28px">28px (Muito Grande)</option>
                        <option value="32px">32px (Gigante)</option>
                    </select>
                    <p style="color: var(--text-muted); font-size: 0.78rem; margin-top: 4px;">Ajusta o tamanho de letra do nome do evento no PDF.</p>
                </div>

                <div>
                    <label class="form-label">Rótulo / Marca no Topo do PDF</label>
                    <input type="text" wire:model="pdf_brand_text" class="form-input" placeholder="Deixe em branco para usar o organizador do evento">
                    <p style="color: var(--text-muted); font-size: 0.78rem; margin-top: 4px;">Ex: ALPHA PRODUÇÕES APRESENTA:</p>
                </div>

                <div>
                    <label class="form-label">Cor Primária do Destaque (Hexadecimal)</label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="color" wire:model.live="pdf_primary_color" class="form-input" style="width: 50px; height: 42px; padding: 2px; cursor: pointer;">
                        <input type="text" wire:model="pdf_primary_color" class="form-input" placeholder="#C9A227" style="flex: 1;">
                    </div>
                </div>

                <div>
                    <label class="form-label">Exibir Fotos dos Artistas no PDF</label>
                    <div style="margin-top: 10px; display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" wire:model="pdf_show_artists" id="pdf_show_artists" style="width: 20px; height: 20px; cursor: pointer;">
                        <label for="pdf_show_artists" style="cursor: pointer; color: var(--text-primary); font-size: 0.9rem;">Exibir fotos e nomes dos artistas no bilhete</label>
                    </div>
                </div>

                {{-- Upload Imagem de Fundo Global do Bilhete PDF --}}
                <div style="grid-column: 1 / -1; background: rgba(0,0,0,0.3); border: 1px solid var(--dark-border); border-radius: 10px; padding: 16px;">
                    <label class="form-label" style="margin-bottom: 8px; font-weight: 700; color: var(--gold);">IMAGEM DE FUNDO GLOBAL DO BILHETE PDF</label>
                    <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 12px;">Se não for carregada uma imagem específica para o evento, esta imagem será usada como fundo de todos os bilhetes PDF (720x250px recomendado).</p>
                    
                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                        @if($upload_pdf_bg_image)
                            <img src="{{ $upload_pdf_bg_image->temporaryUrl() }}" style="width: 220px; height: 75px; border-radius: 8px; object-fit: cover; border: 2px solid var(--gold);">
                        @elseif($current_pdf_bg_image)
                            <img src="{{ asset($current_pdf_bg_image) }}" style="width: 220px; height: 75px; border-radius: 8px; object-fit: cover; border: 2px solid var(--gold);">
                        @else
                            <div style="width: 220px; height: 75px; border-radius: 8px; background: rgba(255,255,255,0.05); border: 1px dashed var(--dark-border); display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 0.8rem;">
                                <i data-lucide="image" class="w-5 h-5" style="margin-right: 6px;"></i> Fundo Vetorial Padrão
                            </div>
                        @endif

                        <div style="flex: 1; min-width: 240px;">
                            <input type="file" wire:model="upload_pdf_bg_image" accept="image/*" class="form-input" style="padding: 8px 12px; font-size: 0.85rem;">
                            @if($current_pdf_bg_image || $upload_pdf_bg_image)
                                <button type="button" wire:click="removePdfBgImage" style="background: none; border: none; color: #EF4444; font-size: 0.8rem; cursor: pointer; margin-top: 6px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i> Remover Fundo Global Personalizado
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div style="grid-column: 1 / -1;">
                    <label class="form-label">Termos &amp; Condições do Bilhete</label>
                    <textarea wire:model="pdf_terms_text" class="form-input" rows="3" placeholder="Termos legais impressos no rodapé do bilhete..."></textarea>
                </div>
            </div>

            <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
                <button type="submit" class="btn-gold" style="padding: 12px 28px;">
                    <i data-lucide="save" class="w-4 h-4"></i> Guardar Configurações de PDF
                </button>
            </div>
        </form>
        @endif

        {{-- Tab 3: Evento Selecionado --}}
        @if($activeTab === 'event')
        <form wire:submit.prevent="saveEvent" style="max-width: 100%;">
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
                    <textarea wire:model="event_description" class="form-input" rows="3"></textarea>
                </div>

                {{-- Personalização de PDF por Evento --}}
                <div style="grid-column: 1 / -1; margin-top: 12px; border-top: 1px dashed var(--dark-border); padding-top: 16px;">
                    <h4 style="color: var(--gold); font-size: 1.05rem; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="file-text" class="w-4 h-4"></i> PERSONALIZAÇÃO DO BILHETE PDF DESTE EVENTO
                    </h4>
                </div>

                <div style="grid-column: 1 / -1;">
                    <label class="form-label">Artistas / Oradores do Evento (Impresso no Bilhete)</label>
                    <input type="text" wire:model="event_artists" class="form-input" placeholder="Ex: Dra. Ana Paula · Pra. Sarah Silva">
                    <p style="color: var(--text-muted); font-size: 0.78rem; margin-top: 4px;">Estes nomes serão impressos exclusivamente no bilhete deste evento.</p>
                </div>

                {{-- Upload Fotos dos Artistas do Evento --}}
                <div style="grid-column: 1 / -1; background: rgba(0,0,0,0.3); border: 1px solid var(--dark-border); border-radius: 10px; padding: 16px;">
                    <label class="form-label" style="margin-bottom: 12px; font-weight: 700; color: var(--gold);">FOTOS DOS ARTISTAS / ORADORES DO EVENTO (IMPRESSAS NO BILHETE PDF)</label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                        
                        {{-- Foto Artista 1 --}}
                        <div>
                            <label class="form-label">Foto do Artista / Orador 1</label>
                            <div style="display: flex; align-items: center; gap: 12px; margin-top: 6px;">
                                @if($upload_artist_photo_1)
                                    <img src="{{ $upload_artist_photo_1->temporaryUrl() }}" style="width: 54px; height: 54px; border-radius: 50%; object-fit: cover; border: 2px solid var(--gold);">
                                @elseif($current_artist_photo_1)
                                    <img src="{{ asset($current_artist_photo_1) }}" style="width: 54px; height: 54px; border-radius: 50%; object-fit: cover; border: 2px solid var(--gold);">
                                @else
                                    <div style="width: 54px; height: 54px; border-radius: 50%; background: rgba(255,255,255,0.05); border: 1px dashed var(--dark-border); display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                                        <i data-lucide="user" class="w-6 h-6"></i>
                                    </div>
                                @endif

                                <div style="flex: 1;">
                                    <input type="file" wire:model="upload_artist_photo_1" accept="image/*" class="form-input" style="padding: 6px 10px; font-size: 0.8rem;">
                                    @if($current_artist_photo_1 || $upload_artist_photo_1)
                                        <button type="button" wire:click="removeArtistPhoto(1)" style="background: none; border: none; color: #EF4444; font-size: 0.78rem; cursor: pointer; margin-top: 4px; display: inline-flex; align-items: center; gap: 4px;">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Remover Foto 1
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Foto Artista 2 --}}
                        <div>
                            <label class="form-label">Foto do Artista / Orador 2</label>
                            <div style="display: flex; align-items: center; gap: 12px; margin-top: 6px;">
                                @if($upload_artist_photo_2)
                                    <img src="{{ $upload_artist_photo_2->temporaryUrl() }}" style="width: 54px; height: 54px; border-radius: 50%; object-fit: cover; border: 2px solid var(--gold);">
                                @elseif($current_artist_photo_2)
                                    <img src="{{ asset($current_artist_photo_2) }}" style="width: 54px; height: 54px; border-radius: 50%; object-fit: cover; border: 2px solid var(--gold);">
                                @else
                                    <div style="width: 54px; height: 54px; border-radius: 50%; background: rgba(255,255,255,0.05); border: 1px dashed var(--dark-border); display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                                        <i data-lucide="user" class="w-6 h-6"></i>
                                    </div>
                                @endif

                                <div style="flex: 1;">
                                    <input type="file" wire:model="upload_artist_photo_2" accept="image/*" class="form-input" style="padding: 6px 10px; font-size: 0.8rem;">
                                    @if($current_artist_photo_2 || $upload_artist_photo_2)
                                        <button type="button" wire:click="removeArtistPhoto(2)" style="background: none; border: none; color: #EF4444; font-size: 0.78rem; cursor: pointer; margin-top: 4px; display: inline-flex; align-items: center; gap: 4px;">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Remover Foto 2
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Upload Imagem de Fundo do Bilhete do Evento --}}
                <div style="grid-column: 1 / -1; background: rgba(0,0,0,0.3); border: 1px solid var(--dark-border); border-radius: 10px; padding: 16px;">
                    <label class="form-label" style="margin-bottom: 8px; font-weight: 700; color: var(--gold);">IMAGEM DE FUNDO EXCLUSIVA DO BILHETE DESTE EVENTO (PDF)</label>
                    <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 12px;">Carregue uma imagem/banner de fundo personalizada exclusivamente para os bilhetes deste evento (720x250px recomendado).</p>
                    
                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                        @if($upload_event_ticket_bg_image)
                            <img src="{{ $upload_event_ticket_bg_image->temporaryUrl() }}" style="width: 220px; height: 75px; border-radius: 8px; object-fit: cover; border: 2px solid var(--gold);">
                        @elseif($current_event_ticket_bg_image)
                            <img src="{{ asset($current_event_ticket_bg_image) }}" style="width: 220px; height: 75px; border-radius: 8px; object-fit: cover; border: 2px solid var(--gold);">
                        @else
                            <div style="width: 220px; height: 75px; border-radius: 8px; background: rgba(255,255,255,0.05); border: 1px dashed var(--dark-border); display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 0.8rem;">
                                <i data-lucide="image" class="w-5 h-5" style="margin-right: 6px;"></i> Sem fundo exclusivo (Usando Fundo Global/Padrão)
                            </div>
                        @endif

                        <div style="flex: 1; min-width: 240px;">
                            <input type="file" wire:model="upload_event_ticket_bg_image" accept="image/*" class="form-input" style="padding: 8px 12px; font-size: 0.85rem;">
                            @if($current_event_ticket_bg_image || $upload_event_ticket_bg_image)
                                <button type="button" wire:click="removeEventTicketBgImage" style="background: none; border: none; color: #EF4444; font-size: 0.8rem; cursor: pointer; margin-top: 6px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i> Remover Fundo Exclusivo do Evento
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <label class="form-label">Rótulo / Marca no Topo do PDF deste Evento</label>
                    <input type="text" wire:model="event_pdf_brand_text" class="form-input" placeholder="Ex: MINISTÉRIO MULHERES DE FÉ APRESENTA:">
                </div>

                <div>
                    <label class="form-label">Cor Primária do Destaque no PDF deste Evento</label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="color" wire:model.live="event_primary_color" class="form-input" style="width: 50px; height: 42px; padding: 2px; cursor: pointer;">
                        <input type="text" wire:model="event_primary_color" class="form-input" placeholder="#D4A017" style="flex: 1;">
                    </div>
                </div>

                <div style="grid-column: 1 / -1;">
                    <label class="form-label">Termos &amp; Condições do Bilhete deste Evento</label>
                    <textarea wire:model="event_pdf_terms_text" class="form-input" rows="2" placeholder="Termos específicos impressos no rodapé do bilhete deste evento..."></textarea>
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

        {{-- Tab 4: Redes Sociais & Suporte --}}
        @if($activeTab === 'social')
        <form wire:submit.prevent="saveSocial" style="max-width: 100%;">
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
