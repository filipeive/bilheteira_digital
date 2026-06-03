<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} — Bilhetes Renúncia</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('alpha-logo-gold.png') }}">

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
            --body-gradient: radial-gradient(circle at 18% 0%, rgba(212,175,55,0.13), transparent 28rem),
                             radial-gradient(circle at 92% 12%, rgba(16,185,129,0.08), transparent 24rem),
                             linear-gradient(180deg, #0D0B07 0%, #151008 45%, #0D0B07 100%);
        }

        :root.theme-light {
            --gold: #C59A26;
            --gold-light: #E0C058;
            --gold-dark: #9A7718;
            --dark-bg: #F3F4F6;
            --dark-surface: #FFFFFF;
            --dark-card: #FFFFFF;
            --dark-border: #E5E7EB;
            --text-primary: #111827;
            --text-secondary: #4B5563;
            --text-muted: #6B7280;
            --body-gradient: radial-gradient(circle at 18% 0%, rgba(212,175,55,0.08), transparent 28rem),
                             radial-gradient(circle at 92% 12%, rgba(16,185,129,0.04), transparent 24rem),
                             linear-gradient(180deg, #F9FAFB 0%, #E5E7EB 100%);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--body-gradient);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
        }

        h1, h2, h3, h4 { font-family: 'Bebas Neue', cursive; letter-spacing: 0.05em; }
        .mono { font-family: 'JetBrains Mono', monospace; }
        .hidden { display: none !important; }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: rgba(26, 22, 16, 0.92);
            border-right: 1px solid rgba(212,175,55,0.15);
            backdrop-filter: blur(18px);
            padding: 24px 0;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 50;
            transition: transform 0.3s ease;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        .sidebar-logo {
            padding: 0 24px 24px;
            border-bottom: 1px solid rgba(212,175,55,0.14);
            margin-bottom: 8px;
        }
        .sidebar-logo img {
            width: 150px;
            max-height: 62px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        .sidebar-section-label {
            padding: 16px 24px 6px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--text-muted);
        }
        .sidebar-nav { flex: 1; }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 24px;
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            color: var(--gold);
            background: rgba(212, 175, 55, 0.05);
            border-left-color: var(--gold);
        }
        .sidebar-nav a .nav-icon { font-size: 1.1rem; }
        .sidebar-divider {
            height: 1px;
            background: var(--dark-border);
            margin: 8px 24px;
            opacity: 0.5;
        }

        .sidebar-user {
            padding: 16px 24px;
            border-top: 1px solid var(--dark-border);
            background: var(--dark-surface);
        }

        /* Main content */
        .main-content {
            margin-left: 260px;
            flex: 1;
            min-height: 100vh;
            padding: 32px;
            position: relative;
        }

        /* Mobile hamburger */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 60;
            background: var(--dark-card);
            border: 1px solid var(--dark-border);
            color: var(--gold);
            padding: 10px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2rem;
        }

        /* Stat cards */
        .stat-card {
            background: rgba(35,31,24,0.78);
            border: 1px solid rgba(212,175,55,0.14);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 14px 42px rgba(0,0,0,0.16);
        }
        .stat-card:hover {
            border-color: rgba(212, 175, 55, 0.3);
            transform: translateY(-2px);
        }

        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th {
            padding: 12px 16px;
            text-align: left;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--dark-border);
            font-weight: 600;
            cursor: pointer;
            user-select: none;
        }
        .data-table th:hover { color: var(--gold); }
        .data-table td {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(61, 54, 42, 0.5);
            font-size: 0.9rem;
            color: var(--text-secondary);
        }
        .data-table tr:hover td {
            background: rgba(212, 175, 55, 0.03);
        }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .badge-green { background: rgba(16,185,129,0.15); color: #34D399; border: 1px solid rgba(16,185,129,0.3); }
        .badge-yellow { background: rgba(245,158,11,0.15); color: #FBBF24; border: 1px solid rgba(245,158,11,0.3); }
        .badge-blue { background: rgba(59,130,246,0.15); color: #60A5FA; border: 1px solid rgba(59,130,246,0.3); }
        .badge-red { background: rgba(239,68,68,0.15); color: #F87171; border: 1px solid rgba(239,68,68,0.3); }
        .badge-gold { background: rgba(212,175,55,0.15); color: var(--gold); border: 1px solid rgba(212,175,55,0.3); }

        /* Form elements */
        .form-input, .form-select {
            width: 100%;
            padding: 10px 14px;
            background: var(--dark-bg);
            border: 1px solid var(--dark-border);
            border-radius: 8px;
            color: var(--text-primary);
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
        }
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212,175,55,0.15);
        }
        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .form-error {
            color: var(--accent-red);
            font-size: 0.8rem;
            margin-top: 4px;
        }

        /* Buttons */
        .btn-gold {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 10px 24px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: var(--dark-bg); font-family: 'Bebas Neue', cursive;
            font-size: 1rem; letter-spacing: 0.08em;
            border: none; border-radius: 8px; cursor: pointer;
            transition: all 0.3s ease; text-decoration: none;
        }
        .btn-gold:hover { transform: translateY(-1px); box-shadow: 0 4px 15px rgba(212,175,55,0.3); }
        .btn-gold:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        .btn-outline {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 9px 16px;
            background: rgba(13,11,7,0.4);
            color: var(--gold);
            border: 1px solid rgba(212,175,55,0.34);
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.86rem;
            text-decoration: none;
        }
        .btn-outline:hover { background: rgba(212,175,55,0.08); color: var(--gold-light); }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.75rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-confirm { background: rgba(16,185,129,0.15); color: #34D399; border: 1px solid rgba(16,185,129,0.3); }
        .btn-confirm:hover { background: rgba(16,185,129,0.3); }
        .btn-cancel { background: rgba(239,68,68,0.15); color: #F87171; border: 1px solid rgba(239,68,68,0.3); }
        .btn-cancel:hover { background: rgba(239,68,68,0.3); }

        /* Toast notification */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--dark-bg); }
        ::-webkit-scrollbar-thumb { background: var(--gold-dark); border-radius: 3px; }

        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .mobile-toggle { display: block; }
            .main-content { margin-left: 0; padding: 70px 16px 16px; }
        }

        @media (min-width: 768px) {
            .md\:block { display: block !important; }
            .md\:hidden { display: none !important; }
        }
        @media (max-width: 767px) {
            .md\:block { display: none !important; }
            .md\:hidden { display: block !important; }
        }
    </style>
    <script>
        if (localStorage.getItem('adminTheme') === 'light') {
            document.documentElement.classList.add('theme-light');
        }
    </script>
</head>
<body>
    <!-- Mobile Toggle -->
    <button class="mobile-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')" aria-label="Abrir menu"><i data-lucide="menu" class="w-5 h-5"></i></button>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('alpha-logo-gold.png') }}" alt="Alpha Produções">
            <h2 style="font-size: 1.6rem; color: var(--gold);">RENÚNCIA</h2>
            <p style="color: var(--text-muted); font-size: 0.75rem;">Painel Administrativo</p>
        </div>

        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Principal</div>
            <a href="{{ url('/admin') }}" class="{{ request()->is('admin') && !request()->is('admin/*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="layout-dashboard" class="w-4 h-4"></i></span> Dashboard
            </a>
            <a href="{{ url('/admin/tickets') }}" class="{{ request()->is('admin/tickets') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="ticket" class="w-4 h-4"></i></span> Bilhetes
            </a>
            <a href="{{ url('/admin/batches') }}" class="{{ request()->is('admin/batches') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="layers" class="w-4 h-4"></i></span> Lotes
            </a>
            <a href="{{ url('/admin/quick-sale') }}" class="{{ request()->is('admin/quick-sale') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="shopping-cart" class="w-4 h-4"></i></span> Venda Rápida
            </a>
            <a href="{{ url('/admin/manual') }}" class="{{ request()->is('admin/manual') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="pen-line" class="w-4 h-4"></i></span> Venda Manual
            </a>

            @if(auth()->user()->canAccessAdmin())
            <div class="sidebar-divider"></div>
            <div class="sidebar-section-label">Administração</div>
            <a href="{{ url('/admin/users') }}" class="{{ request()->is('admin/users*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="users" class="w-4 h-4"></i></span> Utilizadores
            </a>
            <a href="{{ url('/admin/site') }}" class="{{ request()->is('admin/site') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="layout" class="w-4 h-4"></i></span> Gestão do Site
            </a>
            <a href="{{ url('/admin/settings') }}" class="{{ request()->is('admin/settings') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="settings" class="w-4 h-4"></i></span> Configurações do Site
            </a>
            <a href="{{ url('/admin/audit') }}" class="{{ request()->is('admin/audit') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="shield" class="w-4 h-4"></i></span> Auditoria
            </a>
            @endif

            <div class="sidebar-divider"></div>
            <div class="sidebar-section-label">Ferramentas</div>
            <a href="{{ url('/validar') }}" target="_blank">
                <span class="nav-icon"><i data-lucide="scan" class="w-4 h-4"></i></span> Scanner
            </a>
            <a href="{{ url('/admin/tickets/export') }}">
                <span class="nav-icon"><i data-lucide="download" class="w-4 h-4"></i></span> Exportar CSV
            </a>
            <a href="{{ route('home') }}" target="_blank">
                <span class="nav-icon"><i data-lucide="external-link" class="w-4 h-4"></i></span> Ver Site
            </a>
        </nav>

        <div class="sidebar-user">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                <img src="{{ auth()->user()->avatar_url }}" alt="" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(212,175,55,0.3);">
                <div>
                    <p style="font-size: 0.85rem; font-weight: 600; color: var(--text-primary);">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p style="font-size: 0.7rem; color: var(--text-muted);">{{ \App\Models\User::ROLES[auth()->user()->role] ?? ucfirst(auth()->user()->role) }}</p>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; gap: 8px;">
                    <a href="{{ url('/admin/profile') }}" style="color: var(--text-muted); font-size: 0.8rem; text-decoration: none; display: flex; align-items: center; gap: 4px; transition: color 0.2s;" title="O meu perfil">
                        <i data-lucide="user-circle" class="w-3 h-3"></i> Perfil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: var(--text-muted); font-size: 0.8rem; cursor: pointer; padding: 0; transition: color 0.2s; display: flex; align-items: center; gap: 4px;">
                            <i data-lucide="log-out" class="w-3 h-3"></i> Sair
                        </button>
                    </form>
                </div>
                <button onclick="toggleTheme()" style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; display: flex; align-items: center;" aria-label="Alternar tema">
                    <i data-lucide="sun-moon" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        {{ $slot }}
    </main>

    <!-- Toast Container -->
    <div class="toast-container" x-data="{ toasts: [] }"
         @notify.window="
            toasts.push({ type: $event.detail.type, message: $event.detail.message, id: Date.now() });
            setTimeout(() => toasts.shift(), 4000);
         ">
        <template x-for="toast in toasts" :key="toast.id">
            <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 10px; padding: 14px 20px; margin-bottom: 8px; min-width: 280px; animation: fadeInUp 0.3s ease;"
                 :style="toast.type === 'success' ? 'border-left: 3px solid #10B981' : toast.type === 'warning' ? 'border-left: 3px solid #F59E0B' : 'border-left: 3px solid #EF4444'">
                <p style="font-size: 0.85rem; color: var(--text-primary);" x-text="toast.message"></p>
            </div>
        </template>
    </div>

    @livewireScripts

    <script>
        function toggleTheme() {
            const isLight = document.documentElement.classList.toggle('theme-light');
            localStorage.setItem('adminTheme', isLight ? 'light' : 'dark');
        }

        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar');
            const toggle = document.querySelector('.mobile-toggle');
            if (sidebar.classList.contains('open') && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });

        lucide.createIcons();
        document.addEventListener('livewire:init', () => {
            if (window.Livewire?.hook) {
                Livewire.hook('morph.updated', () => lucide.createIcons());
            }
        });
    </script>

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</body>
</html>
