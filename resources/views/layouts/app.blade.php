<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $title ?? 'Tableau de bord') — {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @stack('styles')
    <style>
        :root {
            --pad-blue: #1a5490;
            --pad-cyan: #00b8d4;
            --pad-yellow: #b8d400;
            --sidebar-bg: #eef2f7;
            --sidebar-hover: #e2e8f2;
            --sidebar-active: rgba(26,84,144,0.1);
            --sidebar-text: #4a5568;
            --sidebar-text-active: #1a5490;
            --page-bg: #f4f7fb;
            --card-bg: #ffffff;
            --text-primary: #2d3748;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --shadow-soft: 0 2px 8px rgba(0,0,0,0.04);
            --shadow-soft-hover: 0 4px 16px rgba(0,0,0,0.06);
            --kpi-vehicules: var(--pad-blue);
            --kpi-dispo: var(--pad-yellow);
            --kpi-repa: var(--pad-cyan);
            --kpi-sinistres: #c96b6b;
            --sidebar-width: 260px;
            --sidebar-collapsed: 72px;
        }
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { margin: 0; background: var(--page-bg); color: var(--text-primary); }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            transition: width .25s ease;
            overflow: hidden;
            border-right: 1px solid var(--border);
            z-index: 200;
        }
        .sidebar.collapsed { width: var(--sidebar-collapsed); }
        .sidebar.collapsed .sidebar-brand-text,
        .sidebar.collapsed .nav-link span,
        .sidebar.collapsed .nav-group-trigger span,
        .sidebar.collapsed .sidebar-footer span,
        .sidebar.collapsed .sidebar-logout-btn span { opacity: 0; width: 0; overflow: hidden; white-space: nowrap; }
        .sidebar.collapsed .sidebar-footer-logo { flex-shrink: 0; }
        .sidebar.collapsed .sidebar-brand,
        .sidebar.collapsed .nav-link,
        .sidebar.collapsed .nav-group-trigger,
        .sidebar.collapsed .sidebar-footer,
        .sidebar.collapsed .sidebar-logout-wrap { justify-content: center; padding-left: 0; padding-right: 0; }
        .sidebar-logout-wrap { padding: 1rem 0.75rem; border-top: 1px solid var(--border); flex-shrink: 0; }
        .sidebar-logout-btn {
            width: 100%;
            padding: 0.6rem 1rem;
            border-radius: 10px;
            border: none;
            background: rgba(201, 107, 107, 0.12);
            color: #b55555;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background .2s, color .2s;
            text-decoration: none;
            border-left: 3px solid transparent;
            font-family: inherit;
        }
        .sidebar-logout-btn:hover { background: rgba(201, 107, 107, 0.2); color: #9e4545; }
        .sidebar-logout-btn i { font-size: 1.15rem; min-width: 22px; text-align: center; flex-shrink: 0; }
        .sidebar.collapsed .nav-group-chevron { display: none; }
        .sidebar.collapsed .nav-group-collapse { display: none !important; }
        .sidebar.collapsed .nav-group { margin-bottom: 2px; }
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            transition: padding .25s ease;
        }
        .sidebar-brand-icon {
            width: 40px; height: 40px; min-width: 40px;
            background: transparent;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
        }
        .sidebar-brand-icon .sidebar-logo-img {
            max-height: 100%; max-width: 100%; object-fit: contain;
        }
        .sidebar-brand-text { font-weight: 700; font-size: 1.05rem; letter-spacing: -0.02em; color: var(--sidebar-text-active); transition: opacity .2s; }
        .sidebar-nav { flex: 1; padding: 1rem 0.75rem; overflow-y: auto; overflow-x: hidden; }
        .sidebar-nav .nav-link {
            color: var(--sidebar-text);
            padding: 0.6rem 1rem;
            border-radius: 10px;
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            font-size: 0.9rem;
            transition: background .2s, color .2s;
            text-decoration: none;
            border-left: 3px solid transparent;
        }
        .sidebar-nav .nav-link:hover { background: var(--sidebar-hover); color: var(--sidebar-text-active); }
        .sidebar-nav .nav-link.active { background: var(--sidebar-active); color: var(--sidebar-text-active); box-shadow: 0 1px 3px rgba(0,0,0,0.04); border-left-color: var(--pad-yellow); }
        .sidebar-nav .nav-link i { font-size: 1.15rem; min-width: 22px; text-align: center; flex-shrink: 0; }
        /* Sidebar: groups (dropdowns / modules) */
        .nav-group { margin-bottom: 2px; }
        .nav-group-trigger {
            width: 100%;
            color: var(--sidebar-text);
            padding: 0.6rem 1rem;
            border-radius: 10px;
            border: none;
            background: transparent;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            cursor: pointer;
            transition: background .2s, color .2s;
            text-align: left;
            border-left: 3px solid transparent;
        }
        .nav-group-trigger:hover { background: var(--sidebar-hover); color: var(--sidebar-text-active); }
        .nav-group-trigger.open { background: var(--sidebar-active); color: var(--sidebar-text-active); border-left-color: var(--pad-cyan); }
        .nav-group-trigger i:first-child { font-size: 1.1rem; min-width: 22px; text-align: center; flex-shrink: 0; }
        .nav-group-chevron {
            margin-left: auto;
            font-size: 0.75rem;
            opacity: 0.8;
            transition: transform .2s ease;
        }
        .nav-group-trigger.open .nav-group-chevron { transform: rotate(180deg); }
        .nav-group-collapse { overflow: hidden; }
        .nav-group-collapse:not(.show) { display: none; }
        .nav-group-sub {
            padding: 0.25rem 0 0.35rem 0.75rem;
            border-left: 2px solid var(--border);
            margin-left: 0.6rem;
        }
        .nav-link-sub {
            padding: 0.45rem 0.75rem !important;
            font-size: 0.88rem !important;
            font-weight: 500 !important;
            margin-bottom: 2px !important;
        }
        .nav-link-sub i { font-size: 1rem !important; min-width: 20px !important; }
        .sidebar-footer { padding: 1rem 1.25rem; border-top: 1px solid var(--border); font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.5rem; }
        .sidebar-footer span { transition: opacity .2s; }
        .sidebar-footer-logo { height: 20px; width: auto; object-fit: contain; flex-shrink: 0; }
        .main-content {
            flex: 1; min-width: 0; min-height: 100vh; display: flex; flex-direction: column; position: relative;
            margin-left: var(--sidebar-width);
            transition: margin-left .25s ease;
        }
        .sidebar.collapsed + .main-content { margin-left: var(--sidebar-collapsed); }
        .topbar {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            padding: 0.85rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 0 rgba(0,0,0,0.03);
        }
        .topbar-left { display: flex; align-items: center; gap: 0.75rem; }
        .topbar-logo { height: 36px; width: auto; object-fit: contain; display: block; }
        .topbar-sidebar-toggle {
            width: 42px; height: 42px; border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--card-bg);
            color: var(--text-primary);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.25rem; cursor: pointer;
            transition: background .2s, color .2s, border-color .2s;
        }
        .topbar-sidebar-toggle:hover { background: rgba(184,212,0,0.12); color: #7a9000; border-color: var(--pad-yellow); }
        .topbar h1 { font-size: 1.25rem; font-weight: 700; margin: 0; letter-spacing: -0.02em; color: var(--text-primary); }
        .topbar-actions { display: flex; align-items: center; gap: 0.5rem; }
        .topbar-btn {
            width: 38px; height: 38px; border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--card-bg);
            color: var(--text-muted);
            display: inline-flex; align-items: center; justify-content: center;
            text-decoration: none;
            transition: background .2s, color .2s, border-color .2s;
        }
        .topbar-btn:hover { background: var(--page-bg); color: var(--text-primary); border-color: #dde4ed; }
        .topbar-btn .badge { position: absolute; top: 4px; right: 4px; font-size: 0.65rem; min-width: 16px; height: 16px; padding: 0 4px; border-radius: 8px; }
        .user-trigger {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.35rem 0.5rem 0.35rem 0.35rem;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--card-bg);
            cursor: pointer;
            transition: background .2s, border-color .2s;
            text-align: left;
        }
        .user-trigger:hover { background: var(--page-bg); border-color: #dde4ed; }
        .user-trigger .avatar { flex-shrink: 0; }
        .user-badge { display: flex; flex-direction: column; align-items: flex-start; gap: 0.1rem; }
        .user-name { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); line-height: 1.2; }
        .user-meta { font-size: 0.7rem; color: var(--text-muted); line-height: 1.2; }
        .avatar {
            width: 38px; height: 38px; border-radius: 10px;
            background: linear-gradient(145deg, var(--pad-cyan), var(--pad-blue));
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 600; font-size: 0.85rem;
            box-shadow: 0 2px 10px rgba(0,184,212,0.35);
        }
        .dropdown-menu { border: 1px solid var(--border); border-radius: 12px; box-shadow: var(--shadow-soft-hover); padding: 0.4rem; min-width: 200px; }
        .dropdown-item { border-radius: 8px; padding: 0.5rem 0.75rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.6rem; }
        .dropdown-item i { font-size: 1rem; color: var(--text-muted); }
        .dropdown-item:hover { background: var(--page-bg); }
        .dropdown-item.logout { color: #c96b6b; }
        .dropdown-item.logout i { color: inherit; }
        .dropdown-divider { margin: 0.3rem 0; border-color: var(--border); }
        .dropdown-header { font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; padding: 0.4rem 0.75rem; }
        .notif-item { padding: 0.6rem 0.75rem; border-radius: 8px; display: flex; gap: 0.75rem; font-size: 0.85rem; border: none; background: transparent; width: 100%; text-align: left; color: var(--text-primary); cursor: pointer; }
        .notif-item:hover { background: var(--page-bg); }
        .notif-item.unread { background: rgba(0,184,212,0.08); }
        .notif-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-top: 6px; }
        .notif-dot.info { background: var(--kpi-vehicules); }
        .notif-dot.success { background: var(--kpi-dispo); }
        .notif-dot.warning { background: var(--kpi-repa); }
        .notif-dot.danger { background: var(--kpi-sinistres); }
        .lang-option { padding: 0.4rem 0.75rem; border-radius: 8px; font-size: 0.9rem; cursor: pointer; border: none; background: transparent; width: 100%; text-align: left; }
        .lang-option:hover { background: var(--page-bg); }
        .lang-option.active { background: rgba(0,184,212,0.12); color: var(--pad-cyan); font-weight: 600; }
        .lang-option i { opacity: 0; }
        .lang-option.active i { opacity: 1; }
        .content-inner { padding: 1.5rem; padding-bottom: 4rem; flex: 1; min-height: 0; overflow-y: auto; }
        .section-label { font-size: 0.8rem; font-weight: 600; color: var(--pad-blue); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem; }
        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; }
        @media (max-width: 992px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 576px) { .kpi-grid { grid-template-columns: 1fr; } }
        .kpi-card {
            background: var(--card-bg); border-radius: 14px; padding: 1.35rem;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-soft);
            transition: box-shadow .2s, transform .2s;
        }
        .kpi-card:hover { box-shadow: var(--shadow-soft-hover); transform: translateY(-2px); }
        .kpi-card-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 0.75rem; }
        .kpi-card-label { font-size: 0.88rem; font-weight: 600; color: var(--text-muted); }
        .kpi-card-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; }
        .kpi-card-icon.vehicules { background: rgba(26,84,144,0.12); color: var(--kpi-vehicules); }
        .kpi-card-icon.dispo { background: rgba(184,212,0,0.2); color: #7a9000; }
        .kpi-card-icon.repa { background: rgba(0,184,212,0.15); color: var(--kpi-repa); }
        .kpi-card-icon.sinistres { background: rgba(201,107,107,0.14); color: var(--kpi-sinistres); }
        .kpi-card:nth-child(1) .kpi-card-value { color: var(--pad-blue); }
        .kpi-card:nth-child(2) .kpi-card-value { color: #7a9000; }
        .kpi-card:nth-child(3) .kpi-card-value { color: var(--pad-cyan); }
        .kpi-card:nth-child(4) .kpi-card-value { color: var(--text-primary); }
        .kpi-card-value { font-size: 1.75rem; font-weight: 700; letter-spacing: -0.03em; line-height: 1.2; }
        .kpi-card-sub { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem; }
        .activity-card { background: var(--card-bg); border-radius: 14px; border: 1px solid var(--border); box-shadow: var(--shadow-soft); margin-top: 1.5rem; overflow: hidden; }
        .activity-card-header { padding: 1rem 1.25rem; border-bottom: 1px solid var(--border); border-top: 3px solid var(--pad-cyan); font-size: 0.95rem; color: var(--text-primary); }
        .module-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem 1rem; width: 100%; }
        .module-toolbar-title { font-weight: 600; margin-right: 0.25rem; white-space: nowrap; }
        .module-toolbar-filters { display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem; }
        .module-toolbar .module-toolbar-actions { margin-left: auto; flex-shrink: 0; }
        .activity-list { padding: 0; margin: 0; list-style: none; }
        .activity-list li { padding: 0.8rem 1.25rem; border-bottom: 1px solid #f4f7fb; display: flex; align-items: center; gap: 1rem; }
        .activity-list li:last-child { border-bottom: 0; }
        .activity-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .activity-dot.info { background: var(--kpi-vehicules); }
        .activity-dot.success { background: var(--kpi-dispo); }
        .activity-dot.warning { background: var(--kpi-repa); }
        .activity-dot.danger { background: var(--kpi-sinistres); }
        .activity-text { font-size: 0.9rem; color: var(--text-primary); }
        .activity-time { font-size: 0.8rem; color: var(--text-muted); margin-left: auto; }
        .dropdown-toggle-no-caret::after { display: none; }
        .notif-badge { background: var(--pad-yellow) !important; color: #1a5490 !important; font-weight: 700; }
        .modal-content { border-radius: 14px; border: 1px solid var(--border); box-shadow: 0 8px 32px rgba(0,0,0,0.12); }
        .modal-header { border-bottom: 1px solid var(--border); padding: 1rem 1.25rem; border-top-left-radius: 14px; border-top-right-radius: 14px; }
        .modal-footer { border-top: 1px solid var(--border); padding: 1rem 1.25rem; }
        .toast-pad { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; box-shadow: var(--shadow-soft-hover); }
        .app-footer {
            position: fixed;
            bottom: 0;
            left: var(--sidebar-width);
            right: 0;
            padding: 0.75rem 1.5rem;
            border-top: 1px solid var(--border);
            background: var(--card-bg);
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
            z-index: 50;
            transition: left 0.25s ease;
        }
        .sidebar.collapsed ~ main .app-footer { left: var(--sidebar-collapsed); }
        .app-footer-text { display: inline-block; }
    </style>
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>
    <div id="sidebar-overlay" class="sidebar-overlay"></div>
<div class="d-flex">
    @include('layouts.components.navigation.sidebar')
    <main class="main-content">
        @include('layouts.components.navigation.header')
        <div class="content-inner">
            @isset($slot){{ $slot }}@else @yield('content') @endisset
        </div>
        @include('layouts.components.navigation.footer')
    </main>
</div>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;" id="toast-container"></div>

<script src="https://cdn.tiny.cloud/1/{{ config('services.tinymce.api_key') }}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const stored = localStorage.getItem('sidebarCollapsed');
    if (stored === '1' && sidebar) sidebar.classList.add('collapsed');
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle && sidebar) {
        const updateIcon = function() {
            const icon = sidebarToggle.querySelector('i');
            if (!icon) return;
            if (sidebar.classList.contains('collapsed')) {
                icon.className = 'bi bi-layout-sidebar-inset';
                sidebarToggle.title = 'Ouvrir le menu';
            } else {
                icon.className = 'bi bi-layout-sidebar-inset-reverse';
                sidebarToggle.title = 'Réduire le menu';
            }
        };
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
            updateIcon();
        });
        updateIcon();
    }
    const fullscreenBtn = document.getElementById('fullscreenBtn');
    if (fullscreenBtn) {
        fullscreenBtn.addEventListener('click', function() {
            if (!document.fullscreenElement) document.documentElement.requestFullscreen();
            else document.exitFullscreen();
        });
        document.addEventListener('fullscreenchange', function() {
            const icon = fullscreenBtn.querySelector('i');
            if (icon) {
                icon.className = document.fullscreenElement ? 'bi bi-fullscreen-exit' : 'bi bi-fullscreen';
                fullscreenBtn.title = document.fullscreenElement ? 'Quitter le plein écran' : 'Plein écran';
            }
        });
    }
    document.querySelectorAll('.dropdown-toggle-no-caret').forEach(function(b) { b.classList.add('dropdown-toggle'); });
    document.querySelectorAll('.lang-option').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.lang-option').forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
        });
    });
});
document.addEventListener('livewire:init', function() {
    Livewire.on('notify', function(e) {
        var type = (e && e.type) ? e.type : 'success';
        var message = (e && e.message) ? e.message : 'Enregistré.';
        var container = document.getElementById('toast-container');
        if (!container) return;
        var alertClass = type === 'success' ? 'alert-success' : (type === 'error' ? 'alert-danger' : 'alert-info');
        var el = document.createElement('div');
        el.className = 'toast-pad alert ' + alertClass + ' show mb-2 shadow-sm';
        el.setAttribute('role', 'alert');
        el.textContent = message;
        container.appendChild(el);
        setTimeout(function() { if (el.parentNode) el.remove(); }, 4000);
    });
});
</script>
@livewireScripts
@stack('scripts')

</body>
</html>
