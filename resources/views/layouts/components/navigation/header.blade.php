@php
    $user = auth()->user();
    $userName = $user?->name ?? 'Utilisateur';
    $userInitials = '??';
    if ($user && $user->name) {
        $parts = preg_split('/\s+/', trim($user->name), 2);
        $userInitials = count($parts) >= 2
            ? strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1))
            : strtoupper(mb_substr($user->name, 0, 2));
    }
    $userMeta = trim(($user?->matricule ? 'Mat. ' . $user->matricule : '') . ($user?->occupation ? ' · ' . $user->occupation : ''));
@endphp
<header class="topbar">
    <div class="topbar-left">
        <button
    id="sidebar-hamburger"
    class="topbar-hamburger"
    aria-label="Ouvrir le menu"
    aria-expanded="false"
    aria-controls="sidebar"
>
    <i class="bi bi-list"></i>
</button>
        <button type="button" class="topbar-sidebar-toggle" id="sidebarToggle" title="Réduire / Ouvrir le menu">
            <i class="bi bi-layout-sidebar-inset-reverse"></i>
        </button>
        {{-- <img src="{{ asset('img/logo.png') }}" alt="PAD" class="topbar-logo"> --}}
        <h1>@yield('title', $title ?? 'Tableau de bord')</h1>
    </div>
    <div class="topbar-actions">
        <button type="button" class="topbar-btn position-relative" id="fullscreenBtn" title="Plein écran">
            <i class="bi bi-fullscreen"></i>
        </button>

        <div class="dropdown">
            <button class="topbar-btn dropdown-toggle dropdown-toggle-no-caret" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Langue">
                <i class="bi bi-translate"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><button class="lang-option active" type="button" data-lang="fr"><i class="bi bi-check-lg me-2"></i> Français</button></li>
                <li><button class="lang-option" type="button" data-lang="en"><i class="bi bi-check-lg me-2"></i> English</button></li>
            </ul>
        </div>

        <div class="dropdown">
            <button class="topbar-btn position-relative dropdown-toggle dropdown-toggle-no-caret" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                <i class="bi bi-bell"></i>
                <span class="badge notif-badge">3</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 320px; max-width: 360px;">
                <li class="dropdown-header">Notifications</li>
                <li>
                    <button class="notif-item unread" type="button">
                        <span class="notif-dot success"></span>
                        <div>
                            <div>Véhicule AA-123-BB rendu disponible</div>
                            <small class="text-muted">Il y a 15 min</small>
                        </div>
                    </button>
                </li>
                <li>
                    <button class="notif-item unread" type="button">
                        <span class="notif-dot warning"></span>
                        <div>
                            <div>Réparation terminée — XY-456-CD</div>
                            <small class="text-muted">Il y a 1 h</small>
                        </div>
                    </button>
                </li>
                <li>
                    <button class="notif-item unread" type="button">
                        <span class="notif-dot danger"></span>
                        <div>
                            <div>Nouveau sinistre déclaré #12</div>
                            <small class="text-muted">Il y a 2 h</small>
                        </div>
                    </button>
                </li>
                <li>
                    <button class="notif-item" type="button">
                        <span class="notif-dot info"></span>
                        <div>
                            <div>Assurance ZZ-789-EF renouvelée</div>
                            <small class="text-muted">Hier</small>
                        </div>
                    </button>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-center text-primary small" href="#">Voir toutes les notifications</a></li>
            </ul>
        </div>

        <div class="dropdown">
            <button class="user-trigger dropdown-toggle dropdown-toggle-no-caret" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Profil">
                <div class="avatar">{{ $userInitials }}</div>
                <div class="user-badge">
                    <span class="user-name">{{ $userName }}</span>
                    <span class="user-meta">{{ $userMeta ?: '—' }}</span>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li class="dropdown-header">Mon compte</li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Mon profil</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> Paramètres</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="post" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item logout border-0 bg-transparent w-100 text-start">
                            <i class="bi bi-box-arrow-right"></i> Se déconnecter
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
