<x-public-layout title="Sobre o Concerto Renúncia">

    <section style="padding: 120px 0 60px; background: var(--dark-bg); min-height: 100vh;">
        <div class="container">
            <div style="max-width: 800px; margin: 0 auto; text-align: center; margin-bottom: 60px;">
                <span class="badge badge-gold" style="margin-bottom: 16px;">
                    <i data-lucide="info" style="width: 14px; height: 14px; margin-right: 4px; display: inline-block; vertical-align: middle;"></i> Sobre o Evento
                </span>
                <h1 style="font-size: clamp(2.5rem, 6vw, 4.5rem); line-height: 1; margin-bottom: 24px; background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold) 50%, var(--gold-dark) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    O CONCERTO RENÚNCIA
                </h1>
                <p style="font-size: 1.1rem; color: var(--text-secondary); line-height: 1.8;">
                    Um momento de adoração, louvor e entrega total. O Concerto Renúncia não é apenas um evento musical, mas um encontro de almas dispostas a renunciar o mundo e abraçar a fé.
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; margin-bottom: 80px;">
                <!-- Artists Section -->
                <div class="glass-card">
                    <h3 style="font-size: 2rem; color: var(--gold); margin-bottom: 24px; border-bottom: 1px solid rgba(212, 175, 55, 0.2); padding-bottom: 12px;">
                        Artistas Principais
                    </h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <div style="display: flex; gap: 16px; align-items: center;">
                            <img src="{{ asset('artists/abel-1.png') }}" alt="Abel Last" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid var(--gold);">
                            <div>
                                <h4 style="font-size: 1.4rem; color: var(--text-primary); margin-bottom: 4px;">Abel Last</h4>
                                <p style="font-size: 0.9rem; color: var(--text-secondary);">Músico e adorador, conhecido pelas suas canções profundas que tocam o coração e convidam à reflexão.</p>
                            </div>
                        </div>

                        <div style="display: flex; gap: 16px; align-items: center;">
                            <img src="{{ asset('artists/nair-2.jpg') }}" alt="Nair Nany" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid var(--gold);">
                            <div>
                                <h4 style="font-size: 1.4rem; color: var(--text-primary); margin-bottom: 4px;">Nair Nany</h4>
                                <p style="font-size: 0.9rem; color: var(--text-secondary);">Voz marcante na música gospel, Nair Nany traz uma mensagem de esperança e renovação através do seu ministério.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event Details Section -->
                <div class="glass-card">
                    <h3 style="font-size: 2rem; color: var(--gold); margin-bottom: 24px; border-bottom: 1px solid rgba(212, 175, 55, 0.2); padding-bottom: 12px;">
                        Detalhes do Evento
                    </h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 16px;">
                            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(212, 175, 55, 0.1); display: grid; place-items: center; color: var(--gold); flex-shrink: 0;">
                                <i data-lucide="calendar" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 style="font-size: 1.1rem; color: var(--text-primary); margin-bottom: 4px;">Data e Hora</h4>
                                <p style="color: var(--text-secondary);">{{ $event->date->format('d de F de Y') }}</p>
                                <p style="color: var(--text-secondary);">Abertura das portas: 16:00 | Início: {{ $event->date->format('H:i') }}</p>
                            </div>
                        </div>

                        <div style="display: flex; align-items: flex-start; gap: 16px;">
                            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(212, 175, 55, 0.1); display: grid; place-items: center; color: var(--gold); flex-shrink: 0;">
                                <i data-lucide="map-pin" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 style="font-size: 1.1rem; color: var(--text-primary); margin-bottom: 4px;">Localização</h4>
                                <p style="color: var(--text-secondary);">{{ $event->venue }}</p>
                                <p style="color: var(--text-secondary);">{{ $event->city }}, Moçambique</p>
                            </div>
                        </div>

                        <div style="display: flex; align-items: flex-start; gap: 16px;">
                            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(212, 175, 55, 0.1); display: grid; place-items: center; color: var(--gold); flex-shrink: 0;">
                                <i data-lucide="users" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 style="font-size: 1.1rem; color: var(--text-primary); margin-bottom: 4px;">Participações Especiais</h4>
                                <p style="color: var(--text-secondary);">Minister Asafe Jamal, Echoes of the Spirit, Muana Careva, Adélia Balice</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            </div>

            <!-- Gallery Section -->
            @php $gallery = json_decode($siteSettings['gallery_images'] ?? '[]', true); @endphp
            @if(is_array($gallery) && count($gallery) > 0)
            <div style="margin-bottom: 80px;">
                <h3 style="font-size: 2rem; color: var(--gold); margin-bottom: 24px; border-bottom: 1px solid rgba(212, 175, 55, 0.2); padding-bottom: 12px; text-align: center;">
                    Galeria do Evento
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                    @foreach($gallery as $img)
                        <img src="{{ asset($img) }}" alt="Galeria" style="width: 100%; height: 200px; object-fit: cover; border-radius: 12px; border: 2px solid rgba(212, 175, 55, 0.2); transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                    @endforeach
                </div>
            </div>
            @endif

            <div style="text-align: center;">
                <a href="{{ route('home') }}#bilhetes" class="btn-gold" style="font-size: 1.3rem; padding: 16px 40px;">
                    <i data-lucide="ticket" class="w-6 h-6"></i> Comprar o Seu Bilhete
                </a>
            </div>
        </div>
    </section>

</x-public-layout>
