<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Bilhetes') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
        <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --gold: #D4AF37;
                --gold-light: #F5E6A3;
                --dark-bg: #0D0B07;
                --dark-card: #17130D;
                --dark-border: rgba(212, 175, 55, 0.22);
                --text-primary: #F7F1E4;
                --text-secondary: #B8A890;
            }
            body { font-family: 'Montserrat', sans-serif; }
            .auth-shell {
                min-height: 100vh;
                display: grid;
                place-items: center;
                padding: 94px 18px 36px;
                background:
                    linear-gradient(rgba(13,11,7,0.76), rgba(13,11,7,0.92)),
                    radial-gradient(circle at 20% 8%, rgba(212,175,55,0.24), transparent 28rem),
                    radial-gradient(circle at 80% 12%, rgba(16,185,129,0.12), transparent 24rem),
                    #0D0B07;
                color: var(--text-primary);
            }
            .auth-card {
                width: 100%;
                max-width: 470px;
                background: rgba(23, 19, 13, 0.9);
                border: 1px solid var(--dark-border);
                border-radius: 20px;
                box-shadow: 0 26px 80px rgba(0,0,0,0.42);
                overflow: hidden;
                backdrop-filter: blur(18px);
            }
            .auth-card-header {
                padding: 28px 30px 22px;
                text-align: center;
                border-bottom: 1px solid rgba(212,175,55,0.16);
                background: linear-gradient(135deg, rgba(212,175,55,0.12), rgba(13,11,7,0));
            }
            .auth-card-header img {
                width: 190px;
                max-height: 78px;
                object-fit: contain;
                margin: 0 auto 14px;
            }
            .auth-card-header h1 {
                font-family: 'Bebas Neue', cursive;
                font-size: 2.15rem;
                letter-spacing: 0.08em;
                color: var(--gold);
                line-height: 1;
            }
            .auth-card-header p {
                color: var(--text-secondary);
                font-size: 0.88rem;
                margin-top: 8px;
            }
            .auth-card-body { padding: 28px 30px 30px; }
            .auth-card a { color: var(--gold-light); }
            .auth-card label { color: var(--text-secondary); font-weight: 700; }
            .auth-card input[type="email"],
            .auth-card input[type="password"],
            .auth-card input[type="text"] {
                background: rgba(13, 11, 7, 0.72);
                border-color: rgba(212,175,55,0.2);
                color: var(--text-primary);
                border-radius: 10px;
            }
            .auth-card input:focus {
                border-color: var(--gold);
                box-shadow: 0 0 0 3px rgba(212,175,55,0.16);
            }
        </style>
    </head>
    <body>
        <main class="auth-shell">
            <section class="auth-card">
                <div class="auth-card-header">
                    <a href="/">
                        <img src="{{ asset('alpha-logo-gold.png') }}" alt="Alpha Produções">
                    </a>
                    <h1>{{ $heading ?? 'Acesso ao Sistema' }}</h1>
                    <p>{{ $subheading ?? 'Gestão segura dos bilhetes do Concerto Renúncia' }}</p>
                </div>

                <div class="auth-card-body">
                    {{ $slot }}
                </div>
            </section>
        </main>

        <script>lucide.createIcons();</script>
    </body>
</html>
