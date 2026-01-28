<div>
    <p class="section-label">Rapports — Statistiques d'utilisation, analyses de coûts</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Tableau de bord</span>
                <div class="module-toolbar-filters"></div>
                <div class="module-toolbar-actions">
                    <select class="form-select form-select-sm" style="width: 160px;" wire:model.live="period">
                <option value="month">Ce mois</option>
                        <option value="quarter">Ce trimestre</option>
                        <option value="year">Cette année</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="p-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="kpi-card h-100">
                        <div class="kpi-card-header">
                            <span class="kpi-card-label">Véhicules</span>
                            <span class="kpi-card-icon vehicules"><i class="bi bi-car-front"></i></span>
                        </div>
                        <div class="kpi-card-value">{{ $stats['vehicles_total'] }}</div>
                        <div class="kpi-card-sub">{{ $stats['vehicles_available'] }} dispo. · {{ $stats['vehicles_repair'] }} en réparation</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card h-100">
                        <div class="kpi-card-header">
                            <span class="kpi-card-label">Missions terminées</span>
                            <span class="kpi-card-icon dispo"><i class="bi bi-calendar-check"></i></span>
                        </div>
                        <div class="kpi-card-value">{{ $stats['missions_count'] }}</div>
                        <div class="kpi-card-sub">{{ format_number($stats['missions_distance'], 0) }} km parcourus</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card h-100">
                        <div class="kpi-card-header">
                            <span class="kpi-card-label">Réparations</span>
                            <span class="kpi-card-icon repa"><i class="bi bi-wrench"></i></span>
                        </div>
                        <div class="kpi-card-value">{{ $stats['repairs_count'] }}</div>
                        <div class="kpi-card-sub">{{ format_money($stats['repairs_cost'], 0) }} F coûts</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card h-100">
                        <div class="kpi-card-header">
                            <span class="kpi-card-label">Sinistres</span>
                            <span class="kpi-card-icon sinistres"><i class="bi bi-exclamation-triangle"></i></span>
                        </div>
                        <div class="kpi-card-value">{{ $stats['sinistres_count'] }}</div>
                        <div class="kpi-card-sub">Coût estimé {{ format_money($stats['sinistres_estimated_cost'], 0) }} F</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
