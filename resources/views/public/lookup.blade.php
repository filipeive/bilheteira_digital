<x-public-layout title="Consultar Bilhetes — Concerto Renúncia">
    <section style="min-height: 100vh; padding: 128px 0 72px; position: relative; overflow: hidden;">
        <div style="position: absolute; inset: 0; background: radial-gradient(circle at 18% 10%, rgba(212,175,55,0.18), transparent 28rem), radial-gradient(circle at 82% 12%, rgba(16,185,129,0.1), transparent 25rem);"></div>
        @if (!empty($siteSettings['hero_image']))
            <img src="{{ $siteSettings['hero_image'] }}" alt="" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.12;">
        @endif

        <div class="container" style="position: relative; z-index: 1;">
            <div style="max-width: 1040px; margin: 0 auto;">
                <a href="{{ route('home') }}" style="color: var(--gold); text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 22px; font-weight: 700;">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Voltar à página inicial
                </a>

                <div style="display: grid; grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr); gap: 24px; align-items: start;">
                    <div class="glass-card" style="border-color: rgba(212, 175, 55, 0.28);">
                        <span class="badge badge-gold" style="margin-bottom: 14px;"><i data-lucide="search" class="w-4 h-4" style="margin-right: 6px;"></i> Consulta de bilhetes</span>
                        <h1 style="font-size: clamp(2.5rem, 6vw, 4.4rem); color: var(--gold); line-height: 0.95; margin-bottom: 12px;">MEUS BILHETES</h1>
                        <p style="color: var(--text-secondary); font-size: 0.98rem; margin-bottom: 24px;">
                            Digite o número usado na compra para baixar os bilhetes com QR Code e verificar se já foram usados.
                        </p>

                        <form method="POST" action="{{ route('tickets.lookup') }}">
                            @csrf
                            <label class="form-label" for="lookup_phone">Número de celular</label>
                            <div style="display: flex; gap: 10px;">
                                <input id="lookup_phone" name="phone" type="tel" value="{{ old('phone', $lookupPhone ?? '') }}" class="form-input" placeholder="+258841234567" style="flex: 1;">
                                <button type="submit" class="btn-gold" style="font-size: 1rem; padding: 12px 18px;">
                                    <i data-lucide="search" class="w-5 h-5"></i> Consultar
                                </button>
                            </div>
                            @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                        </form>

                        @auth
                            <div style="margin-top: 24px; padding-top: 22px; border-top: 1px solid rgba(212,175,55,0.14);">
                                <h2 style="font-size: 1.55rem; color: var(--text-primary); margin-bottom: 8px; display: flex; align-items: center; gap: 8px;"><i data-lucide="scan-qr-code" class="w-5 h-5" style="color: var(--gold);"></i> Consulta por QR</h2>
                                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 14px;">Disponível para utilizadores autenticados.</p>

                                <form method="POST" action="{{ route('tickets.lookup') }}" id="qrLookupForm">
                                    @csrf
                                    <input type="hidden" name="ticket_code" id="ticket_code" value="{{ old('ticket_code', $lookupCode ?? '') }}">
                                    <button type="button" class="btn-outline" id="startLookupScanner" style="width: 100%; justify-content: center;">
                                        <i data-lucide="camera" class="w-4 h-4"></i> Abrir câmera
                                    </button>
                                </form>

                                <div id="lookup-reader" style="display: none; margin-top: 14px; border: 1px solid var(--dark-border); border-radius: 12px; overflow: hidden;"></div>
                                <p id="lookupCameraStatus" style="display: none; margin-top: 10px; color: #FBBF24; font-size: 0.82rem;"></p>
                            </div>
                        @endauth
                    </div>

                    <div class="glass-card" style="min-height: 320px;">
                        @isset($lookupTickets)
                            <div style="display: flex; justify-content: space-between; gap: 12px; align-items: center; margin-bottom: 18px;">
                                <div>
                                    <h2 style="font-size: 1.8rem; color: var(--text-primary);">Resultado da consulta</h2>
                                    <p style="color: var(--text-muted); font-size: 0.82rem;">{{ ($lookupMode ?? 'phone') === 'ticket_code' ? 'Pesquisa por QR/código' : 'Pesquisa por celular' }}</p>
                                </div>
                                <span class="badge badge-blue">{{ $lookupTickets->count() }} encontrado(s)</span>
                            </div>

                            @forelse ($lookupTickets as $ticket)
                                @php
                                    $badgeClass = match ($ticket->status) {
                                        'confirmed' => 'badge-green',
                                        'used' => 'badge-blue',
                                        'cancelled' => 'badge-red',
                                        default => 'badge-yellow',
                                    };
                                    $statusIcon = match ($ticket->status) {
                                        'confirmed' => 'check-circle',
                                        'used' => 'ban',
                                        'cancelled' => 'x-circle',
                                        default => 'alert-triangle',
                                    };
                                @endphp
                                <article style="background: rgba(13,11,7,0.68); border: 1px solid var(--dark-border); border-radius: 12px; padding: 16px; margin-bottom: 12px;">
                                    <div style="display: flex; justify-content: space-between; gap: 12px; align-items: flex-start;">
                                        <div>
                                            <p class="mono" style="color: var(--gold); font-size: 1.12rem; font-weight: 700;">{{ $ticket->ticket_code }}</p>
                                            <p style="color: var(--text-secondary); font-size: 0.9rem;">{{ $ticket->buyer_name }} · {{ $ticket->getTicketTypeLabel() }}</p>
                                        </div>
                                        <span class="badge {{ $badgeClass }}"><i data-lucide="{{ $statusIcon }}" class="w-4 h-4" style="margin-right: 6px;"></i>{{ $ticket->getStatusLabel() }}</span>
                                    </div>

                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 14px; flex-wrap: wrap;">
                                        <div style="color: var(--text-muted); font-size: 0.84rem;">
                                            @if ($ticket->used_at)
                                                Usado em {{ $ticket->used_at->format('d/m/Y H:i') }}
                                            @else
                                                {{ $ticket->status === 'confirmed' ? 'Pronto para apresentar na entrada.' : 'Aguarda regularização pela equipa.' }}
                                            @endif
                                        </div>
                                        <div style="display: flex; gap: 8px;">
                                            <a href="https://wa.me/?text={{ urlencode('Aqui está o meu bilhete para o Concerto Renúncia: ' . URL::temporarySignedRoute('tickets.download', now()->addHours(12), $ticket)) }}" target="_blank" class="btn-outline" style="font-size: 0.82rem; padding: 9px 12px; color: #25D366; border-color: rgba(37, 211, 102, 0.3);" title="Partilhar no WhatsApp">
                                                <i data-lucide="message-circle" class="w-4 h-4"></i> Enviar WhatsApp
                                            </a>
                                            <a href="{{ URL::temporarySignedRoute('tickets.download.png', now()->addHours(12), $ticket) }}" class="btn-outline" style="font-size: 0.82rem; padding: 9px 12px;" title="Baixar Imagem">
                                                <i data-lucide="image" class="w-4 h-4"></i> PNG
                                            </a>
                                            <a href="{{ URL::temporarySignedRoute('tickets.download', now()->addHours(12), $ticket) }}" class="btn-gold" style="font-size: 0.82rem; padding: 9px 12px;" title="Baixar PDF">
                                                <i data-lucide="download" class="w-4 h-4"></i> PDF
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div style="text-align: center; padding: 44px 10px; color: var(--text-secondary);">
                                    <i data-lucide="ticket-x" class="w-12 h-12" style="color: var(--text-muted); margin-bottom: 12px;"></i>
                                    <p>Nenhum bilhete encontrado para esta consulta.</p>
                                </div>
                            @endforelse
                        @else
                            <div style="height: 100%; min-height: 260px; display: grid; place-items: center; text-align: center; color: var(--text-secondary);">
                                <div>
                                    <i data-lucide="ticket" class="w-14 h-14" style="color: var(--gold); margin-bottom: 14px;"></i>
                                    <p>Os seus bilhetes aparecem aqui após a consulta.</p>
                                </div>
                            </div>
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </section>

    @auth
        <script src="https://unpkg.com/html5-qrcode/html5-qrcode.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const button = document.getElementById('startLookupScanner');
                const reader = document.getElementById('lookup-reader');
                const status = document.getElementById('lookupCameraStatus');
                const input = document.getElementById('ticket_code');
                const form = document.getElementById('qrLookupForm');
                let scanner = null;

                button?.addEventListener('click', async () => {
                    if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                        status.textContent = 'A câmera só abre em HTTPS ou localhost.';
                        status.style.display = 'block';
                        return;
                    }

                    reader.style.display = 'block';
                    status.style.display = 'none';

                    try {
                        scanner = scanner || new Html5Qrcode('lookup-reader');
                        await scanner.start(
                            { facingMode: 'environment' },
                            { fps: 10, qrbox: { width: 240, height: 240 } },
                            async (decodedText) => {
                                input.value = decodedText;
                                await scanner.stop();
                                form.submit();
                            }
                        );
                    } catch (error) {
                        status.textContent = 'Não foi possível abrir a câmera. Verifique as permissões.';
                        status.style.display = 'block';
                    }
                });
            });
        </script>
    @endauth

    <style>
        @media (max-width: 860px) {
            section .container > div > div {
                grid-template-columns: 1fr !important;
            }
            form > div {
                flex-direction: column;
            }
        }
    </style>
</x-public-layout>
