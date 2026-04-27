<div>
    <p class="section-label">Analyses des déplacements — Statuts, directions, chauffeurs, période</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Filtres</span>
                <div class="module-toolbar-filters">
                    <input type="date" class="form-control form-control-sm" style="width: 145px;" wire:model.live="start_date">
                    <span class="text-muted small align-self-center">→</span>
                    <input type="date" class="form-control form-control-sm" style="width: 145px;" wire:model.live="end_date">
                    <select class="form-select form-select-sm" style="width: 190px;" wire:model.live="driver_id">
                        <option value="">Tous chauffeurs</option>
                        @foreach($drivers as $d)
                            <option value="{{ $d->id }}">{{ $d->full_name }}</option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-sm" style="width: 190px;" wire:model.live="direction_id">
                        <option value="">Toutes directions</option>
                        @foreach($directions as $dir)
                            <option value="{{ $dir->id }}">{{ $dir->name }}</option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-sm" style="width: 170px;" wire:model.live="status">
                        <option value="">Tous statuts</option>
                        @foreach($statusOptions as $statusValue => $statusLabel)
                            <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <a class="btn btn-sm btn-outline-success"
                       href="{{ route('reports.missions-analytics.export.excel', ['start_date' => $start_date, 'end_date' => $end_date, 'driver_id' => $driver_id, 'direction_id' => $direction_id, 'status' => $status]) }}">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                    </a>
                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ route('reports.missions-analytics.export.pdf', ['start_date' => $start_date, 'end_date' => $end_date, 'driver_id' => $driver_id, 'direction_id' => $direction_id, 'status' => $status]) }}"
                       target="_blank">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                    </a>
                </div>
            </div>
        </div>

        <div class="p-4">
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="kpi-card h-100">
                        <div class="kpi-card-label">Total déplacements</div>
                        <div class="kpi-card-value">{{ $stats['total'] }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card h-100">
                        <div class="kpi-card-label">Distance totale (km)</div>
                        <div class="kpi-card-value">{{ format_number($stats['distance'], 0) }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="kpi-card h-100">
                        <div class="kpi-card-label">Répartition statuts</div>
                        <div class="small mt-2 d-flex flex-wrap gap-2">
                            <span class="badge bg-primary">Programmée: {{ $stats['by_status']['programmed'] }}</span>
                            <span class="badge bg-info">En cours: {{ $stats['by_status']['in_progress'] }}</span>
                            <span class="badge bg-dark">Reportée: {{ $stats['by_status']['postponed'] }}</span>
                            <span class="badge bg-success">Terminée: {{ $stats['by_status']['completed'] }}</span>
                            <span class="badge bg-warning text-dark">En attente: {{ $stats['by_status']['pending'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">Top directions (déplacements)</div>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Direction</th>
                                        <th class="text-end">Missions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['top_directions'] as $row)
                                        <tr>
                                            <td>{{ $row['name'] }}</td>
                                            <td class="text-end">{{ $row['missions'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2" class="text-center text-muted">Aucune donnée.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">Top chauffeurs (déplacements)</div>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Chauffeur</th>
                                        <th class="text-end">Missions</th>
                                        <th class="text-end">Distance (km)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['top_drivers'] as $row)
                                        <tr>
                                            <td>{{ $row['name'] }}</td>
                                            <td class="text-end">{{ $row['missions'] }}</td>
                                            <td class="text-end">{{ format_number($row['distance'], 0) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted">Aucune donnée.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card h-100">
                        <div class="card-header">Top techniciens missionnés (rotation)</div>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Technicien</th>
                                        <th class="text-end">Missions</th>
                                        <th class="text-end">Distance cumulée (km)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['top_technicians'] as $row)
                                        <tr>
                                            <td>{{ $row['name'] }}</td>
                                            <td class="text-end">{{ $row['missions'] }}</td>
                                            <td class="text-end">{{ format_number($row['distance'], 0) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted">Aucune donnée.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

