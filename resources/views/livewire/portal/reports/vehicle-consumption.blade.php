<div>
    <p class="section-label">Consommation par type de véhicule</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Analyse de consommation</span>
                <div class="module-toolbar-filters">
                    <select class="form-select form-select-sm" style="width: 120px;" wire:model.live="period">
                        <option value="week">Semaine</option>
                        <option value="month">Mois</option>
                        <option value="quarter">Trimestre</option>
                        <option value="year">Année</option>
                    </select>
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
                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ route('reports.vehicle-consumption.export.pdf', ['start_date' => $start_date, 'end_date' => $end_date, 'vehicle_category' => $vehicle_category]) }}"
                       target="_blank">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                    </a>
                    <a class="btn btn-sm btn-outline-success"
                       href="{{ route('reports.vehicle-consumption.export.excel', ['start_date' => $start_date, 'end_date' => $end_date, 'vehicle_category' => $vehicle_category]) }}">
                        <i class="bi bi-file-earmark-excel me-1"></i> Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Alertes pneus -->
        @if(!empty($tireAlerts))
        <div class="alert alert-warning mb-4">
            <h6 class="alert-heading">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Alertes de stock pneus
            </h6>
            @foreach($tireAlerts as $alert)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong>{{ $alert['tire_size'] }}</strong> - {{ $alert['article_name'] }}
                        <small class="text-muted">(Compatible: {{ $alert['compatible_vehicles'] }})</small>
                    </div>
                    <span class="badge bg-{{ $alert['alert_level'] === 'critical' ? 'danger' : 'warning' }}">
                        {{ $alert['stock_quantity'] }} en stock
                    </span>
                </div>
            @endforeach
        </div>
        @endif

        <!-- Tableau de consommation par type de véhicule -->
        <div class="row">
            @foreach($consumptionByType as $category => $data)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-{{ $category === 'leger' ? 'primary' : ($category === 'lourd' ? 'danger' : 'secondary') }} text-white">
                        <h6 class="mb-0">
                            {{ ucfirst($category) }}
                            <span class="badge bg-light text-dark ms-2">{{ $data['vehicle_count'] }} véhicule{{ $data['vehicle_count'] > 1 ? 's' : '' }}</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <div class="fw-bold text-primary">{{ $data['total_repairs'] }}</div>
                                <small class="text-muted">Réparations</small>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold text-success">{{ number_format($data['total_parts_cost'], 0, ',', ' ') }} €</div>
                                <small class="text-muted">Coût pièces</small>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold text-info">{{ number_format($data['total_distance'], 0, ',', ' ') }} km</div>
                                <small class="text-muted">Distance</small>
                            </div>
                        </div>
                        
                        @if($data['total_distance'] > 0)
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="fw-bold text-warning">{{ number_format($data['total_fuel_consumed'], 1, ',', ' ') }} L</div>
                                <small class="text-muted">Carburant</small>
                            </div>
                            <div class="col-6">
                                <div class="fw-bold text-danger">{{ number_format($data['average_fuel_per_100km'], 1, ',', ' ') }} L/100km</div>
                                <small class="text-muted">Moyenne</small>
                            </div>
                        </div>
                        @endif

                        @if(!empty($data['parts_used']))
                        <div class="mt-3">
                            <h6 class="text-muted small mb-2">Pièces les plus utilisées :</h6>
                            @foreach(array_slice($data['parts_used'], 0, 3) as $part)
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-truncate">{{ $part['name'] }}</small>
                                <small class="text-muted">{{ $part['quantity'] }}x / {{ number_format($part['total_cost'], 0, ',', ' ') }} €</small>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Top 20 des pièces consommées -->
        <div class="mt-4">
            <h5 class="mb-3">Top 20 des pièces consommées</h5>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Pièce</th>
                            <th>Référence</th>
                            <th>Quantité totale</th>
                            <th>Coût total</th>
                            <th>Nombre d'utilisations</th>
                            <th>Coût moyen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topParts as $part)
                        <tr>
                            <td class="fw-semibold">{{ $part['name'] }}</td>
                            <td><span class="badge bg-light text-dark">{{ $part['reference'] }}</span></td>
                            <td>{{ $part['total_quantity'] }}</td>
                            <td class="fw-semibold">{{ number_format($part['total_cost'], 2, ',', ' ') }} €</td>
                            <td>{{ $part['usage_count'] }}</td>
                            <td>{{ number_format($part['total_cost'] / $part['total_quantity'], 2, ',', ' ') }} €</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
