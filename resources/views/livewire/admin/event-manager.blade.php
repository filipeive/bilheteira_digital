<div>
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
        <div>
            <span class="badge badge-gold"><i data-lucide="calendar" class="w-4 h-4" style="margin-right: 6px;"></i> Plataforma</span>
            <h1 style="font-size: 2.8rem; color: var(--gold); margin-top: 8px;">GESTÃO DE EVENTOS</h1>
            <p style="color: var(--text-secondary);">Crie e gira os seus eventos gospel e conferências</p>
        </div>
        @if(!$isCreating && !$editingId)
            <button wire:click="create" class="btn-gold">
                <i data-lucide="plus-circle" class="w-5 h-5"></i> Novo Evento
            </button>
        @else
            <button wire:click="resetForm" class="btn-outline">
                <i data-lucide="arrow-left" class="w-5 h-5"></i> Voltar à Lista
            </button>
        @endif
    </div>

    {{-- Formulário de Criação/Edição --}}
    @if($isCreating || $editingId)
    <div class="glass-card" style="margin-bottom: 40px; padding: 32px;">
        <h2 style="font-size: 1.8rem; color: var(--gold); margin-bottom: 24px;">
            {{ $editingId ? 'Editar Evento' : 'Criar Novo Evento' }}
        </h2>

        <form wire:submit.prevent="save">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px;">

                {{-- Nome --}}
                <div class="form-group">
                    <label class="form-label">Nome do Evento *</label>
                    <input type="text" wire:model="name" class="form-input" placeholder="Ex: Concerto Renúncia 2026">
                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                {{-- Categoria --}}
                <div class="form-group">
                    <label class="form-label">Categoria *</label>
                    <select wire:model="category" class="form-select">
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                {{-- Província --}}
                <div class="form-group">
                    <label class="form-label">Província *</label>
                    <select wire:model="province" class="form-select">
                        @foreach($provinces as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('province') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                {{-- Venue --}}
                <div class="form-group">
                    <label class="form-label">Local / Venue *</label>
                    <input type="text" wire:model="venue" class="form-input" placeholder="Ex: Pavilhão do Benfica">
                    @error('venue') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                {{-- Endereço Completo --}}
                <div class="form-group">
                    <label class="form-label">Endereço Completo</label>
                    <input type="text" wire:model="venue_address" class="form-input" placeholder="Ex: Av. Samora Machel, Quelimane">
                </div>

                {{-- Google Maps URL --}}
                <div class="form-group">
                    <label class="form-label">Link do Google Maps</label>
                    <input type="url" wire:model="maps_url" class="form-input" placeholder="https://maps.google.com/...">
                </div>

                {{-- Promotor --}}
                <div class="form-group">
                    <label class="form-label">Nome do Promotor / Organizador *</label>
                    <input type="text" wire:model="organizer_name" class="form-input" placeholder="Ex: Alpha Produções & Faith">
                    @error('organizer_name') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                {{-- Data de Início --}}
                <div class="form-group">
                    <label class="form-label">Data e Hora de Início *</label>
                    <input type="datetime-local" wire:model="starts_at" class="form-input">
                    @error('starts_at') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                {{-- Data de Fim --}}
                <div class="form-group">
                    <label class="form-label">Data e Hora de Fim</label>
                    <input type="datetime-local" wire:model="ends_at" class="form-input">
                </div>

                {{-- Portões Abrem --}}
                <div class="form-group">
                    <label class="form-label">Abertura de Portões</label>
                    <input type="datetime-local" wire:model="gates_open_at" class="form-input">
                </div>

                {{-- Capacidade --}}
                <div class="form-group">
                    <label class="form-label">Capacidade Máxima</label>
                    <input type="number" wire:model="max_capacity" class="form-input" placeholder="0 = ilimitado">
                </div>

                {{-- Estado --}}
                <div class="form-group">
                    <label class="form-label">Estado do Evento *</label>
                    <select wire:model="status" class="form-select">
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tema do Bilhete --}}
                <div class="form-group">
                    <label class="form-label">Tema do Bilhete *</label>
                    <select wire:model.live="ticket_theme" class="form-select">
                        @foreach($themes as $key => $theme)
                            <option value="{{ $key }}">{{ $theme['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Telefone Contacto --}}
                <div class="form-group">
                    <label class="form-label">Telefone de Suporte / Dúvidas</label>
                    <input type="text" wire:model="contact_phone" class="form-input" placeholder="Ex: 87 541 1644">
                </div>

            </div>

            {{-- Cores Customizadas se tema = custom --}}
            @if($ticket_theme === 'custom')
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 24px; padding: 16px; background: rgba(0,0,0,0.3); border-radius: 8px;">
                <div>
                    <label class="form-label">Cor Primária</label>
                    <input type="color" wire:model="primary_color" class="form-input" style="height: 44px; padding: 4px;">
                </div>
                <div>
                    <label class="form-label">Cor de Fundo</label>
                    <input type="color" wire:model="secondary_color" class="form-input" style="height: 44px; padding: 4px;">
                </div>
            </div>
            @endif

            {{-- Imagens --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 24px;">
                <div>
                    <label class="form-label">Imagem de Capa (Cover)</label>
                    <input type="file" wire:model="cover_image_file" class="form-input">
                    @error('cover_image_file') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="form-label">Logótipo do Evento</label>
                    <input type="file" wire:model="logo_image_file" class="form-input">
                    @error('logo_image_file') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Descrição --}}
            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label">Descrição do Evento</label>
                <textarea wire:model="description" rows="4" class="form-input" placeholder="Detalhes sobre o evento, artistas, agenda..."></textarea>
            </div>

            {{-- Destaque checkbox --}}
            <div style="margin-bottom: 32px; display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" wire:model="featured" id="featured" style="width: 18px; height: 18px; cursor: pointer;">
                <label for="featured" style="cursor: pointer; font-size: 0.9rem; color: var(--gold-light);">Colocar este evento em DESTAQUE na página inicial</label>
            </div>

            {{-- Botões --}}
            <div style="display: flex; gap: 16px;">
                <button type="submit" class="btn-gold">
                    <i data-lucide="check" class="w-5 h-5"></i> Guardar Evento
                </button>
                <button type="button" wire:click="resetForm" class="btn-outline">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Tabela de Eventos --}}
    <div class="glass-card" style="padding: 24px;">
        <h2 style="font-size: 1.5rem; color: var(--gold); margin-bottom: 20px;">Todos os Eventos</h2>

        @if($events->isEmpty())
            <p style="color: var(--text-muted); text-align: center; padding: 32px;">Nenhum evento registado.</p>
        @else
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Evento</th>
                            <th>Categoria</th>
                            <th>Província</th>
                            <th>Data</th>
                            <th>Estado</th>
                            <th>Destaque</th>
                            <th style="text-align: right;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                        <tr>
                            <td>
                                <strong style="color: var(--text-primary); font-size: 1rem;">{{ $event->name }}</strong>
                                <span style="display: block; font-size: 0.75rem; color: var(--text-muted);">{{ $event->organizer_name }}</span>
                            </td>
                            <td>
                                <span class="badge badge-gold">{{ $categories[$event->category] ?? $event->category }}</span>
                            </td>
                            <td>{{ $event->province ?? 'Zambézia' }}</td>
                            <td>
                                {{ $event->starts_at?->format('d/m/Y H:i') ?? $event->date?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td>
                                <button wire:click="toggleStatus({{ $event->id }})"
                                        class="badge {{ $event->status === 'published' ? 'badge-green' : ($event->status === 'sold_out' ? 'badge-red' : 'badge-yellow') }}"
                                        style="cursor: pointer; border: none;">
                                    {{ $statuses[$event->status] ?? $event->status }}
                                </button>
                            </td>
                            <td>
                                <button wire:click="toggleFeatured({{ $event->id }})"
                                        style="background: none; border: none; cursor: pointer; color: {{ $event->featured ? 'var(--gold)' : 'var(--text-muted)' }};">
                                    <i data-lucide="star" class="w-5 h-5" style="{{ $event->featured ? 'fill: var(--gold);' : '' }}"></i>
                                </button>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="{{ route('events.show', $event->slug ?? $event->id) }}" target="_blank" class="btn-sm" title="Ver Página Pública">
                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                    </a>
                                    <button wire:click="edit({{ $event->id }})" class="btn-sm" title="Editar">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
