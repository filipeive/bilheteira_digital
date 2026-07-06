<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Concerto Renúncia — Abel Last & Nair Nany. 11 Julho 2026, Pavilhão do Benfica, Quelimane. Compre o seu bilhete agora!">
    <meta name="keywords" content="Concerto Renúncia, Abel Last, Nair Nany, Quelimane, bilhetes, música gospel, Moçambique">
    <meta name="author" content="Alpha Produções">

    <title>{{ $title ?? 'Concerto Renúncia — Bilhetes' }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('alpha-logo-gold.png') }}">


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <style>
        :root {
            --gold: #D4AF37;
            --gold-light: #F5E6A3;
            --gold-dark: #B8960C;
            --dark-bg: #0D0B07;
            --dark-surface: #1A1610;
            --dark-card: #231F18;
            --dark-border: #3D362A;
            --text-primary: #F5F0E8;
            --text-secondary: #B8A890;
            --text-muted: #8A7D6B;
            --accent-green: #10B981;
            --accent-red: #EF4444;
            --accent-blue: #3B82F6;
            --accent-yellow: #F59E0B;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background:
                radial-gradient(circle at 15% 10%, rgba(212, 175, 55, 0.12), transparent 28rem),
                radial-gradient(circle at 85% 0%, rgba(16, 185, 129, 0.08), transparent 26rem),
                linear-gradient(180deg, #0D0B07 0%, #141008 46%, #0D0B07 100%);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Bebas Neue', cursive;
            letter-spacing: 0.05em;
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: var(--dark-bg);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--gold-dark);
            border-radius: 3px;
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes pulse-gold {
            0%, 100% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.4); }
            50% { box-shadow: 0 0 20px 5px rgba(212, 175, 55, 0.2); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease forwards;
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease forwards;
        }
        .animate-fade-up { animation: fadeUp 0.6s ease forwards; }
        .animate-pulse-gold { animation: pulse-gold 2s ease infinite; }

        @keyframes fadeUp {
          from { opacity: 0; transform: translateY(24px); }
          to   { opacity: 1; transform: translateY(0); }
        }

        .delay-100 { animation-delay: 0.1s; opacity: 0; }
        .delay-200 { animation-delay: 0.2s; opacity: 0; }
        .delay-300 { animation-delay: 0.3s; opacity: 0; }
        .delay-500 { animation-delay: 0.5s; opacity: 0; }
        .animate-delay-1 { animation-delay: 0.1s; opacity: 0; }
        .animate-delay-2 { animation-delay: 0.2s; opacity: 0; }
        .animate-delay-3 { animation-delay: 0.3s; opacity: 0; }
        .animate-delay-4 { animation-delay: 0.4s; opacity: 0; }
        .animate-delay-5 { animation-delay: 0.5s; opacity: 0; }
        .animate-delay-6 { animation-delay: 0.6s; opacity: 0; }

        /* Buttons */
        .btn-gold {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 32px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: var(--dark-bg);
            font-family: 'Bebas Neue', cursive;
            font-size: 1.2rem;
            letter-spacing: 0.1em;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }
        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.35);
        }
        .btn-gold:active {
            transform: translateY(0);
        }
        .btn-gold:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: transparent;
            color: var(--gold);
            border: 1px solid var(--gold);
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-outline:hover {
            background: rgba(212, 175, 55, 0.1);
            border-color: var(--gold-light);
        }

        /* Cards */
        .glass-card {
            background: rgba(35, 31, 24, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.15);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
        }
        .glass-card:hover {
            border-color: rgba(212, 175, 55, 0.35);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        /* Form elements */
        .form-group {
            margin-bottom: 16px;
        }
        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .form-input {
            width: 100%;
            padding: 12px 16px;
            background: var(--dark-bg);
            border: 1px solid var(--dark-border);
            border-radius: 8px;
            color: var(--text-primary);
            font-family: 'Montserrat', sans-serif;
            font-size: 0.95rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
        }
        .form-input::placeholder {
            color: var(--text-muted);
        }
        .form-error {
            color: var(--accent-red);
            font-size: 0.8rem;
            margin-top: 4px;
        }
        .form-select {
            width: 100%;
            padding: 12px 16px;
            background: var(--dark-bg);
            border: 1px solid var(--dark-border);
            border-radius: 8px;
            color: var(--text-primary);
            font-family: 'Montserrat', sans-serif;
            font-size: 0.95rem;
            transition: border-color 0.3s ease;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23B8A890' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
        }
        .form-select:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
        }
        .form-select option {
            background: var(--dark-surface);
            color: var(--text-primary);
        }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .badge-gold {
            background: rgba(212, 175, 55, 0.15);
            color: var(--gold);
            border: 1px solid rgba(212, 175, 55, 0.3);
        }
        .badge-green {
            background: rgba(16, 185, 129, 0.15);
            color: #34D399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .badge-red {
            background: rgba(239, 68, 68, 0.15);
            color: #F87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .badge-yellow {
            background: rgba(245, 158, 11, 0.15);
            color: #FBBF24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .badge-blue {
            background: rgba(59, 130, 246, 0.15);
            color: #60A5FA;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        /* Container */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .site-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background: rgba(13, 11, 7, 0.82);
            border-bottom: 1px solid rgba(212, 175, 55, 0.12);
            backdrop-filter: blur(16px);
        }
        .site-nav-inner {
            height: 76px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        .site-nav-logo img {
            width: 158px;
            max-height: 54px;
            object-fit: contain;
        }
        .site-nav-links {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .site-nav-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.86rem;
            font-weight: 700;
        }
        .site-nav-link:hover { color: var(--gold-light); }
        .site-nav-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: var(--dark-bg);
            text-decoration: none;
            font-family: 'Bebas Neue', cursive;
            font-size: 1.1rem;
            letter-spacing: 0.08em;
        }
        .site-nav-login {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border-radius: 8px;
            border: 1px solid rgba(212, 175, 55, 0.38);
            background: rgba(13, 11, 7, 0.42);
            color: var(--gold-light);
            text-decoration: none;
            font-weight: 800;
            font-size: 0.84rem;
        }
        .site-nav-login:hover {
            background: rgba(212, 175, 55, 0.1);
            color: var(--gold);
        }
        .mobile-buy-cta {
            display: none;
            position: fixed;
            right: 16px;
            bottom: 16px;
            z-index: 60;
            box-shadow: 0 12px 30px rgba(0,0,0,0.34);
        }
        @media (max-width: 760px) {
            .site-nav-inner { height: 66px; }
            .site-nav-logo img { width: 134px; }
            .site-nav-links .site-nav-link { display: none; }
            .site-nav-links .site-nav-cta { display: none; }
            .site-nav-login span { display: none; }
            .site-nav-cta { padding: 9px 13px; }
            .site-nav-login { padding: 9px 11px; }
            .mobile-buy-cta { display: inline-flex; }
        }
    </style>
