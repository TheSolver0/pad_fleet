@push('styles')
<style>
.dashboard-page .kpi-grid-sm { grid-template-columns: repeat(4, 1fr); }
@media (max-width: 992px) { .dashboard-page .kpi-grid-sm { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 576px) { .dashboard-page .kpi-grid-sm { grid-template-columns: 1fr; } }
.kpi-card-sm .kpi-card-value { font-size: 1.35rem; }
.kpi-card-sm .kpi-card-icon.missions { background: rgba(26, 84, 144, 0.12); color: var(--kpi-vehicules); }
.kpi-card-sm .kpi-card-icon.assurances { background: rgba(0, 184, 212, 0.15); color: var(--kpi-repa); }
.kpi-card-sm .kpi-card-icon.km { background: rgba(122, 144, 0, 0.2); color: #7a9000; }
.kpi-card-sm .kpi-card-icon.cost { background: rgba(100, 120, 160, 0.15); color: #4a6fa5; }
.dashboard-section-title { font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; display: flex; align-items: center; }
.insights-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 0.75rem; }
.insight-card { display: flex; align-items: flex-start; gap: 0.75rem; padding: 1rem 1.25rem; border-radius: 12px; border: 1px solid var(--border); background: var(--card-bg); box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
.insight-card .insight-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
.insight-card-success .insight-icon { background: rgba(122, 144, 0, 0.2); color: #7a9000; }
.insight-card-info .insight-icon { background: rgba(26, 84, 144, 0.12); color: var(--kpi-vehicules); }
.insight-card-warning .insight-icon { background: rgba(0, 184, 212, 0.15); color: var(--kpi-repa); }
.insight-card-danger .insight-icon { background: rgba(201, 107, 107, 0.15); color: var(--kpi-sinistres); }
.insight-title { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); margin-bottom: 0.25rem; }
.insight-text { font-size: 0.85rem; color: var(--text-muted); line-height: 1.4; }
.charts-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }
.charts-grid-3 { grid-template-columns: repeat(3, 1fr); }
@media (max-width: 992px) { .charts-grid, .charts-grid-3 { grid-template-columns: 1fr; } }
.chart-card { background: var(--card-bg); border-radius: 14px; border: 1px solid var(--border); box-shadow: 0 2px 8px rgba(0,0,0,0.04); overflow: hidden; }
.chart-card-header { padding: 1rem 1.25rem; border-bottom: 1px solid var(--border); font-weight: 600; font-size: 0.95rem; color: var(--text-primary); }
.chart-wrap { padding: 1rem 1.25rem; position: relative; }
.chart-wrap-pie { min-height: 240px; }
.chart-wrap canvas { max-width: 100%; }
</style>
@endpush
<div class="dashboard-page container">
    <p class="section-label">Vue d’ensemble</p>
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-card-header">
                <span class="kpi-card-label">Véhicules</span>
                <span class="kpi-card-icon vehicules"><i class="bi bi-car-front"></i></span>
            </div>
            <div class="kpi-card-value">{{ number_format($kpis['total_vehicles']) }}</div>
            <div class="kpi-card-sub">Total parc</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-card-header">
                <span class="kpi-card-label">Disponibles</span>
                <span class="kpi-card-icon dispo"><i class="bi bi-check-circle"></i></span>
            </div>
            <div class="kpi-card-value">{{ number_format($kpis['available']) }}</div>
            <div class="kpi-card-sub">{{ $kpis['availability_rate'] }}% du parc — utilisables</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-card-header">
                <span class="kpi-card-label">En réparation</span>
                <span class="kpi-card-icon repa"><i class="bi bi-wrench"></i></span>
            </div>
            <div class="kpi-card-value">{{ number_format($kpis['repair']) }}</div>
            <div class="kpi-card-sub">{{ $kpis['repairs_ongoing'] }} non clôturée(s)</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-card-header">
                <span class="kpi-card-label">Sinistres</span>
                <span class="kpi-card-icon sinistres"><i class="bi bi-exclamation-triangle"></i></span>
            </div>
            <div class="kpi-card-value">{{ number_format($kpis['sinistres_open']) }}</div>
            <div class="kpi-card-sub">À traiter ou en cours</div>
        </div>
    </div>

    {{-- Deuxième ligne KPIs léger --}}
    <div class="kpi-grid kpi-grid-sm mt-3">
        <div class="kpi-card kpi-card-sm">
            <div class="kpi-card-header">
                <span class="kpi-card-label">Missions ce mois</span>
                <span class="kpi-card-icon missions"><i class="bi bi-calendar3-week"></i></span>
            </div>
            <div class="kpi-card-value">{{ number_format($kpis['missions_this_month']) }}</div>
        </div>
        <div class="kpi-card kpi-card-sm">
            <div class="kpi-card-header">
                <span class="kpi-card-label">Assurances (30 j)</span>
                <span class="kpi-card-icon assurances"><i class="bi bi-shield-check"></i></span>
            </div>
            <div class="kpi-card-value">{{ number_format($kpis['insurances_expiring_30']) }}</div>
            <div class="kpi-card-sub">À renouveler</div>
        </div>
        <div class="kpi-card kpi-card-sm">
            <div class="kpi-card-header">
                <span class="kpi-card-label">Km ce mois</span>
                <span class="kpi-card-icon km"><i class="bi bi-speedometer2"></i></span>
            </div>
            <div class="kpi-card-value">{{ number_format($quickStats['km_this_month']) }}</div>
        </div>
        <div class="kpi-card kpi-card-sm">
            <div class="kpi-card-header">
                <span class="kpi-card-label">Réparations (interne)</span>
                <span class="kpi-card-icon cost"><i class="bi bi-wrench"></i></span>
            </div>
            <div class="kpi-card-value">{{ number_format($quickStats['repair_cost_internal_this_month'], 0, ',', ' ') }}</div>
            <div class="kpi-card-sub">FCFA ce mois</div>
        </div>
        <div class="kpi-card kpi-card-sm">
            <div class="kpi-card-header">
                <span class="kpi-card-label">Réparations (externe)</span>
                <span class="kpi-card-icon cost"><i class="bi bi-gear"></i></span>
            </div>
            <div class="kpi-card-value">{{ number_format($quickStats['repair_cost_external_this_month'], 0, ',', ' ') }}</div>
            <div class="kpi-card-sub">FCFA ce mois</div>
        </div>
    </div>

    {{-- Insights --}}
    {{-- @if(count($insights) > 0)
        <div class="dashboard-section mt-4">
            <h3 class="dashboard-section-title"><i class="bi bi-lightbulb me-2"></i>Points d’attention</h3>
            <div class="insights-grid">
                @foreach($insights as $insight)
                    <div class="insight-card insight-card-{{ $insight['type'] }}">
                        <div class="insight-icon"><i class="bi {{ $insight['icon'] }}"></i></div>
                        <div class="insight-body">
                            <div class="insight-title">{{ $insight['title'] }}</div>
                            <div class="insight-text">{{ $insight['text'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif --}}

    {{-- Graphiques --}}
    <div class="dashboard-section mt-4">
        <h3 class="dashboard-section-title"><i class="bi bi-bar-chart-line me-2"></i>Statistiques</h3>
        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-card-header">Répartition par statut</div>
                <div class="chart-wrap chart-wrap-pie">
                    <canvas id="chartStatus" height="220"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-card-header">Répartition par catégorie</div>
                <div class="chart-wrap chart-wrap-pie">
                    <canvas id="chartCategory" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="charts-grid charts-grid-3 mt-3">
            <div class="chart-card">
                <div class="chart-card-header">Missions par mois</div>
                <div class="chart-wrap">
                    <canvas id="chartMissions" height="180"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-card-header">Sinistres par mois</div>
                <div class="chart-wrap">
                    <canvas id="chartSinistres" height="180"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-card-header">Coût réparations (FCFA)</div>
                <div class="chart-wrap">
                    <canvas id="chartRepairs" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques voyages par chauffeur et par véhicule --}}
    <div class="dashboard-section mt-4">
        <h3 class="dashboard-section-title"><i class="bi bi-graph-up-arrow me-2"></i>Statistiques voyages</h3>
        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-card-header"><i class="bi bi-person-badge me-2"></i>Par chauffeur (top 10)</div>
                <div class="chart-wrap">
                    <table class="table table-sm table-hover mb-0">
                        <thead><tr><th>Chauffeur</th><th class="text-end">Missions</th><th class="text-end">Km</th></tr></thead>
                        <tbody>
                            @forelse($driverTripStats as $row)
                                <tr>
                                    <td>{{ $row['driver_name'] }}</td>
                                    <td class="text-end">{{ number_format($row['missions_count']) }}</td>
                                    <td class="text-end">{{ number_format($row['total_km']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted text-center py-3">Aucune mission</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-card-header"><i class="bi bi-car-front me-2"></i>Par véhicule (top 10)</div>
                <div class="chart-wrap">
                    <table class="table table-sm table-hover mb-0">
                        <thead><tr><th>Véhicule</th><th class="text-end">Missions</th><th class="text-end">Km</th></tr></thead>
                        <tbody>
                            @forelse($vehicleTripStats as $row)
                                <tr>
                                    <td>{{ $row['vehicle_registration'] }}</td>
                                    <td class="text-end">{{ number_format($row['missions_count']) }}</td>
                                    <td class="text-end">{{ number_format($row['total_km']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted text-center py-3">Aucune mission</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Activité récente --}}
    <div class="dashboard-section mt-4">
        <div class="activity-card">
            <div class="activity-card-header">
                <i class="bi bi-activity me-2"></i>Activité récente
            </div>
            <ul class="activity-list">
                @forelse($activity as $item)
                    <li>
                        @php
                            $dot = match($item['action']) {
                                'created' => 'success',
                                'updated' => 'info',
                                'deleted' => 'danger',
                                default => 'info',
                            };
                        @endphp
                        <span class="activity-dot {{ $dot }}"></span>
                        <span class="activity-text">{{ $item['description'] }}</span>
                        <span class="activity-time">{{ $item['time_ago'] }}</span>
                    </li>
                @empty
                    <li class="text-muted"><span class="activity-text">Aucune activité récente</span></li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sectionLabel = document.querySelector('.dashboard-page .section-label');
    if (!sectionLabel) return;

    const chartStatusData = @json($chartStatus);
    const chartCategoryData = @json($chartCategory);
    const chartMissionsData = @json($chartMissions);
    const chartSinistresData = @json($chartSinistres);
    const chartRepairsData = @json($chartRepairs);

    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#64748b';
    Chart.defaults.borderColor = 'rgba(0,0,0,0.06)';

    if (document.getElementById('chartStatus') && chartStatusData.values && chartStatusData.values.some(v => v > 0)) {
        new Chart(document.getElementById('chartStatus'), {
            type: 'doughnut',
            data: {
                labels: chartStatusData.names,
                datasets: [{
                    data: chartStatusData.values,
                    backgroundColor: chartStatusData.colors,
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                },
                cutout: '62%',
            },
        });
    }

    if (document.getElementById('chartCategory') && chartCategoryData.values && chartCategoryData.values.some(v => v > 0)) {
        new Chart(document.getElementById('chartCategory'), {
            type: 'doughnut',
            data: {
                labels: chartCategoryData.names,
                datasets: [{
                    data: chartCategoryData.values,
                    backgroundColor: chartCategoryData.colors,
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                },
                cutout: '62%',
            },
        });
    }

    if (document.getElementById('chartMissions')) {
        new Chart(document.getElementById('chartMissions'), {
            type: 'line',
            data: {
                labels: chartMissionsData.labels,
                datasets: [{
                    label: 'Missions',
                    data: chartMissionsData.values,
                    borderColor: 'rgba(26, 84, 144, 0.9)',
                    backgroundColor: 'rgba(26, 84, 144, 0.1)',
                    fill: true,
                    tension: 0.3,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                },
            },
        });
    }

    if (document.getElementById('chartSinistres')) {
        new Chart(document.getElementById('chartSinistres'), {
            type: 'bar',
            data: {
                labels: chartSinistresData.labels,
                datasets: [{
                    label: 'Sinistres',
                    data: chartSinistresData.values,
                    backgroundColor: 'rgba(201, 107, 107, 0.7)',
                    borderRadius: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                },
            },
        });
    }

    if (document.getElementById('chartRepairs')) {
        new Chart(document.getElementById('chartRepairs'), {
            type: 'bar',
            data: {
                labels: chartRepairsData.labels,
                datasets: [{
                    label: 'FCFA',
                    data: chartRepairsData.values,
                    backgroundColor: 'rgba(0, 184, 212, 0.6)',
                    borderRadius: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: { beginAtZero: true },
                },
            },
        });
    }
});
</script>
