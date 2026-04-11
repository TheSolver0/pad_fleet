@push('styles')
    <style>
        .dashboard-page .kpi-grid-sm {
            grid-template-columns: repeat(4, 1fr);
        }

        @media (max-width: 992px) {
            .dashboard-page .kpi-grid-sm {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .dashboard-page .kpi-grid-sm {
                grid-template-columns: 1fr;
            }
        }

        .kpi-card-sm .kpi-card-value {
            font-size: 1.35rem;
        }

        .kpi-card-sm .kpi-card-icon.missions {
            background: rgba(26, 84, 144, 0.12);
            color: var(--kpi-vehicules);
        }

        .kpi-card-sm .kpi-card-icon.assurances {
            background: rgba(0, 184, 212, 0.15);
            color: var(--kpi-repa);
        }

        .kpi-card-sm .kpi-card-icon.km {
            background: rgba(122, 144, 0, 0.2);
            color: #7a9000;
        }

        .kpi-card-sm .kpi-card-icon.cost {
            background: rgba(100, 120, 160, 0.15);
            color: #4a6fa5;
        }

        .dashboard-section-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .insights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 0.75rem;
        }

        .insight-card {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--card-bg);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .insight-card .insight-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .insight-card-success .insight-icon {
            background: rgba(122, 144, 0, 0.2);
            color: #7a9000;
        }

        .insight-card-info .insight-icon {
            background: rgba(26, 84, 144, 0.12);
            color: var(--kpi-vehicules);
        }

        .insight-card-warning .insight-icon {
            background: rgba(0, 184, 212, 0.15);
            color: var(--kpi-repa);
        }

        .insight-card-danger .insight-icon {
            background: rgba(201, 107, 107, 0.15);
            color: var(--kpi-sinistres);
        }

        .insight-title {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .insight-text {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }

        .charts-grid-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        @media (max-width: 992px) {

            .charts-grid,
            .charts-grid-3 {
                grid-template-columns: 1fr;
            }
        }

        .chart-card {
            background: var(--card-bg);
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .chart-card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--text-primary);
        }

        .chart-wrap {
            padding: 1rem 1.25rem;
            position: relative;
        }

        .chart-wrap-pie {
            min-height: 240px;
        }

        .chart-wrap canvas {
            max-width: 100%;
        }
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
                <span class="kpi-card-label">Missions </span>
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
                <span class="kpi-card-label">Km</span>
                <span class="kpi-card-icon km"><i class="bi bi-speedometer2"></i></span>
            </div>
            <div class="kpi-card-value">{{ number_format($quickStats['km_this_month']) }}</div>
        </div>
        <div class="kpi-card kpi-card-sm">
            <div class="kpi-card-header">
                <span class="kpi-card-label">Réparations (interne)</span>
                <span class="kpi-card-icon cost"><i class="bi bi-wrench"></i></span>
            </div>
            <div class="kpi-card-value">{{ number_format($quickStats['repair_cost_internal_this_month'], 0, ',', ' ') }}
            </div>
            <div class="kpi-card-sub">FCFA ce mois</div>
        </div>
        <div class="kpi-card kpi-card-sm">
            <div class="kpi-card-header">
                <span class="kpi-card-label">Réparations (externe)</span>
                <span class="kpi-card-icon cost"><i class="bi bi-gear"></i></span>
            </div>
            <div class="kpi-card-value">
                {{ number_format($quickStats['repair_cost_external_this_month'], 0, ',', ' ') }}</div>
            <div class="kpi-card-sub">FCFA ce mois</div>
        </div>
    </div>

    {{-- Insights --}}
    {{-- @if (count($insights) > 0)
        <div class="dashboard-section mt-4">
            <h3 class="dashboard-section-title"><i class="bi bi-lightbulb me-2"></i>Points d’attention</h3>
            <div class="insights-grid">
                @foreach ($insights as $insight)
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

    {{-- Statistiques voyages --}}
    {{-- Statistiques voyages — full JS, sans Livewire --}}
<div class="dashboard-section mt-4" id="trip-section">
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <h3 class="dashboard-section-title mb-0">
            <i class="bi bi-graph-up-arrow me-2"></i>Statistiques voyages
        </h3>
        <div class="btn-group btn-group-sm" role="group" id="trip-period-btns">
            <button type="button" class="btn btn-primary"        data-period="month">Ce mois</button>
            <button type="button" class="btn btn-outline-secondary" data-period="quarter">Trimestre</button>
            <button type="button" class="btn btn-outline-secondary" data-period="year">Année</button>
        </div>
    </div>

    <div class="charts-grid">
        {{-- Chauffeurs --}}
        <div class="chart-card">
            <div class="chart-card-header"><i class="bi bi-person-badge me-2"></i>Par chauffeur (top 10)</div>
            <div class="chart-wrap" style="height:220px; position:relative">
                <canvas id="chartDrivers"></canvas>
            </div>
            <div class="chart-wrap pt-0">
                <table class="table table-sm table-hover mb-0" id="tableDrivers">
                    <thead>
                        <tr>
                            <th>Chauffeur</th>
                            <th class="text-end">Missions</th>
                            <th class="text-end">Km total</th>
                            <th class="text-end">Km moy.</th>
                            <th class="text-end">Durée moy.</th>
                            <th class="text-end">Dernière mission</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        {{-- Véhicules --}}
        <div class="chart-card">
            <div class="chart-card-header"><i class="bi bi-car-front me-2"></i>Par véhicule (top 10)</div>
            <div class="chart-wrap" style="height:220px; position:relative">
                <canvas id="chartVehicles"></canvas>
            </div>
            <div class="chart-wrap pt-0">
                <table class="table table-sm table-hover mb-0" id="tableVehicles">
                    <thead>
                        <tr>
                            <th>Véhicule</th>
                            <th class="text-end">Missions</th>
                            <th class="text-end">Km total</th>
                            <th class="text-end">Km moy.</th>
                            <th class="text-end">Durée moy.</th>
                            <th class="text-end">Dernière mission</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
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
                            $dot = match ($item['action']) {
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
(function () {
    // ── Données injectées par Blade (recalculées à chaque re-render Livewire) ──
    const DATA = {
        status:    @json($chartStatus),
        category:  @json($chartCategory),
        missions:  @json($chartMissions),
        sinistres: @json($chartSinistres),
        repairs:   @json($chartRepairs),
        drivers:   @json($driverTripStats),
        vehicles:  @json($vehicleTripStats),
    };

    // ── Registre des instances Chart.js ──
    const CHARTS = {};
    // ── Statistiques voyages — full JS fetch ──────────────────────────────
(function () {
    const TRIP_URL  = '{{ route("dashboard.trip-stats") }}';
    const tripCharts = {};
    let activePeriod = 'month';

    // Boutons filtre
    document.querySelectorAll('#trip-period-btns button').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#trip-period-btns button').forEach(b => {
                b.classList.replace('btn-primary', 'btn-outline-secondary');
            });
            this.classList.replace('btn-outline-secondary', 'btn-primary');
            activePeriod = this.dataset.period;
            loadTripStats(activePeriod);
        });
    });

    function loadTripStats(period) {
        fetch(`${TRIP_URL}?period=${period}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            renderTripChart('chartDrivers',  data.drivers,  'rgba(26,84,144,0.75)',  'rgba(0,184,212,0.5)');
            renderTripChart('chartVehicles', data.vehicles, 'rgba(122,144,0,0.75)',  'rgba(201,107,107,0.5)');
            renderTripTable('tableDrivers',  data.drivers);
            renderTripTable('tableVehicles', data.vehicles);
        })
        .catch(err => console.error('Trip stats error:', err));
    }

    function renderTripChart(canvasId, rows, colorMissions, colorKm) {
        if (tripCharts[canvasId]) {
            tripCharts[canvasId].destroy();
            delete tripCharts[canvasId];
        }
        const canvas = document.getElementById(canvasId);
        if (!canvas || !rows.length) return;

        // Reset dimensions pour éviter l'étirement
        canvas.style.height = '';
        canvas.removeAttribute('height');

        tripCharts[canvasId] = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: rows.map(r => r.name),
                datasets: [
                    {
                        label: 'Missions',
                        data: rows.map(r => r.missions_count),
                        backgroundColor: colorMissions,
                        borderRadius: 5,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Km total',
                        data: rows.map(r => r.total_km),
                        backgroundColor: colorKm,
                        borderRadius: 5,
                        yAxisID: 'y1',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 400 },
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    y:  { beginAtZero: true, position: 'left',  title: { display: true, text: 'Missions' } },
                    y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'Km' }, grid: { drawOnChartArea: false } },
                },
            },
        });
    }

    function renderTripTable(tableId, rows) {
        const tbody = document.querySelector(`#${tableId} tbody`);
        if (!tbody) return;

        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-muted text-center py-3">Aucune mission sur cette période</td></tr>';
            return;
        }

        const maxMissions = Math.max(...rows.map(r => r.missions_count), 1);
        const fmt = n => new Intl.NumberFormat('fr-FR').format(n);

        tbody.innerHTML = rows.map(row => {
            const pct = Math.round(row.missions_count / maxMissions * 100);
            return `
                <tr>
                    <td>
                        <div class="fw-semibold" style="font-size:.85rem">${row.name}</div>
                        <div class="progress mt-1" style="height:3px">
                            <div class="progress-bar bg-primary" style="width:${pct}%"></div>
                        </div>
                    </td>
                    <td class="text-end">
                        <span class="badge bg-primary bg-opacity-10 text-primary">${fmt(row.missions_count)}</span>
                    </td>
                    <td class="text-end" style="font-size:.85rem">${fmt(row.total_km)}</td>
                    <td class="text-end" style="font-size:.85rem">${fmt(row.avg_km)}</td>
                    <td class="text-end" style="font-size:.85rem">${row.dur_label}</td>
                    <td class="text-end" style="font-size:.8rem;color:var(--text-muted)">${row.last_mission}</td>
                </tr>
            `;
        }).join('');
    }

    // Chargement initial
    loadTripStats(activePeriod);
})();

    function destroyAll() {
        Object.keys(CHARTS).forEach(id => {
            CHARTS[id].destroy();
            delete CHARTS[id];
        });
    }

    function make(id, config) {
        const el = document.getElementById(id);
        if (!el) return;
        if (CHARTS[id]) { CHARTS[id].destroy(); delete CHARTS[id]; }
        CHARTS[id] = new Chart(el, config);
    }

    function initAll() {
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.color       = '#64748b';
        Chart.defaults.borderColor = 'rgba(0,0,0,0.06)';

        // ── Répartition par statut (doughnut) ──
        if (DATA.status.values?.some(v => v > 0)) {
            make('chartStatus', {
                type: 'doughnut',
                data: {
                    labels:   DATA.status.names,
                    datasets: [{ data: DATA.status.values, backgroundColor: DATA.status.colors, borderWidth: 0 }],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    cutout: '62%',
                },
            });
        }

        // ── Répartition par catégorie (doughnut) ──
        if (DATA.category.values?.some(v => v > 0)) {
            make('chartCategory', {
                type: 'doughnut',
                data: {
                    labels:   DATA.category.names,
                    datasets: [{ data: DATA.category.values, backgroundColor: DATA.category.colors, borderWidth: 0 }],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    cutout: '62%',
                },
            });
        }

        // ── Missions par mois (line) ──
        make('chartMissions', {
            type: 'line',
            data: {
                labels:   DATA.missions.labels,
                datasets: [{
                    label: 'Missions', data: DATA.missions.values,
                    borderColor: 'rgba(26, 84, 144, 0.9)', backgroundColor: 'rgba(26, 84, 144, 0.1)',
                    fill: true, tension: 0.3,
                }],
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
            },
        });

        // ── Sinistres par mois (bar) ──
        make('chartSinistres', {
            type: 'bar',
            data: {
                labels:   DATA.sinistres.labels,
                datasets: [{
                    label: 'Sinistres', data: DATA.sinistres.values,
                    backgroundColor: 'rgba(201, 107, 107, 0.7)', borderRadius: 6,
                }],
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
            },
        });

        // ── Coût réparations interne / externe (bar groupé) ──
        make('chartRepairs', {
            type: 'bar',
            data: {
                labels:   DATA.repairs.labels,
                datasets: [
                    { label: 'Interne', data: DATA.repairs.internal, backgroundColor: 'rgba(0, 184, 212, 0.65)', borderRadius: 6 },
                    { label: 'Externe', data: DATA.repairs.external, backgroundColor: 'rgba(201, 107, 107, 0.65)', borderRadius: 6 },
                ],
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.dataset.label + ' : ' + new Intl.NumberFormat('fr-FR').format(ctx.parsed.y) + ' FCFA',
                        },
                    },
                },
                scales: {
                    x: { stacked: false },
                    y: {
                        beginAtZero: true,
                        ticks: { callback: val => new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(val) },
                    },
                },
            },
        });

        // ── Voyages par chauffeur (bar double axe) ──
        if (DATA.drivers.length) {
            make('chartDrivers', {
                type: 'bar',
                data: {
                    labels: DATA.drivers.map(r => r.name),
                    datasets: [
                        { label: 'Missions', data: DATA.drivers.map(r => r.missions_count), backgroundColor: 'rgba(26, 84, 144, 0.75)', borderRadius: 5, yAxisID: 'y' },
                        { label: 'Km total', data: DATA.drivers.map(r => r.total_km),       backgroundColor: 'rgba(0, 184, 212, 0.5)',   borderRadius: 5, yAxisID: 'y1' },
                    ],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y:  { beginAtZero: true, position: 'left',  title: { display: true, text: 'Missions' } },
                        y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'Km' }, grid: { drawOnChartArea: false } },
                    },
                },
            });
        }

        // ── Voyages par véhicule (bar double axe) ──
        if (DATA.vehicles.length) {
            make('chartVehicles', {
                type: 'bar',
                data: {
                    labels: DATA.vehicles.map(r => r.name),
                    datasets: [
                        { label: 'Missions', data: DATA.vehicles.map(r => r.missions_count), backgroundColor: 'rgba(122, 144, 0, 0.75)',   borderRadius: 5, yAxisID: 'y' },
                        { label: 'Km total', data: DATA.vehicles.map(r => r.total_km),       backgroundColor: 'rgba(201, 107, 107, 0.5)', borderRadius: 5, yAxisID: 'y1' },
                    ],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y:  { beginAtZero: true, position: 'left',  title: { display: true, text: 'Missions' } },
                        y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'Km' }, grid: { drawOnChartArea: false } },
                    },
                },
            });
        }
    }

    // ── Lancement initial ──
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // ── Après chaque re-render Livewire : destruction totale + re-init ──
    document.addEventListener('livewire:navigated', initAll);
    document.addEventListener('livewire:update',    destroyAll);
    document.addEventListener('livewire:updated',   initAll);

})();
</script>