</head>
<body>
    <header class="site-nav">
        <div class="container site-nav-inner">
            <a href="{{ route('home') }}" class="site-nav-logo" aria-label="Alpha Produções">
                <img src="{{ asset('alpha-logo-gold.png') }}" alt="Alpha Produções">
            </a>
            <nav class="site-nav-links" aria-label="Navegação principal">
                <a href="{{ route('home') }}#bilhetes" class="site-nav-link"><i data-lucide="ticket" class="w-4 h-4"></i> Bilhetes</a>
                <a href="{{ route('tickets.lookup.form') }}" class="site-nav-link"><i data-lucide="search" class="w-4 h-4"></i> Consultar</a>
                <a href="{{ route('about') }}" class="site-nav-link"><i data-lucide="info" class="w-4 h-4"></i> Sobre</a>
                <a href="tel:+258875411644" class="site-nav-link"><i data-lucide="phone" class="w-4 h-4"></i> 87 541 1644</a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="site-nav-login"><i data-lucide="layout-dashboard" class="w-4 h-4"></i> <span>Gerir</span></a>
                @else
                    <a href="{{ route('login') }}" class="site-nav-login"><i data-lucide="log-in" class="w-4 h-4"></i> <span>Login</span></a>
                @endauth
                <a href="{{ route('home') }}#bilhetes" class="site-nav-cta"><i data-lucide="shopping-cart" class="w-4 h-4"></i> Comprar</a>
            </nav>
        </div>
    </header>
    <a href="{{ route('home') }}#bilhetes" class="site-nav-cta mobile-buy-cta"><i data-lucide="ticket" class="w-4 h-4"></i> Comprar</a>

    {{ $slot }}

    <footer class="site-footer" style="background: var(--dark-surface); border-top: 1px solid var(--dark-border); padding: 40px 0;">
        <div class="container" style="display: flex; flex-wrap: wrap; gap: 40px; justify-content: space-between; margin-bottom: 20px;">
            <div class="footer-logo-col" style="max-width: 300px;">
                <div class="footer-logo" style="margin-bottom: 16px;">
                    <img src="{{ asset('alpha-logo-gold.png') }}" alt="Alpha Produções" style="max-width: 200px;">
                </div>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 16px;">A produtora de eventos de referência na Zambézia, Moçambique.</p>
                <div class="footer-social" style="display: flex; gap: 16px;">
                    <a href="https://instagram.com/alphaproducoes" aria-label="Instagram" style="color: var(--gold);"><i data-lucide="instagram" class="w-5 h-5"></i></a>
                    <a href="https://facebook.com/alphaproducoes" aria-label="Facebook" style="color: var(--gold);"><i data-lucide="facebook" class="w-5 h-5"></i></a>
                    <a href="https://tiktok.com/@alphaproducoes" aria-label="TikTok" style="color: var(--gold);"><i data-lucide="music-2" class="w-5 h-5"></i></a>
                    <a href="https://wa.me/258875411644" aria-label="WhatsApp" style="color: var(--gold);"><i data-lucide="message-circle" class="w-5 h-5"></i></a>
                </div>
            </div>
            <div class="footer-links-col" style="display: flex; flex-direction: column; gap: 8px;">
                <h4 style="color: var(--text-primary); margin-bottom: 8px;">Evento</h4>
                <a href="{{ route('home') }}#bilhetes" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">Comprar Bilhetes</a>
                <a href="{{ route('about') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">Sobre o Evento</a>
            </div>
            <div class="footer-links-col" style="display: flex; flex-direction: column; gap: 8px;">
                <h4 style="color: var(--text-primary); margin-bottom: 8px;">Suporte</h4>
                <a href="tel:+258875411644" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">87 541 1644</a>
                <a href="tel:+258848871940" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">84 887 1940</a>
                <a href="https://wa.me/258875411644" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">WhatsApp</a>
            </div>
            <div class="footer-links-col" style="max-width: 250px;">
                <h4 style="color: var(--text-primary); margin-bottom: 8px;">Local de Venda Presencial</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem;">LU & YOSHI CAFÉ LUFAMINA<br>Próximo ao Banco de Moçambique<br>Quelimane</p>
            </div>
        </div>
        <div class="footer-bottom" style="text-align: center; border-top: 1px solid rgba(212, 175, 55, 0.1); padding-top: 20px; color: var(--text-muted); font-size: 0.8rem;">
            <span>© 2026 Alpha Produções & Faith. Todos os direitos reservados.</span>
            <span style="display: block; margin-top: 4px;">Desenvolvido com <i data-lucide="heart" style="width: 12px; height: 12px; display: inline-block;"></i> em Quelimane</span>
        </div>
    </footer>

    @livewireScripts
    <script>
        lucide.createIcons();
        document.addEventListener('livewire:navigated', () => lucide.createIcons());
        document.addEventListener('livewire:init', () => {
            if (window.Livewire?.hook) {
                let lucideTimeout = null;
                Livewire.hook('morph.updated', () => {
                    if (lucideTimeout) clearTimeout(lucideTimeout);
                    lucideTimeout = setTimeout(() => lucide.createIcons(), 50);
                });
            }
        });
    </script>
</body>
</html>
