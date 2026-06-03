<x-public-layout title="Concerto Renúncia — Bilhetes">

    <!-- Hero Section -->
    <section id="hero" style="position: relative; min-height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden;">
        <!-- Background Effects -->
        <div style="position: absolute; inset: 0; background: radial-gradient(ellipse at center, rgba(212, 175, 55, 0.08) 0%, transparent 60%), radial-gradient(ellipse at top right, rgba(212, 175, 55, 0.05) 0%, transparent 50%), var(--dark-bg);"></div>
        @if (!empty($siteSettings['hero_image']))
            <img src="{{ asset($siteSettings['hero_image']) }}" alt="" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.22; filter: saturate(0.9) contrast(1.08);">
            <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(13,11,7,0.34), rgba(13,11,7,0.72) 62%, var(--dark-bg));"></div>
        @endif
        <div style="position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle at 30% 50%, rgba(212, 175, 55, 0.03) 0%, transparent 40%); animation: float 15s ease-in-out infinite;"></div>

        <!-- Decorative particles -->
        <div style="position: absolute; inset: 0; overflow: hidden;">
            <div style="position: absolute; top: 10%; left: 15%; width: 3px; height: 3px; background: var(--gold); border-radius: 50%; opacity: 0.4; animation: float 4s ease-in-out infinite;"></div>
            <div style="position: absolute; top: 30%; right: 20%; width: 2px; height: 2px; background: var(--gold-light); border-radius: 50%; opacity: 0.3; animation: float 6s ease-in-out 1s infinite;"></div>
            <div style="position: absolute; bottom: 25%; left: 25%; width: 4px; height: 4px; background: var(--gold); border-radius: 50%; opacity: 0.25; animation: float 5s ease-in-out 2s infinite;"></div>
            <div style="position: absolute; top: 60%; right: 10%; width: 2px; height: 2px; background: var(--gold-light); border-radius: 50%; opacity: 0.35; animation: float 7s ease-in-out 0.5s infinite;"></div>
        </div>

        <div class="hero-artist hero-artist-left animate-fade-in-up animate-delay-2" aria-hidden="true">
            <div class="hero-artist-glow"></div>
            <img src="{{ asset('artists/abel-2.png') }}" alt="">
            <div class="hero-artist-name">Abel Last</div>
            <img class="hero-artist-mini hero-artist-mini-left" src="{{ asset('artists/abel-1.png') }}" alt="">
        </div>

        <div class="hero-artist hero-artist-right animate-fade-in-up animate-delay-3" aria-hidden="true">
            <div class="hero-artist-glow"></div>
            <img src="{{ asset('artists/nair-1.jpeg') }}" alt="">
            <div class="hero-artist-name">Nair Nany</div>
            <img class="hero-artist-mini hero-artist-mini-right" src="{{ asset('artists/nair-2.jpg') }}" alt="">
        </div>

        <div style="position: relative; z-index: 10; text-align: center; padding: 40px 20px;">
            <!-- Pre-title -->
            <div class="animate-fade-up delay-100" style="margin-bottom: 16px;">
                <span class="badge badge-gold" style="font-size: 0.8rem; padding: 6px 16px;">
                    <i data-lucide="star" style="width: 14px; height: 14px; margin-right: 4px; display: inline-block; vertical-align: middle;"></i> {{ strtoupper($siteSettings['hero_label'] ?? 'Alpha Produções apresenta') }}
                </span>
            </div>

            <!-- Main Title -->
            <h1 class="animate-fade-in-up animate-delay-2" style="font-size: clamp(3rem, 10vw, 7rem); line-height: 0.95; margin-bottom: 12px; background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold) 50%, var(--gold-dark) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                @foreach (explode(' ', strtoupper($siteSettings['hero_title'] ?? 'Concerto Renúncia')) as $word)
                    {{ $word }}@if (!$loop->last)<br>@endif
                @endforeach
            </h1>

            <!-- Artists -->
            <div class="animate-fade-in-up animate-delay-3" style="margin-bottom: 24px;">
                <p style="font-family: 'Bebas Neue', cursive; font-size: clamp(1.2rem, 3vw, 2rem); color: var(--text-primary); letter-spacing: 0.1em;">
                    {{ strtoupper($siteSettings['hero_artists'] ?? 'Abel Last & Nair Nany') }}
                </p>
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 8px;">
                    {{ $siteSettings['hero_support'] ?? 'Minister Asafe Jamal · Echoes of the Spirit · Muana Careva · Adélia Balice' }}
                </p>
            </div>

            <!-- Event Info -->
            <div class="animate-fade-up delay-300" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 24px; margin-bottom: 40px;">
                <div style="display: flex; align-items: center; gap: 8px; color: var(--text-secondary);">
                    <i data-lucide="calendar" class="w-5 h-5" style="color: var(--gold);"></i>
                    <span style="font-weight: 600;">{{ $event->date->format('d M Y') }} — {{ $event->date->format('H\hi') }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; color: var(--text-secondary);">
                    <i data-lucide="map-pin" class="w-5 h-5" style="color: var(--gold);"></i>
                    <span style="font-weight: 600;">{{ $event->venue }}, {{ $event->city }}</span>
                </div>
            </div>

            <!-- CTA -->
            <div class="animate-fade-up delay-500">
                <a href="#bilhetes" class="btn-gold animate-pulse-gold" style="font-size: 1.4rem; padding: 16px 48px;">
                    <i data-lucide="ticket" class="w-6 h-6"></i> COMPRAR BILHETE
                </a>
            </div>

            <!-- Countdown hint -->
            <p class="animate-fade-in-up animate-delay-6" style="color: var(--text-muted); font-size: 0.8rem; margin-top: 24px; display: inline-flex; align-items: center; gap: 6px;">
                Ver bilhetes disponíveis <i data-lucide="chevrons-down" class="w-4 h-4"></i>
            </p>
        </div>
    </section>

    <style>
        .hero-artist {
            position: absolute;
            z-index: 4;
            bottom: 5.5vh;
            width: min(24vw, 310px);
            min-width: 210px;
            aspect-ratio: 4 / 5;
            border: 1px solid rgba(212, 175, 55, 0.34);
            border-radius: 22px;
            overflow: visible;
            background: rgba(13, 11, 7, 0.36);
            box-shadow: 0 28px 90px rgba(0,0,0,0.42);
            backdrop-filter: blur(8px);
        }
        .hero-artist-left { left: max(0px, calc(50vw - 640px)); transform: rotate(-4deg); }
        .hero-artist-right { right: max(0px, calc(50vw - 640px)); transform: rotate(4deg); }
        .hero-artist > img:first-of-type {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
            border-radius: 21px;
            filter: saturate(1.05) contrast(1.05);
        }
        .hero-artist::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 22px;
            background: linear-gradient(180deg, rgba(13,11,7,0) 48%, rgba(13,11,7,0.72) 100%);
            pointer-events: none;
        }
        .hero-artist-glow {
            position: absolute;
            inset: -18px;
            border-radius: 30px;
            background: radial-gradient(circle at 50% 12%, rgba(212,175,55,0.36), transparent 58%);
            filter: blur(16px);
            opacity: 0.82;
            z-index: -1;
        }
        .hero-artist-name {
            position: absolute;
            left: 16px;
            right: 16px;
            bottom: 14px;
            z-index: 2;
            color: var(--gold-light);
            font-family: 'Bebas Neue', cursive;
            font-size: clamp(1.5rem, 2.4vw, 2.2rem);
            letter-spacing: 0.08em;
            text-shadow: 0 2px 18px rgba(0,0,0,0.75);
        }
        .hero-artist-mini {
            position: absolute;
            z-index: 3;
            width: 88px;
            height: 88px;
            object-fit: cover;
            border-radius: 18px;
            border: 2px solid rgba(245,230,163,0.72);
            box-shadow: 0 14px 36px rgba(0,0,0,0.38);
        }
        .hero-artist-mini-left {
            right: -22px;
            top: 26px;
        }
        .hero-artist-mini-right {
            left: -22px;
            top: 30px;
        }
        @media (max-width: 1180px) {
            .hero-artist {
                width: 205px;
                min-width: 0;
                opacity: 0.58;
                bottom: 7vh;
            }
            .hero-artist-left { left: 12px; }
            .hero-artist-right { right: 12px; }
            .hero-artist-mini { display: none; }
        }
        @media (max-width: 820px) {
            #hero {
                min-height: auto !important;
                padding-top: 96px;
                flex-direction: column;
            }
            .hero-artist {
                position: relative;
                display: inline-block;
                width: min(42vw, 170px);
                aspect-ratio: 4 / 5;
                left: auto;
                right: auto;
                bottom: auto;
                margin: 0 5px 22px;
                opacity: 1;
                transform: none;
            }
            .hero-artist-left,
            .hero-artist-right {
                transform: none;
            }
            .hero-artist-name {
                font-size: 1.35rem;
                bottom: 10px;
            }
        }
        @media (max-width: 480px) {
            .hero-artist {
                width: min(43vw, 145px);
                border-radius: 16px;
            }
            .hero-artist > img:first-of-type,
            .hero-artist::after {
                border-radius: 15px;
            }
        }
    </style>

    <!-- Ticket Types Section -->
    <section id="bilhetes" style="padding: 80px 0; background: var(--dark-surface);">
        <div class="container">
            <div style="text-align: center; margin-bottom: 48px;">
                <h2 style="font-size: clamp(2rem, 5vw, 3.5rem); background: linear-gradient(135deg, var(--gold-light), var(--gold)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 8px;">
                    ESCOLHA O SEU BILHETE
                </h2>
                <p style="color: var(--text-secondary); font-size: 1rem;">
                    Garanta já o seu lugar neste evento especial
                </p>
            </div>
            
            <div class="urgency-bar" style="background: rgba(224,138,58,0.1); border: 1px solid rgba(224,138,58,0.3); border-radius: 4px; padding: 10px 16px; display: flex; align-items: center; gap: 8px; font-size: 12px; color: #E08A3A; margin-bottom: 24px; max-width: 640px; margin-left: auto; margin-right: auto;">
              <i data-lucide="alert-triangle" class="w-4 h-4"></i>
              <span>Bilhetes quase esgotados — <strong>garanta já o seu!</strong></span>
            </div>

            <!-- Ticket Cards Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 60px;">
                @foreach ($event->getTicketTypePrices() as $key => $type)
                    @if ($key !== 'free')
                    <div x-data @click="$dispatch('open-ticket-modal', { type: '{{ $key }}' })" class="glass-card" style="cursor: pointer; text-align: center; position: relative; overflow: hidden; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                        <!-- Glow effect -->
                        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: {{ $type['color'] ?? 'var(--gold)' }};"></div>

                        <div style="width: 58px; height: 58px; border-radius: 16px; margin: 0 auto 14px; display: grid; place-items: center; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); color: {{ $type['color'] ?? 'var(--gold)' }};">
                            <i data-lucide="{{ str_starts_with($key, 'vip') ? 'star' : ($key === 'gate' ? 'door-open' : 'ticket') }}" class="w-8 h-8"></i>
                        </div>
                        <h3 style="font-size: 1.6rem; color: var(--text-primary); margin-bottom: 4px;">{{ $type['name'] ?? '' }}</h3>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 16px;">{{ $type['description'] ?? '' }}</p>

                        <div style="margin-bottom: 12px;">
                            <span class="mono" style="font-size: 2.2rem; font-weight: 700; color: {{ $type['color'] ?? 'var(--gold)' }};">
                                {{ number_format($type['price'] ?? 0, 0, ',', '.') }}
                            </span>
                            <span style="color: var(--text-muted); font-size: 0.9rem;"> MT</span>
                        </div>

                        @if (isset($type['lot_size']) && $type['lot_size'] > 0)
                            <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--dark-border);">
                                Lote: {{ $type['lot_size'] }} bilhetes
                            </span>
                        @endif
                    </div>
                    @endif
                @endforeach
            </div>

            <!-- Purchase Component (renders as modal) -->
            <livewire:ticket-form :event="$event" />
        </div>
    </section>

    <!-- Info Section -->
    <section id="informacoes" style="padding: 60px 0; background: var(--dark-bg);">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 32px;">
                <!-- M-Pesa Instructions -->
                <div class="glass-card">
                    <h3 style="font-size: 1.8rem; color: var(--gold); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;"><i data-lucide="smartphone" class="w-6 h-6"></i> COMO PAGAR</h3>
                    <div style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.8;">
                        <p style="font-weight: 700; color: var(--text-primary); margin-bottom: 8px;">Via M-Pesa:</p>
                        <ol style="padding-left: 20px; margin-bottom: 16px; list-style-type: none; margin-left: 0; padding-left: 0;">
                            <li style="display: flex; align-items: center; gap: 8px;"><i data-lucide="smartphone" class="w-4 h-4"></i> Marque <span class="mono" style="color: var(--gold);">*150#</span></li>
                            <li style="display: flex; align-items: center; gap: 8px;"><i data-lucide="send" class="w-4 h-4"></i> Seleccione <strong>Transferir Dinheiro</strong></li>
                            <li style="display: flex; align-items: center; gap: 8px;"><i data-lucide="hash" class="w-4 h-4"></i> Número: <span class="mono" style="color: var(--gold);">87 541 1644</span></li>
                            <li style="display: flex; align-items: center; gap: 8px;"><i data-lucide="tag" class="w-4 h-4"></i> Valor conforme o tipo de bilhete</li>
                            <li style="display: flex; align-items: center; gap: 8px;"><i data-lucide="check-circle" class="w-4 h-4"></i> Confirme com o seu PIN</li>
                        </ol>
                    </div>
                </div>

                <!-- FAQ -->
                <div class="glass-card">
                    <h3 style="font-size: 1.8rem; color: var(--gold); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;"><i data-lucide="help-circle" class="w-6 h-6"></i> PERGUNTAS FREQUENTES</h3>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;">
                        <div style="margin-bottom: 16px;">
                            <p style="font-weight: 700; color: var(--text-primary);">Como recebo o bilhete?</p>
                            <p>Após confirmação do pagamento, receberá o bilhete digital com QR Code por WhatsApp ou email.</p>
                        </div>
                        <div style="margin-bottom: 16px;">
                            <p style="font-weight: 700; color: var(--text-primary);">O bilhete é válido sem internet?</p>
                            <p>Sim! O QR Code funciona offline. Basta ter a imagem guardada no telemóvel.</p>
                        </div>
                        <div style="margin-bottom: 16px;">
                            <p style="font-weight: 700; color: var(--text-primary);">Posso comprar para outra pessoa?</p>
                            <p>Sim, coloque o nome de quem vai ao evento no formulário.</p>
                        </div>
                        <div>
                            <p style="font-weight: 700; color: var(--text-primary);">Quanto tempo demora a confirmação?</p>
                            <p>Normalmente em poucas horas. Em caso de dúvida, contacte-nos pelo WhatsApp.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-public-layout>
