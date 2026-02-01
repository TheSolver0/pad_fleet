@php
    $current = request()->route()?->getName() ?? 'dashboard';

    $groups = [
        [
            'id' => 'flotte',
            'label' => 'Flotte',
            'icon' => 'bi bi-truck-front-fill',
            'routes' => ['vehicles.index', 'brands.index', 'vehicle-models.index', 'personnes.index', 'affectations.index'],
            'items' => [
                ['route' => 'vehicles.index', 'label' => 'Véhicules', 'icon' => 'bi bi-car-front-fill'],
                ['route' => 'affectations.index', 'label' => 'Affectations', 'icon' => 'bi bi-link-45deg'],
                ['route' => 'brands.index', 'label' => 'Marques', 'icon' => 'bi bi-tag-fill'],
                ['route' => 'vehicle-models.index', 'label' => 'Modèles', 'icon' => 'bi bi-card-list'],
                ['route' => 'personnes.index', 'label' => 'Personnes', 'icon' => 'bi bi-person-vcard-fill'],
            ],
        ],
        [
            'id' => 'organisation',
            'label' => 'Organisation',
            'icon' => 'bi bi-building',
            'routes' => ['organisation.directions.index', 'organisation.departments.index', 'organisation.services.index'],
            'items' => [
                ['route' => 'organisation.directions.index', 'label' => 'Directions', 'icon' => 'bi bi-diagram-3'],
                ['route' => 'organisation.departments.index', 'label' => 'Départements', 'icon' => 'bi bi-folder'],
                ['route' => 'organisation.services.index', 'label' => 'Services', 'icon' => 'bi bi-briefcase'],
            ],
        ],
        [
            'id' => 'personnel',
            'label' => 'Personnel',
            'icon' => 'bi bi-people-fill',
            'routes' => ['drivers.index', 'demandeurs.index'],
            'items' => [
                ['route' => 'drivers.index', 'label' => 'Chauffeurs', 'icon' => 'bi bi-person-badge-fill'],
                ['route' => 'demandeurs.index', 'label' => 'Demandeurs', 'icon' => 'bi bi-person-lines-fill'],
            ],
        ],
        [
            'id' => 'risques',
            'label' => 'Risques & maintenance',
            'icon' => 'bi bi-wrench-adjustable',
            'routes' => ['sinistres.index', 'garages.index', 'repairs.index'],
            'items' => [
                ['route' => 'sinistres.index', 'label' => 'Sinistres', 'icon' => 'bi bi-exclamation-triangle-fill'],
                ['route' => 'garages.index', 'label' => 'Garages', 'icon' => 'bi bi-gear-wide-connected'],
                ['route' => 'repairs.index', 'label' => 'Réparations', 'icon' => 'bi bi-wrench-adjustable'],
            ],
        ],
        [
            'id' => 'admin',
            'label' => 'Administration',
            'icon' => 'bi bi-gear-fill',
            'routes' => ['audit.index'],
            'items' => [
                ['route' => 'audit.index', 'label' => 'Journal d\'audit', 'icon' => 'bi bi-journal-text'],
            ],
            'can' => 'audits',
        ],
    ];

    $singles = [
        ['route' => 'missions.index', 'label' => 'Planning', 'icon' => 'bi bi-calendar3-week'],
        ['route' => 'assurances.index', 'label' => 'Assurances', 'icon' => 'bi bi-shield-check'],
        ['route' => 'reports.index', 'label' => 'Rapports', 'icon' => 'bi bi-graph-up-arrow'],
    ];
@endphp
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('img/logo.png') }}" alt="PAD" class="sidebar-logo-img">
        </div>
        <span class="sidebar-brand-text">{{ config('app.name') }}</span>
    </div>
    <nav class="sidebar-nav">
        <a class="nav-link {{ $current === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="bi bi-grid-1x2-fill"></i><span>Tableau de bord</span>
        </a>

        @foreach ($groups as $group)
            @if (isset($group['can']) && !Gate::allows($group['can']))
                @continue
            @endif
            @php
                $isOpen = in_array($current, $group['routes'], true);
                $collapseId = 'collapse-' . $group['id'];
            @endphp
            <div class="nav-group">
                <button type="button" class="nav-group-trigger {{ $isOpen ? 'open' : '' }}" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="{{ $isOpen ? 'true' : 'false' }}" aria-controls="{{ $collapseId }}">
                    <i class="bi {{ $group['icon'] }}"></i>
                    <span>{{ $group['label'] }}</span>
                    <i class="bi bi-chevron-down nav-group-chevron"></i>
                </button>
                <div class="collapse nav-group-collapse {{ $isOpen ? 'show' : '' }}" id="{{ $collapseId }}">
                    <div class="nav-group-sub">
                        @foreach ($group['items'] as $item)
                            @if (isset($item['can']) && !Gate::allows($item['can']))
                                @continue
                            @endif
                            <a class="nav-link nav-link-sub {{ $current === $item['route'] ? 'active' : '' }}" href="{{ route($item['route']) }}">
                                <i class="bi {{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        {{-- @foreach ($singles as $item)
            <a class="nav-link {{ $current === $item['route'] ? 'active' : '' }}" href="{{ route($item['route']) }}">
                <i class="bi {{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span>
            </a>
        @endforeach --}}
    </nav>
    <div class="sidebar-logout-wrap">
        <form method="post" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="sidebar-logout-btn w-100 text-start" title="Se déconnecter">
                <i class="bi bi-box-arrow-left"></i>
                <span>Se déconnecter</span>
            </button>
        </form>
    </div>
</aside>
