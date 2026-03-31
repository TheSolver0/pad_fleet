<div>
    <p class="section-label">Rapports — Statistiques d'utilisation, analyses de coûts</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Tableau de bord</span>
                <div class="module-toolbar-filters">
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="vehicle_category">
                        <option value="">Toutes catégories</option>
                        <option value="leger">Léger</option>
                        <option value="utilitaire">Utilitaire</option>
                        <option value="camionnette">Camionnette</option>
                        <option value="4x4">4x4</option>
                        <option value="lourd">Lourd</option>
                        <option value="bus">Bus</option>
                        <option value="moto">Moto</option>
                        <option value="autre">Autre</option>
                    </select>
                    <input type="date" class="form-control form-control-sm" style="width: 140px;" wire:model="start_date">
                    <input type="date" class="form-control form-control-sm" style="width: 140px;" wire:model="end_date">
                </div>
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
                <div class="col-md-3">
                    <div class="kpi-card h-100">
                        <div class="kpi-card-header">
                            <span class="kpi-card-label">Consommation pièces</span>
                            <span class="kpi-card-icon repa"><i class="bi bi-box-seam"></i></span>
                        </div>
                        <div class="kpi-card-value">{{ format_number($stats['parts_quantity'], 0) }}</div>
                        <div class="kpi-card-sub">{{ format_money($stats['parts_cost'], 0) }} F</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card h-100">
                        <div class="kpi-card-header">
                            <span class="kpi-card-label">Stock (entrées/sorties)</span>
                            <span class="kpi-card-icon vehicules"><i class="bi bi-arrow-left-right"></i></span>
                        </div>
                        <div class="kpi-card-value">{{ format_number($stats['stock_entries_qty'], 0) }} / {{ format_number($stats['stock_exits_qty'], 0) }}</div>
                        <div class="kpi-card-sub">{{ format_money($stats['stock_entries_cost'], 0) }} F / {{ format_money($stats['stock_exits_cost'], 0) }} F</div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ route('reports.fleet.export.pdf', ['start_date' => $start_date, 'end_date' => $end_date, 'vehicle_category' => $vehicle_category]) }}"
                       target="_blank">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Global (PDF)
                    </a>
                    <a class="btn btn-sm btn-outline-success"
                       href="{{ route('reports.stock-movements.export.excel', ['start_date' => $start_date, 'end_date' => $end_date]) }}">
                        <i class="bi bi-file-earmark-excel me-1"></i> Stock entrées/sorties (Excel)
                    </a>
                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ route('reports.stock-movements.export.pdf', ['start_date' => $start_date, 'end_date' => $end_date]) }}"
                       target="_blank">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Stock entrées/sorties (PDF)
                    </a>
                    <a class="btn btn-sm btn-outline-success"
                       href="{{ route('reports.stock-per-vehicle.export.excel', ['start_date' => $start_date, 'end_date' => $end_date, 'vehicle_category' => $vehicle_category]) }}">
                        <i class="bi bi-file-earmark-excel me-1"></i> Consommation par véhicule (Excel)
                    </a>
                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ route('reports.stock-per-vehicle.export.pdf', ['start_date' => $start_date, 'end_date' => $end_date, 'vehicle_category' => $vehicle_category]) }}"
                       target="_blank">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Consommation par véhicule (PDF)
                    </a>
                    <a class="btn btn-sm btn-outline-success"
                       href="{{ route('reports.drivers.export.excel', ['start_date' => $start_date, 'end_date' => $end_date]) }}">
                        <i class="bi bi-file-earmark-excel me-1"></i> Chauffeurs (Excel)
                    </a>
                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ route('reports.drivers.export.pdf', ['start_date' => $start_date, 'end_date' => $end_date]) }}"
                       target="_blank">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Chauffeurs (PDF)
                    </a>
                    <a class="btn btn-sm btn-outline-success"
                       href="{{ route('reports.repairs.export.excel', ['start_date' => $start_date, 'end_date' => $end_date, 'vehicle_category' => $vehicle_category]) }}">
                        <i class="bi bi-file-earmark-excel me-1"></i> Réparations (Excel)
                    </a>
                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ route('reports.repairs.export.pdf', ['start_date' => $start_date, 'end_date' => $end_date, 'vehicle_category' => $vehicle_category]) }}"
                       target="_blank">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Réparations (PDF)
                    </a>
                    <a class="btn btn-sm btn-outline-success"
                       href="{{ route('reports.missions.export.excel', ['start_date' => $start_date, 'end_date' => $end_date]) }}">
                        <i class="bi bi-file-earmark-excel me-1"></i> Missions/déplacements (Excel)
                    </a>
                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ route('reports.missions.export.pdf', ['start_date' => $start_date, 'end_date' => $end_date]) }}"
                       target="_blank">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Missions/déplacements (PDF)
                    </a>
                    <a class="btn btn-sm btn-outline-success"
                       href="{{ route('reports.sinistres.export.excel', ['start_date' => $start_date, 'end_date' => $end_date]) }}">
                        <i class="bi bi-file-earmark-excel me-1"></i> Sinistres (Excel)
                    </a>
                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ route('reports.sinistres.export.pdf', ['start_date' => $start_date, 'end_date' => $end_date]) }}"
                       target="_blank">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Sinistres (PDF)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
