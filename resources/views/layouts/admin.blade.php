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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --gold: #D4A017;
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

        /* RESET BÁSICO */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; width: 100%; overflow-x: hidden; }

        body.admin-body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--dark-bg);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
        }

        h1, h2, h3, h4 { font-family: 'Bebas Neue', cursive; letter-spacing: 0.05em; }
        .hidden { display: none !important; }

        /* SIDEBAR (Fixa na esquerda) */
        .admin-sidebar {
            width: 260px;
            background: rgba(26, 22, 16, 0.95);
            border-right: 1px solid rgba(212,160,23,0.15);
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 50;
            transition: transform 0.3s ease;
        }

        .sidebar-logo {
            padding: 24px;
            border-bottom: 1px solid rgba(212,160,23,0.14);
            text-align: center;
        }
        .sidebar-logo img { width: 120px; margin-bottom: 10px; }
        .sidebar-logo h2 { font-size: 1.5rem; color: var(--gold); }

        .sidebar-section-label {
            padding: 16px 24px 6px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--text-muted);
        }

        .sidebar-nav { flex: 1; overflow-y: auto; padding: 12px 0; }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 24px;
            color: var(--text-secondary);
            text-decoration: none; font-size: 0.85rem; font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            color: var(--gold);
            background: rgba(212,160,23,0.08);
            border-left-color: var(--gold);
        }

        /* WRAPPER (Área direita) */
        .admin-wrapper {
            margin-left: 260px; /* Mesma largura da sidebar */
            flex: 1; /* Preenche o resto */
            display: flex;
            flex-direction: column;
            min-width: 0; /* Impede overflow flex */
            min-height: 100vh;
        }

        /* TOPBAR (Cabecalho superior) */
        .admin-topbar {
            min-height: 80px;
            padding: 20px 32px;
            border-bottom: 1px solid var(--dark-border);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            background: rgba(13,11,7,0.8);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 40;
        }

        /* MAIN CONTENT (Onde o Livewire renderiza) */
        .admin-main {
            flex: 1;
            padding: 40px 32px;
            width: 100%;
        }

        /* MOBILE TOGGLE */
        .mobile-toggle {
            display: none;
            background: none; border: none; color: var(--gold);
            cursor: pointer;
            outline: none;
            padding: 8px;
            margin-right: 8px;
            align-items: center;
            justify-content: center;
        }

        /* RESPONSIVO */
        @media (max-width: 1024px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.open { transform: translateX(0); }
            .admin-wrapper { margin-left: 0; }
            .mobile-toggle { display: flex; }
            .admin-topbar {
                justify-content: space-between;
                padding-top: calc(24px + env(safe-area-inset-top, 0px));
                padding-bottom: 20px;
                padding-left: calc(16px + env(safe-area-inset-left, 0px));
                padding-right: calc(16px + env(safe-area-inset-right, 0px));
                min-height: 70px;
            }
            .admin-main { padding: 20px 16px; }
        }

        /* Relatórios CSS Adicional (Geral) */
        .report-filters { display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-label { font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); }
        .filter-input { background: var(--dark-card); border: 1px solid var(--dark-border); color: var(--text-primary); padding: 8px 12px; border-radius: 6px; }
        .filter-actions { display: flex; gap: 8px; }
        .btn-export { background: #1a1610; border: 1px solid var(--gold); color: var(--gold); padding: 8px 16px; border-radius: 6px; display: flex; gap: 8px; align-items: center; cursor: pointer; }
        .btn-export:hover { background: rgba(212,160,23,0.1); }
        .btn-export-pdf { background: #1a1610; border: 1px solid #EF4444; color: #EF4444; padding: 8px 16px; border-radius: 6px; display: flex; gap: 8px; align-items: center; cursor: pointer; }
        .btn-export-pdf:hover { background: rgba(239,68,68,0.1); }
        
        .report-grid-4 { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 32px; }
        .report-stat { background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; padding: 20px; position: relative; overflow: hidden; }
        .report-stat::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; }
        .report-stat.gold::before { background: var(--gold); }
        .report-stat.green::before { background: #10B981; }
        .report-stat.blue::before { background: #3B82F6; }
        .report-stat.orange::before { background: #F59E0B; }
        .rs-val { font-size: 2rem; font-family: 'Bebas Neue'; letter-spacing: 1px; margin: 8px 0 4px; }
        .rs-label { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; }
        .rs-icon { position: absolute; top: 20px; right: 20px; opacity: 0.3; }
        
        .report-tabs { display: flex; gap: 8px; border-bottom: 1px solid var(--dark-border); margin-bottom: 24px; overflow-x: auto; }
        .rtab { background: none; border: none; color: var(--text-secondary); padding: 12px 20px; border-bottom: 2px solid transparent; cursor: pointer; font-size: 0.85rem; font-weight: 600; white-space: nowrap; }
        .rtab.active { color: var(--gold); border-bottom-color: var(--gold); }
        
        .report-section { background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; padding: 24px; }
        
        .report-table { width: 100%; border-collapse: collapse; }
        .report-table th { text-align: left; padding: 12px; border-bottom: 1px solid var(--dark-border); color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; }
        .report-table td { padding: 12px; border-bottom: 1px solid rgba(61,54,42,0.4); font-size: 0.85rem; }
        .inline-bar { display: flex; align-items: center; gap: 8px; }
        .inline-fill { height: 6px; background: var(--gold); border-radius: 3px; }
        
        .report-batch-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; }
        .batch-report-card { background: rgba(13,11,7,0.4); border: 1px solid var(--dark-border); border-radius: 8px; padding: 16px; }
        .brc-type { font-size: 1.1rem; font-family: 'Bebas Neue'; color: var(--gold); letter-spacing: 1px; }
        .brc-count { font-size: 2rem; font-weight: bold; margin: 8px 0 4px; }
        .brc-revenue { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 12px; }
        .brc-bar { width: 100%; height: 4px; background: var(--dark-bg); border-radius: 2px; overflow: hidden; margin-bottom: 8px; }
        .brc-fill { height: 100%; background: var(--gold); }
        .brc-pct { font-size: 0.7rem; color: var(--text-muted); text-align: right; }
        
        .mode-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; }
        .mode-card { display: flex; flex-direction: column; align-items: center; text-align: center; background: rgba(13,11,7,0.4); border: 1px solid var(--dark-border); border-radius: 12px; padding: 32px 16px; }
        .mode-icon { color: var(--gold); margin-bottom: 16px; }
        .mode-label { font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
        .mode-count { font-size: 2.5rem; font-family: 'Bebas Neue'; margin: 8px 0; }
        .mode-revenue { font-size: 0.9rem; color: var(--text-secondary); }

        /* ═══ FORM ELEMENTS ═══ */
        .form-input, .form-select {
            width: 100%;
            background: var(--dark-bg);
            border: 1px solid var(--dark-border);
            color: var(--text-primary);
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-family: 'Montserrat', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .form-input:focus, .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212,160,23,0.12);
        }
        .form-input::placeholder { color: var(--text-muted); }
        .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 6px;
        }
        .form-error {
            font-size: 0.75rem;
            color: var(--accent-red);
            margin-top: 4px;
        }

        /* ═══ BUTTONS ═══ */
        .btn-gold {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--gold);
            color: #0D0B07;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .btn-gold:hover { background: var(--gold-light); transform: translateY(-1px); box-shadow: 0 4px 15px rgba(212,160,23,0.3); }
        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: var(--gold);
            border: 1px solid rgba(212,160,23,0.3);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            font-family: 'Montserrat', sans-serif;
        }
        .btn-outline:hover { background: rgba(212,160,23,0.08); border-color: var(--gold); }
        .btn-sm {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid var(--dark-border);
            background: rgba(255,255,255,0.03);
            color: var(--text-secondary);
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-sm:hover { background: rgba(255,255,255,0.08); }
        .btn-confirm {
            background: rgba(16,185,129,0.14);
            color: #10B981;
            border-color: rgba(16,185,129,0.3);
        }
        .btn-confirm:hover { background: rgba(16,185,129,0.25); }
        .btn-cancel {
            background: rgba(239,68,68,0.14);
            color: #EF4444;
            border-color: rgba(239,68,68,0.3);
        }
        .btn-cancel:hover { background: rgba(239,68,68,0.25); }

        /* ═══ BADGES ═══ */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .badge-gold { background: rgba(212,160,23,0.14); color: var(--gold); border: 1px solid rgba(212,160,23,0.25); }
        .badge-green { background: rgba(16,185,129,0.14); color: #34D399; border: 1px solid rgba(16,185,129,0.25); }
        .badge-blue { background: rgba(59,130,246,0.14); color: #60A5FA; border: 1px solid rgba(59,130,246,0.25); }
        .badge-yellow { background: rgba(245,158,11,0.14); color: #FBBF24; border: 1px solid rgba(245,158,11,0.25); }
        .badge-red { background: rgba(239,68,68,0.14); color: #F87171; border: 1px solid rgba(239,68,68,0.25); }
        .badge-gray { background: rgba(156,163,175,0.14); color: #9CA3AF; border: 1px solid rgba(156,163,175,0.25); }

        /* ═══ DATA TABLE ═══ */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th {
            text-align: left;
            padding: 12px 16px;
            border-bottom: 1px solid var(--dark-border);
            color: var(--text-muted);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            white-space: nowrap;
            cursor: pointer;
            user-select: none;
        }
        .data-table th:hover { color: var(--gold); }
        .data-table td {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(61,54,42,0.3);
            font-size: 0.85rem;
            color: var(--text-secondary);
            vertical-align: middle;
        }
        .data-table tbody tr { transition: background 0.15s; }
        .data-table tbody tr:hover { background: rgba(212,160,23,0.04); }

        /* ═══ TYPOGRAPHY UTILS ═══ */
        .mono { font-family: 'JetBrains Mono', monospace; }

        /* ═══ ICON SIZING ═══ */
        .w-3 { width: 12px; } .h-3 { height: 12px; }
        .w-4 { width: 16px; } .h-4 { height: 16px; }
        .w-5 { width: 20px; } .h-5 { height: 20px; }
        .w-6 { width: 24px; } .h-6 { height: 24px; }
        .w-8 { width: 32px; } .h-8 { height: 32px; }
        .w-10 { width: 40px; } .h-10 { height: 40px; }
        .w-12 { width: 48px; } .h-12 { height: 48px; }

        /* ═══ GLASS CARD ═══ */
        .glass-card {
            background: rgba(35,31,24,0.7);
            border: 1px solid var(--dark-border);
            border-radius: 16px;
            padding: 24px;
            backdrop-filter: blur(10px);
        }

        /* ═══ TOAST ═══ */
        .toast-container {
            position: fixed;
            top: 80px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
        }

        /* ═══ DROPDOWN ITEM ═══ */
        .dropdown-item:hover { background: rgba(255,255,255,0.05) !important; }

        /* ═══ RESPONSIVE UTILS ═══ */
        @media (min-width: 768px) {
            .md\:block { display: block !important; }
            .md\:hidden { display: none !important; }
        }
        @media (max-width: 767px) {
            .hidden.md\:block { display: none !important; }
        }
    </style>
</head>
<body class="admin-body">

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('alpha-logo-gold.png') }}" alt="Alpha Produções">
            <h2>RENÚNCIA</h2>
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
            <a href="{{ url('/admin/vender') }}" class="{{ request()->is('admin/vender') ? 'active' : '' }}" style="{{ request()->is('admin/vender') ? '' : 'color: #34D399;' }}">
                <span class="nav-icon"><i data-lucide="scan-line" class="w-4 h-4" style="color: #34D399;"></i></span>
                <span style="color: #34D399;">Confirmar Venda</span>
            </a>
            <a href="{{ url('/admin/manual') }}" class="{{ request()->is('admin/manual') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="pen-line" class="w-4 h-4"></i></span> Venda Manual
            </a>
            <a href="{{ url('/admin/reports') }}" class="{{ request()->is('admin/reports') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="bar-chart-2" class="w-4 h-4"></i></span> Relatórios
            </a>

            @if(auth()->user()->canAccessAdmin())
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
            <a href="{{ url('/admin/notifications') }}" class="{{ request()->is('admin/notifications') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="bell" class="w-4 h-4"></i></span> Notificações
            </a>
            @endif

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
    </aside>

    <!-- ÁREA PRINCIPAL -->
    <div class="admin-wrapper">

        <!-- TOPBAR -->
        <header class="admin-topbar">
            <!-- Mobile Toggle -->
            <button class="mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')" aria-label="Abrir menu">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>

            <div style="display: flex; align-items: center; gap: 16px;">
                <div class="hidden md:block" style="text-align: right;">
                    <p style="font-size: 0.85rem; font-weight: 600; color: var(--text-primary);">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p style="font-size: 0.7rem; color: var(--text-muted);">{{ \App\Models\User::ROLES[auth()->user()->role] ?? ucfirst(auth()->user()->role ?? 'Admin') }}</p>
                </div>
                
                <div style="position: relative;" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false" style="background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <img src="{{ auth()->user()->avatar_url ?? asset('alpha-logo-gold.png') }}" alt="" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(212,160,23,0.3);">
                        <i data-lucide="chevron-down" class="w-4 h-4" style="color: var(--text-muted);"></i>
                    </button>
                    
                    <div x-show="open" style="display: none; position: absolute; top: 100%; right: 0; margin-top: 8px; background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 10px; width: 200px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 50; overflow: hidden;">
                        <a href="{{ url('/admin/profile') }}" style="display: flex; align-items: center; gap: 8px; padding: 12px 16px; color: var(--text-primary); text-decoration: none; font-size: 0.85rem; transition: background 0.2s;" class="dropdown-item">
                            <i data-lucide="user-circle" class="w-4 h-4" style="color: var(--text-muted);"></i> O meu perfil
                        </a>
                        <div style="height: 1px; background: var(--dark-border);"></div>
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" style="width: 100%; display: flex; align-items: center; gap: 8px; padding: 12px 16px; background: none; border: none; color: var(--accent-red); font-size: 0.85rem; cursor: pointer; text-align: left; transition: background 0.2s;" class="dropdown-item">
                                <i data-lucide="log-out" class="w-4 h-4"></i> Terminar Sessão
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTEÚDO DA PÁGINA (Livewire) -->
        <main class="admin-main">
            {{ $slot }}
        </main>
    </div>

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
            const sidebar = document.querySelector('.admin-sidebar');
            const toggle = document.querySelector('.mobile-toggle');
            if (sidebar && toggle && sidebar.classList.contains('open') && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });

        lucide.createIcons();
        document.addEventListener('livewire:init', () => {
            if (window.Livewire?.hook) {
                let lucideTimeout = null;
                Livewire.hook('morph.updated', () => {
                    if (lucideTimeout) clearTimeout(lucideTimeout);
                    lucideTimeout = setTimeout(() => lucide.createIcons(), 50);
                });
            }
        });

        // Premium SweetAlert2 Confirmation helper
        window.swalConfirm = function(title, text, confirmCallback, options = {}) {
            Swal.fire({
                title: title,
                text: text,
                icon: options.icon || 'warning',
                showCancelButton: true,
                confirmButtonColor: 'var(--gold)',
                cancelButtonColor: 'rgba(255,255,255,0.08)',
                confirmButtonText: options.confirmButtonText || 'Sim, Confirmar',
                cancelButtonText: 'Cancelar',
                background: '#231F18', // matches var(--dark-card)
                color: '#F5F0E8', // matches var(--text-primary)
                iconColor: 'var(--gold)',
                backdrop: 'rgba(13,11,7,0.7)',
                customClass: {
                    popup: 'swal-premium-popup',
                    confirmButton: 'swal-premium-confirm',
                    cancelButton: 'swal-premium-cancel'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    confirmCallback();
                }
            });
        };
    </script>

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* SweetAlert2 Premium Overrides */
        .swal-premium-popup {
            border: 1px solid rgba(212, 160, 23, 0.25) !important;
            border-radius: 16px !important;
            font-family: 'Montserrat', sans-serif !important;
            box-shadow: 0 20px 50px rgba(0,0,0,0.6) !important;
        }
        .swal-premium-popup .swal2-title {
            font-family: 'Bebas Neue', cursive !important;
            font-size: 2rem !important;
            letter-spacing: 0.05em !important;
            color: var(--gold) !important;
            font-weight: normal !important;
        }
        .swal-premium-popup .swal2-html-container {
            color: var(--text-secondary) !important;
            font-size: 0.95rem !important;
            line-height: 1.5 !important;
        }
        .swal-premium-confirm {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark)) !important;
            color: var(--dark-bg) !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 10px 24px !important;
            border: none !important;
            font-family: 'Montserrat', sans-serif !important;
            text-transform: uppercase !important;
            font-size: 0.85rem !important;
            letter-spacing: 0.05em !important;
            transition: all 0.2s !important;
        }
        .swal-premium-confirm:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 20px rgba(212, 160, 23, 0.25) !important;
        }
        .swal-premium-cancel {
            background: rgba(255, 255, 255, 0.04) !important;
            color: var(--text-secondary) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 8px !important;
            padding: 10px 24px !important;
            font-family: 'Montserrat', sans-serif !important;
            text-transform: uppercase !important;
            font-size: 0.85rem !important;
            letter-spacing: 0.05em !important;
            transition: all 0.2s !important;
        }
        .swal-premium-cancel:hover {
            background: rgba(255, 255, 255, 0.08) !important;
            color: var(--text-primary) !important;
        }
    </style>
</body>
</html>
