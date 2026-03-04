@php
    $current = request()->route()?->getName() ?? 'dashboard';

    $singles = [
        ['route' => 'missions.index', 'label' => 'Planning', 'icon' => 'bi bi-calendar3-week'],
        ['route' => 'schedules.index', 'label' => 'Déplacements', 'icon' => 'bi bi-route'],
        ['route' => 'reports.index', 'label' => 'Rapports', 'icon' => 'bi bi-graph-up-arrow'],
        ['route' => 'reports.vehicle-consumption', 'label' => 'Consommation', 'icon' => 'bi bi-fuel-pump'],
    ];

    $groups = [
        [
            'id' => 'flotte',
            'label' => 'Flotte',
            'icon' => 'bi bi-truck-front-fill',
            'routes' => ['vehicles.index', 'carte-grises.index', 'brands.index', 'vehicle-models.index', 'personnes.index', 'affectations.index'],
            'items' => [
                ['route' => 'vehicles.index', 'label' => 'Véhicules', 'icon' => 'bi bi-car-front-fill'],
                ['route' => 'carte-grises.index', 'label' => 'Cartes grises', 'icon' => 'bi bi-card-checklist'],
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
            'routes' => ['drivers.index', 'drivers.driving-licenses', 'demandeurs.index'],
            'items' => [
                ['route' => 'drivers.index', 'label' => 'Chauffeurs', 'icon' => 'bi bi-person-badge-fill'],
                ['route' => 'drivers.driving-licenses', 'label' => 'Permis de conduire', 'icon' => 'bi bi-card-text'],
                ['route' => 'demandeurs.index', 'label' => 'Demandeurs', 'icon' => 'bi bi-person-lines-fill'],
            ],
        ],
        [
            'id' => 'assurances',
            'label' => 'Assurances & Risques',
            'icon' => 'bi bi-shield-check',
            'routes' => ['assureurs.index', 'assurances.index', 'sinistres.index'],
            'items' => [
                ['route' => 'assureurs.index', 'label' => 'Assureurs', 'icon' => 'bi bi-building'],
                ['route' => 'assurances.index', 'label' => 'Assurances', 'icon' => 'bi bi-shield-check'],
                ['route' => 'sinistres.index', 'label' => 'Sinistres', 'icon' => 'bi bi-exclamation-triangle-fill'],
            ],
        ],
        [
            'id' => 'maintenance',
            'label' => 'Maintenance & Réparations',
            'icon' => 'bi bi-wrench-adjustable',
            'routes' => ['garages.index', 'repairs.index', 'repairs.stock-usage', 'repairs.documents', 'mechanics.index', 'diagnostics.index', 'work-orders.index'],
            'items' => [
                ['route' => 'diagnostics.index', 'label' => 'Diagnostics', 'icon' => 'bi bi-clipboard-check'],
                ['route' => 'work-orders.index', 'label' => 'Bons de travail', 'icon' => 'bi bi-hammer'],
                ['route' => 'garages.index', 'label' => 'Garages', 'icon' => 'bi bi-gear-wide-connected'],
                ['route' => 'repairs.index', 'label' => 'Réparations', 'icon' => 'bi bi-wrench-adjustable'],
                ['route' => 'repairs.stock-usage', 'label' => 'Utilisation stock', 'icon' => 'bi bi-box-arrow-down'],
                ['route' => 'repairs.documents', 'label' => 'Documents', 'icon' => 'bi bi-file-earmark-text'],
                ['route' => 'mechanics.index', 'label' => 'Mécaniciens', 'icon' => 'bi bi-people-fill'],
            ],
        ],
        [
            'id' => 'stocks',
            'label' => 'Gestion des stocks',
            'icon' => 'bi bi-box-seam',
            'routes' => ['stock.index', 'stock.articles', 'stock.categories', 'stock.entries', 'suppliers.index', 'prestataire-evaluations.index'],
            'items' => [
                ['route' => 'stock.index', 'label' => 'Stocks', 'icon' => 'bi bi-layers-fill'],
                ['route' => 'stock.articles', 'label' => 'Articles', 'icon' => 'bi bi-box'],
                ['route' => 'stock.categories', 'label' => 'Catégories', 'icon' => 'bi bi-tags-fill'],
                ['route' => 'stock.entries', 'label' => 'Entrées', 'icon' => 'bi bi-box-arrow-in-down'],
                ['route' => 'suppliers.index', 'label' => 'Fournisseurs', 'icon' => 'bi bi-building'],
                ['route' => 'prestataire-evaluations.index', 'label' => 'Évaluations prestataires', 'icon' => 'bi bi-star'],
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

        @foreach ($singles as $item)
            <a class="nav-link {{ $current === $item['route'] ? 'active' : '' }}" href="{{ route($item['route']) }}">
                <i class="bi {{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span>
            </a>
        @endforeach
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
