@extends('layouts.public')

@section('title', 'Sobre a Plataforma — Alpha Bilheteira')
@section('meta_description', 'Conheça a Alpha Bilheteira, a plataforma de bilheteira digital para eventos gospel, concertos e conferências em Moçambique.')

@section('content')
<section style="padding: 120px 0 72px; min-height: 100vh;">
    <div class="container">

        {{-- HERO SOBRE A PLATAFORMA --}}
        <div style="max-width: 820px; margin: 0 auto 56px; text-align: center;">
            <span class="badge badge-gold" style="margin-bottom: 16px; padding: 6px 16px; gap: 6px;">
                <i data-lucide="sparkles" style="width: 14px; height: 14px;"></i>
                Sobre a Plataforma
            </span>
            <h1 style="font-size: clamp(2.6rem, 6vw, 4.5rem); line-height: 1; margin-bottom: 20px; background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold) 50%, var(--gold-dark) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                ALPHA BILHETEIRA
            </h1>
            <p style="font-size: 1.05rem; color: var(--text-secondary); line-height: 1.8;">
                A <strong style="color: var(--text-primary);">Alpha Bilheteira</strong> é a plataforma moçambicana de bilheteira digital dedicada a eventos gospel, concertos, conferências e encontros cristãos. Ligamos organizadores e público em todo o país com compra simples, pagamentos locais e bilhetes digitais seguros.
            </p>
        </div>

        {{-- INDICADORES DA PLATAFORMA --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 56px;">
            <div class="glass-card" style="text-align: center; padding: 24px;">
                <div style="width: 46px; height: 46px; border-radius: 12px; background: rgba(212, 175, 55, 0.12); border: 1px solid rgba(212, 175, 55, 0.25); display: inline-grid; place-items: center; color: var(--gold); margin-bottom: 12px;">
                    <i data-lucide="calendar-check" class="w-6 h-6"></i>
                </div>
                <div style="font-family: 'Bebas Neue', cursive; font-size: 2.3rem; line-height: 1; color: var(--gold-light);">
                    {{ $eventsCount ?? 0 }}
                </div>
                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 4px;">Eventos Publicados</div>
            </div>

            <div class="glass-card" style="text-align: center; padding: 24px;">
                <div style="width: 46px; height: 46px; border-radius: 12px; background: rgba(212, 175, 55, 0.12); border: 1px solid rgba(212, 175, 55, 0.25); display: inline-grid; place-items: center; color: var(--gold); margin-bottom: 12px;">
                    <i data-lucide="map-pin" class="w-6 h-6"></i>
                </div>
                <div style="font-family: 'Bebas Neue', cursive; font-size: 2.3rem; line-height: 1; color: var(--gold-light);">
                    {{ max($provincesCount ?? 1, 1) }}
                </div>
                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 4px;">{{ ($provincesCount ?? 1) === 1 ? 'Província Abrangida' : 'Províncias Abrangidas' }}</div>
            </div>

            <div class="glass-card" style="text-align: center; padding: 24px;">
                <div style="width: 46px; height: 46px; border-radius: 12px; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.25); display: inline-grid; place-items: center; color: var(--accent-green); margin-bottom: 12px;">
                    <i data-lucide="qr-code" class="w-6 h-6"></i>
                </div>
                <div style="font-family: 'Bebas Neue', cursive; font-size: 2.3rem; line-height: 1; color: var(--gold-light);">
                    QR CODE
                </div>
                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 4px;">Validação Instantânea e Offline</div>
            </div>

            <div class="glass-card" style="text-align: center; padding: 24px;">
                <div style="width: 46px; height: 46px; border-radius: 12px; background: rgba(212, 175, 55, 0.12); border: 1px solid rgba(212, 175, 55, 0.25); display: inline-grid; place-items: center; color: var(--gold); margin-bottom: 12px;">
                    <i data-lucide="smartphone" class="w-6 h-6"></i>
                </div>
                <div style="font-family: 'Bebas Neue', cursive; font-size: 2.3rem; line-height: 1; color: var(--gold-light);">
                    M-PESA & E-MOLA
                </div>
                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 4px;">Pagamentos Locais Práticos</div>
            </div>
        </div>

        {{-- PILARES DA PLATAFORMA --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: 28px; margin-bottom: 56px;">
            <div class="glass-card">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(212, 175, 55, 0.12); display: grid; place-items: center; color: var(--gold); flex-shrink: 0;">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                    </div>
                    <h2 style="font-size: 1.7rem; color: var(--gold);">Bilhetes Autênticos</h2>
                </div>
                <p style="font-size: 0.93rem; color: var(--text-secondary); line-height: 1.7;">
                    Cada bilhete emitido recebe um código único e assinatura criptográfica em QR Code, impedindo falsificações e garantindo uma entrada rápida no recinto mesmo sem ligação à internet.
                </p>
            </div>

            <div class="glass-card">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(212, 175, 55, 0.12); display: grid; place-items: center; color: var(--gold); flex-shrink: 0;">
                        <i data-lucide="wallet" class="w-5 h-5"></i>
                    </div>
                    <h2 style="font-size: 1.7rem; color: var(--gold);">Pagamento Acessível</h2>
                </div>
                <p style="font-size: 0.93rem; color: var(--text-secondary); line-height: 1.7;">
                    Construída para a realidade de Moçambique, a plataforma suporta pagamentos por carteira móvel (M-Pesa e e-Mola), emissão de comprovativo e envio do bilhete em PDF por WhatsApp e email.
                </p>
            </div>

            <div class="glass-card">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(212, 175, 55, 0.12); display: grid; place-items: center; color: var(--gold); flex-shrink: 0;">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                    </div>
                    <h2 style="font-size: 1.7rem; color: var(--gold);">Para Organizadores</h2>
                </div>
                <p style="font-size: 0.93rem; color: var(--text-secondary); line-height: 1.7;">
                    Igrejas, produtoras e promotores contam com gestão de múltiplos eventos, lotes personalizados, vendas presenciais rápidas, equipa de validação no portão e relatórios financeiros em tempo real.
                </p>
            </div>
        </div>

        {{-- COMO FUNCIONA --}}
        <div class="glass-card" style="margin-bottom: 56px; padding: 36px 28px;">
            <div style="text-align: center; margin-bottom: 32px;">
                <h2 style="font-size: 2.2rem; color: var(--gold); margin-bottom: 6px;">Como Funciona</h2>
                <p style="color: var(--text-secondary); font-size: 0.95rem;">Do catálogo à entrada do evento em quatro passos simples.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 24px;">
                <div style="padding: 18px; border-radius: 12px; background: rgba(13, 11, 7, 0.55); border: 1px solid rgba(212, 175, 55, 0.14);">
                    <span class="badge badge-gold" style="margin-bottom: 10px;">1. Explorar</span>
                    <h3 style="font-size: 1.35rem; color: var(--text-primary); margin-bottom: 6px;">Escolha o Evento</h3>
                    <p style="font-size: 0.88rem; color: var(--text-secondary);">Filtre por categoria ou província e consulte os lotes disponíveis.</p>
                </div>

                <div style="padding: 18px; border-radius: 12px; background: rgba(13, 11, 7, 0.55); border: 1px solid rgba(212, 175, 55, 0.14);">
                    <span class="badge badge-gold" style="margin-bottom: 10px;">2. Reservar</span>
                    <h3 style="font-size: 1.35rem; color: var(--text-primary); margin-bottom: 6px;">Indique os Dados</h3>
                    <p style="font-size: 0.88rem; color: var(--text-secondary);">Seleccione a quantidade de bilhetes e informe o nome e contacto do titular.</p>
                </div>

                <div style="padding: 18px; border-radius: 12px; background: rgba(13, 11, 7, 0.55); border: 1px solid rgba(212, 175, 55, 0.14);">
                    <span class="badge badge-gold" style="margin-bottom: 10px;">3. Pagar</span>
                    <h3 style="font-size: 1.35rem; color: var(--text-primary); margin-bottom: 6px;">M-Pesa ou e-Mola</h3>
                    <p style="font-size: 0.88rem; color: var(--text-secondary);">Efectue o pagamento de forma prática com confirmação rápida pela equipa.</p>
                </div>

                <div style="padding: 18px; border-radius: 12px; background: rgba(13, 11, 7, 0.55); border: 1px solid rgba(212, 175, 55, 0.14);">
                    <span class="badge badge-gold" style="margin-bottom: 10px;">4. Aceder</span>
                    <h3 style="font-size: 1.35rem; color: var(--text-primary); margin-bottom: 6px;">Bilhete Digital</h3>
                    <p style="font-size: 0.88rem; color: var(--text-secondary);">Descarregue o PDF com QR Code ou consulte sempre em Meus Bilhetes.</p>
                </div>
            </div>
        </div>

        {{-- SUPORTE & PROMOTORES + CTAS --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: 28px; margin-bottom: 48px;">
            <div class="glass-card">
                <h3 style="font-size: 1.7rem; color: var(--gold); margin-bottom: 12px;">Suporte ao Cliente</h3>
                <p style="font-size: 0.92rem; color: var(--text-secondary); margin-bottom: 18px;">
                    Precisa de ajuda com uma compra, confirmação de pagamento ou recuperação do seu bilhete? A nossa equipa está disponível para ajudar.
                </p>
                <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                    <a href="https://wa.me/258875411644" target="_blank" rel="noopener" class="btn-outline">
                        <i data-lucide="message-circle" class="w-4 h-4"></i> WhatsApp Suporte
                    </a>
                    <a href="tel:+258875411644" class="btn-outline">
                        <i data-lucide="phone" class="w-4 h-4"></i> 87 541 1644
                    </a>
                </div>
            </div>

            <div class="glass-card">
                <h3 style="font-size: 1.7rem; color: var(--gold); margin-bottom: 12px;">Quer Publicar um Evento?</h3>
                <p style="font-size: 0.92rem; color: var(--text-secondary); margin-bottom: 18px;">
                    Organiza concertos, conferências ou cultos especiais? Fale connosco para disponibilizar os seus bilhetes na Alpha Bilheteira com painel de controlo dedicado.
                </p>
                <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                    <a href="https://wa.me/258875411644?text=Ol%C3%A1%2C%20gostaria%20de%20publicar%20um%20evento%20na%20Alpha%20Bilheteira." target="_blank" rel="noopener" class="btn-outline">
                        <i data-lucide="megaphone" class="w-4 h-4"></i> Falar com a Equipa
                    </a>
                </div>
            </div>
        </div>

        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 16px;">
            <a href="{{ route('events.index') }}" class="btn-gold">
                <i data-lucide="calendar" class="w-5 h-5"></i> Explorar Eventos
            </a>
            <a href="{{ route('tickets.mine') }}" class="btn-outline">
                <i data-lucide="ticket" class="w-5 h-5"></i> Meus Bilhetes
            </a>
            <a href="{{ route('tickets.verify.form') }}" class="btn-outline">
                <i data-lucide="shield-check" class="w-5 h-5"></i> Verificar Bilhete
            </a>
        </div>

    </div>
</section>
@endsection

