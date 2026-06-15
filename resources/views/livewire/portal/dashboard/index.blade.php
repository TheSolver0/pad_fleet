@push('styles')
<style>
/* ═══════════════════════════════════════════
   DASHBOARD — Modern / Futuristic / Dense
═══════════════════════════════════════════ */
.db-page { padding: 0; }

/* ── Welcome bar ── */
.db-welcome {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 0 1.1rem; gap: 1rem;
}
.db-welcome-title {
    font-size: 1.3rem; font-weight: 800; letter-spacing: -0.03em;
    color: var(--text-primary); line-height: 1.2;
}
.db-welcome-title span { color: var(--pad-blue); }
.db-welcome-sub { font-size: 0.78rem; color: var(--text-muted); margin-top: 0.15rem; }
.db-date-pill {
    display: flex; align-items: center; gap: 0.45rem;
    padding: 0.4rem 0.9rem; border-radius: 100px;
    background: var(--card-bg); border: 1px solid var(--border);
    font-size: 0.78rem; color: var(--text-muted); white-space: nowrap;
}

/* ── Insights strip ── */
.db-insights-strip {
    display: flex; gap: 0.5rem; overflow-x: auto;
    padding-bottom: 0.85rem; scrollbar-width: none;
}
.db-insights-strip::-webkit-scrollbar { display: none; }
.db-pill {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.35rem 0.8rem; border-radius: 100px; font-size: 0.77rem;
    font-weight: 500; white-space: nowrap; border: 1px solid transparent; flex-shrink: 0;
}
.db-pill.success { background: rgba(122,144,0,.1);   border-color: rgba(122,144,0,.25);   color: #556600; }
.db-pill.info    { background: rgba(26,84,144,.08);  border-color: rgba(26,84,144,.2);    color: var(--pad-blue); }
.db-pill.warning { background: rgba(245,158,11,.1);  border-color: rgba(245,158,11,.25);  color: #b45309; }
.db-pill.danger  { background: rgba(201,107,107,.1); border-color: rgba(201,107,107,.25); color: #a03030; }

/* ── Section label ── */
.db-sec {
    font-size: 0.68rem; font-weight: 700; color: var(--pad-blue);
    text-transform: uppercase; letter-spacing: 0.08em;
    margin: 0.9rem 0 0.55rem;
    display: flex; align-items: center; gap: 0.5rem;
}
.db-sec::after { content: ''; flex: 1; height: 1px; background: var(--border); }

/* ── KPI grid ── */
.db-kpi-grid { display: grid; gap: 0.6rem; }
.db-kpi-grid-4 { grid-template-columns: repeat(4, 1fr); }
.db-kpi-grid-5 { grid-template-columns: repeat(5, 1fr); }

.db-kpi {
    background: var(--card-bg);
    border-radius: 11px; border: 1px solid var(--border);
    padding: 0.85rem 1rem;
    position: relative; overflow: hidden;
    transition: transform .16s, box-shadow .16s;
}
.db-kpi:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,.07); }
.db-kpi::before {
    content: ''; position: absolute;
    top: 0; left: 0; right: 0; height: 2.5px;
}
.db-kpi.c-blue::before   { background: var(--pad-blue); }
.db-kpi.c-green::before  { background: #7a9000; }
.db-kpi.c-cyan::before   { background: var(--pad-cyan); }
.db-kpi.c-red::before    { background: #c96b6b; }
.db-kpi.c-indigo::before { background: #6366f1; }
.db-kpi.c-orange::before { background: #f59e0b; }
.db-kpi.c-slate::before  { background: #64748b; }

.db-kpi-inner { display: flex; align-items: flex-start; justify-content: space-between; gap: 0.4rem; }
.db-kpi-ico {
    width: 33px; height: 33px; border-radius: 8px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .95rem;
}
.db-kpi-ico.blue   { background: rgba(26,84,144,.1);    color: var(--pad-blue); }
.db-kpi-ico.green  { background: rgba(122,144,0,.15);   color: #7a9000; }
.db-kpi-ico.cyan   { background: rgba(0,184,212,.12);   color: var(--pad-cyan); }
.db-kpi-ico.red    { background: rgba(201,107,107,.12); color: #c96b6b; }
.db-kpi-ico.indigo { background: rgba(99,102,241,.1);   color: #6366f1; }
.db-kpi-ico.orange { background: rgba(245,158,11,.12);  color: #f59e0b; }
.db-kpi-ico.slate  { background: rgba(100,116,139,.1);  color: #64748b; }

.db-kpi-lbl { font-size: .7rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .04em; margin-bottom: .2rem; }
.db-kpi-val { font-size: 1.6rem; font-weight: 800; letter-spacing: -.04em; line-height: 1; }
.db-kpi-val.blue   { color: var(--pad-blue); }
.db-kpi-val.green  { color: #7a9000; }
.db-kpi-val.cyan   { color: #009ab0; }
.db-kpi-val.red    { color: #c96b6b; }
.db-kpi-val.indigo { color: #6366f1; }
.db-kpi-val.orange { color: #d97706; }
.db-kpi-val.slate  { color: #64748b; }
.db-kpi-val.sm     { font-size: 1.15rem; }
.db-kpi-sub { font-size: .7rem; color: var(--text-muted); margin-top: .22rem; }

/* ── Cards ── */
.db-card {
    background: var(--card-bg);
    border-radius: 11px; border: 1px solid var(--border);
    overflow: hidden;
}
.db-card-head {
    padding: .6rem .9rem; border-bottom: 1px solid var(--border);
    font-size: .8rem; font-weight: 600; color: var(--text-primary);
    display: flex; align-items: center; gap: .45rem;
}
.db-card-head .spacer { margin-left: auto; }
.db-card-body { padding: .7rem .9rem; }

/* ── Grids ── */
.db-g2 { display: grid; grid-template-columns: 1fr 1fr; gap: .6rem; }
.db-g3 { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: .6rem; }
.db-g4 { display: grid; grid-template-columns: repeat(4,1fr); gap: .6rem; }

/* ── Activity feed ── */
.db-feed-item {
    display: flex; align-items: center; gap: .65rem;
    padding: .48rem 0; border-bottom: 1px solid #f1f5f9;
    font-size: .8rem;
}
.db-feed-item:last-child { border-bottom: 0; }
.db-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.db-dot.created { background: #7a9000; }
.db-dot.updated { background: var(--pad-cyan); }
.db-dot.deleted { background: #c96b6b; }
.db-dot.info    { background: var(--pad-blue); }
.db-feed-time { font-size: .7rem; color: var(--text-muted); margin-left: auto; white-space: nowrap; }

/* ── Tables ── */
.db-tbl { width: 100%; border-collapse: collapse; }
.db-tbl th {
    padding: .35rem .55rem; font-size: .68rem; font-weight: 700;
    color: var(--text-muted); text-transform: uppercase; letter-spacing: .04em;
    border-bottom: 1px solid var(--border); white-space: nowrap;
}
.db-tbl td { padding: .45rem .55rem; border-bottom: 1px solid #f1f5f9; font-size: .8rem; }
.db-tbl tr:last-child td { border-bottom: 0; }
.db-tbl tbody tr:hover td { background: #f8fafc; }

/* ── Badges ── */
.db-badge {
    display: inline-flex; align-items: center;
    padding: .18rem .5rem; border-radius: 100px;
    font-size: .7rem; font-weight: 600;
}
.db-badge.blue   { background: rgba(26,84,144,.1);    color: var(--pad-blue); }
.db-badge.green  { background: rgba(122,144,0,.12);   color: #556600; }
.db-badge.orange { background: rgba(245,158,11,.12);  color: #b45309; }
.db-badge.red    { background: rgba(201,107,107,.12); color: #a03030; }

/* ── Mini progress bar ── */
.db-bar { height: 2.5px; background: #e8edf4; border-radius: 2px; margin-top: .3rem; }
.db-bar-fill { height: 100%; border-radius: 2px; background: var(--pad-blue); }

/* ── Period buttons ── */
.db-period-btn {
    padding: .25rem .65rem; border-radius: 7px;
    border: 1px solid var(--border); background: transparent;
    font-size: .73rem; font-weight: 500; color: var(--text-muted); cursor: pointer;
    transition: all .14s;
}
.db-period-btn.active, .db-period-btn:hover { background: var(--pad-blue); color: #fff; border-color: var(--pad-blue); }

/* ── Responsive ── */
@media (max-width: 1280px) {
    .db-g4 { grid-template-columns: repeat(2,1fr); }
    .db-g3 { grid-template-columns: repeat(3,1fr); }
}
@media (max-width: 1024px) {
    .db-kpi-grid-5 { grid-template-columns: repeat(3,1fr); }
    .db-g3 { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 768px) {
    .db-kpi-grid-4, .db-kpi-grid-5 { grid-template-columns: repeat(2,1fr); }
    .db-g2, .db-g3, .db-g4 { grid-template-columns: 1fr; }
}
</style>
@endpush

<div class="db-page">

    {{-- ══ WELCOME ══ --}}
    <div class="db-welcome">
        <div>
            <div class="db-welcome-title">Bonjour, <span>{{ auth()->user()->name }}</span></div>
            <div class="db-welcome-sub">Vue d'ensemble du parc · PAD Fleet</div>
        </div>
        <div class="db-date-pill">
            <i class="bi bi-calendar3"></i>
            {{ now()->translatedFormat('l d F Y') }}
        </div>
    </div>

    {{-- ══ INSIGHTS STRIP ══ --}}
    @if(count($insights) > 0)
    <div class="db-insights-strip">
        @foreach($insights as $ins)
        <div class="db-pill {{ $ins['type'] }}">
            <i class="{{ $ins['icon'] }}"></i>
            <strong>{{ $ins['title'] }} —</strong> {{ $ins['text'] }}
        </div>
        @endforeach
    </div>
    @endif

    {{-- ══ KPI VÉHICULES ══ --}}
    <div class="db-sec"><i class="bi bi-car-front"></i> Parc véhicules</div>
    <div class="db-kpi-grid db-kpi-grid-4">
        <div class="db-kpi c-blue">
            <div class="db-kpi-inner">
                <div>
                    <div class="db-kpi-lbl">Total parc</div>
                    <div class="db-kpi-val blue">{{ number_format($kpis['total_vehicles']) }}</div>
                    <div class="db-kpi-sub">Hors motos</div>
                </div>
                <div class="db-kpi-ico blue"><i class="bi bi-car-front"></i></div>
            </div>
        </div>
        <div class="db-kpi c-green">
            <div class="db-kpi-inner">
                <div>
                    <div class="db-kpi-lbl">Disponibles</div>
                    <div class="db-kpi-val green">{{ number_format($kpis['available']) }}</div>
                    <div class="db-kpi-sub">{{ $kpis['availability_rate'] }}% du parc</div>
                </div>
                <div class="db-kpi-ico green"><i class="bi bi-check-circle"></i></div>
            </div>
        </div>
        <div class="db-kpi c-cyan">
            <div class="db-kpi-inner">
                <div>
                    <div class="db-kpi-lbl">En réparation</div>
                    <div class="db-kpi-val cyan">{{ number_format($kpis['repair']) }}</div>
                    <div class="db-kpi-sub">{{ $kpis['repairs_ongoing'] }} non clôturée(s)</div>
                </div>
                <div class="db-kpi-ico cyan"><i class="bi bi-wrench"></i></div>
            </div>
        </div>
        <div class="db-kpi c-red">
            <div class="db-kpi-inner">
                <div>
                    <div class="db-kpi-lbl">Sinistres ouverts</div>
                    <div class="db-kpi-val red">{{ number_format($kpis['sinistres_open']) }}</div>
                    <div class="db-kpi-sub">À traiter ou en cours</div>
                </div>
                <div class="db-kpi-ico red"><i class="bi bi-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>

    {{-- ══ KPI OPÉRATIONNEL ══ --}}
    <div class="db-kpi-grid db-kpi-grid-5" style="margin-top:.6rem">
        <div class="db-kpi c-indigo">
            <div class="db-kpi-inner">
                <div>
                    <div class="db-kpi-lbl">Missions</div>
                    <div class="db-kpi-val indigo">{{ number_format($kpis['missions_this_month']) }}</div>
                    <div class="db-kpi-sub">Total général</div>
                </div>
                <div class="db-kpi-ico indigo"><i class="bi bi-calendar3-week"></i></div>
            </div>
        </div>
        <div class="db-kpi c-orange">
            <div class="db-kpi-inner">
                <div>
                    <div class="db-kpi-lbl">Assurances (30j)</div>
                    <div class="db-kpi-val orange">{{ number_format($kpis['insurances_expiring_30']) }}</div>
                    <div class="db-kpi-sub">À renouveler</div>
                </div>
                <div class="db-kpi-ico orange"><i class="bi bi-shield-exclamation"></i></div>
            </div>
        </div>
        <div class="db-kpi c-slate">
            <div class="db-kpi-inner">
                <div>
                    <div class="db-kpi-lbl">Km parcourus</div>
                    <div class="db-kpi-val slate">{{ number_format($quickStats['km_this_month']) }}</div>
                    <div class="db-kpi-sub">Total missions</div>
                </div>
                <div class="db-kpi-ico slate"><i class="bi bi-speedometer2"></i></div>
            </div>
        </div>
        <div class="db-kpi c-blue">
            <div class="db-kpi-inner">
                <div>
                    <div class="db-kpi-lbl">Répa. interne</div>
                    <div class="db-kpi-val blue sm">{{ number_format($quickStats['repair_cost_internal_this_month'], 0, ',', ' ') }}</div>
                    <div class="db-kpi-sub">FCFA ce mois</div>
                </div>
                <div class="db-kpi-ico blue"><i class="bi bi-tools"></i></div>
            </div>
        </div>
        <div class="db-kpi c-red">
            <div class="db-kpi-inner">
                <div>
                    <div class="db-kpi-lbl">Répa. externe</div>
                    <div class="db-kpi-val red sm">{{ number_format($quickStats['repair_cost_external_this_month'], 0, ',', ' ') }}</div>
                    <div class="db-kpi-sub">FCFA ce mois</div>
                </div>
                <div class="db-kpi-ico red"><i class="bi bi-gear"></i></div>
            </div>
        </div>
    </div>

    {{-- ══ KPI MOTOS ══ --}}
    <div class="db-sec" style="margin-top:.9rem"><i class="bi bi-bicycle"></i> Parc motos</div>
    <div class="db-kpi-grid db-kpi-grid-4">
        <div class="db-kpi c-blue">
            <div class="db-kpi-inner">
                <div>
                    <div class="db-kpi-lbl">Total motos</div>
                    <div class="db-kpi-val blue">{{ number_format($motoKpis['total']) }}</div>
                    <div class="db-kpi-sub">Parc moto</div>
                </div>
                <div class="db-kpi-ico blue"><i class="bi bi-bicycle"></i></div>
            </div>
        </div>
        <div class="db-kpi c-green">
            <div class="db-kpi-inner">
                <div>
                    <div class="db-kpi-lbl">Disponibles</div>
                    <div class="db-kpi-val green">{{ number_format($motoKpis['available']) }}</div>
                    <div class="db-kpi-sub">{{ $motoKpis['availability_rate'] }}% du parc</div>
                </div>
                <div class="db-kpi-ico green"><i class="bi bi-check-circle"></i></div>
            </div>
        </div>
        <div class="db-kpi c-indigo">
            <div class="db-kpi-inner">
                <div>
                    <div class="db-kpi-lbl">En mission</div>
                    <div class="db-kpi-val indigo">{{ number_format($motoKpis['in_use']) }}</div>
                    <div class="db-kpi-sub">Actuellement utilisées</div>
                </div>
                <div class="db-kpi-ico indigo"><i class="bi bi-signpost-2"></i></div>
            </div>
        </div>
        <div class="db-kpi c-cyan">
            <div class="db-kpi-inner">
                <div>
                    <div class="db-kpi-lbl">En réparation</div>
                    <div class="db-kpi-val cyan">{{ number_format($motoKpis['repair']) }}</div>
                    <div class="db-kpi-sub">{{ $motoKpis['out_of_service'] }} hors service</div>
                </div>
                <div class="db-kpi-ico cyan"><i class="bi bi-wrench"></i></div>
            </div>
        </div>
    </div>

    {{-- ══ GRAPHIQUES PRINCIPAUX (4 colonnes) ══ --}}
    <div class="db-sec"><i class="bi bi-bar-chart-line"></i> Statistiques</div>
    <div class="db-g4">
        <div class="db-card">
            <div class="db-card-head"><i class="bi bi-pie-chart" style="color:var(--pad-blue)"></i>Statut véhicules</div>
            <div class="db-card-body" style="height:195px;position:relative">
                <canvas id="chartStatus"></canvas>
            </div>
        </div>
        <div class="db-card">
            <div class="db-card-head"><i class="bi bi-grid-3x3" style="color:var(--pad-cyan)"></i>Catégories</div>
            <div class="db-card-body" style="height:195px;position:relative">
                <canvas id="chartCategory"></canvas>
            </div>
        </div>
        <div class="db-card">
            <div class="db-card-head"><i class="bi bi-calendar3-range" style="color:#6366f1"></i>Missions / mois</div>
            <div class="db-card-body" style="height:195px;position:relative">
                <canvas id="chartMissions"></canvas>
            </div>
        </div>
        <div class="db-card">
            <div class="db-card-head"><i class="bi bi-exclamation-circle" style="color:#c96b6b"></i>Sinistres / mois</div>
            <div class="db-card-body" style="height:195px;position:relative">
                <canvas id="chartSinistres"></canvas>
            </div>
        </div>
    </div>

    {{-- ══ COÛT RÉPARATIONS + ACTIVITÉ ══ --}}
    <div class="db-g2" style="margin-top:.6rem">
        <div class="db-card">
            <div class="db-card-head">
                <i class="bi bi-currency-dollar" style="color:#7a9000"></i>
                Coût réparations 6 mois (FCFA)
            </div>
            <div class="db-card-body" style="height:215px;position:relative">
                <canvas id="chartRepairs"></canvas>
            </div>
        </div>
        <div class="db-card">
            <div class="db-card-head"><i class="bi bi-activity" style="color:var(--pad-cyan)"></i>Activité récente</div>
            <div style="padding:.4rem .85rem .5rem;max-height:247px;overflow-y:auto">
                @forelse($activity as $item)
                <div class="db-feed-item">
                    <span class="db-dot {{ $item['action'] }}"></span>
                    <span>{{ $item['description'] }}</span>
                    <span class="db-feed-time">{{ $item['time_ago'] }}</span>
                </div>
                @empty
                <p style="text-align:center;color:var(--text-muted);font-size:.8rem;padding:.75rem 0 0">Aucune activité récente</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══ STATISTIQUES VOYAGES ══ --}}
    <div class="db-sec"><i class="bi bi-graph-up-arrow"></i> Statistiques voyages</div>
    <div class="db-card">
        <div class="db-card-head">
            <i class="bi bi-compass" style="color:var(--pad-blue)"></i>
            Par chauffeur &amp; véhicule (top 10)
            <div class="spacer"></div>
            <div style="display:flex;gap:.3rem" id="trip-period-btns">
                <button class="db-period-btn active" data-period="month">Ce mois</button>
                <button class="db-period-btn" data-period="quarter">Trimestre</button>
                <button class="db-period-btn" data-period="year">Année</button>
            </div>
        </div>
        <div class="db-g2" style="gap:0;border-top:1px solid var(--border)">
            {{-- Chauffeurs --}}
            <div style="border-right:1px solid var(--border)">
                <div style="padding:.5rem .85rem .2rem;font-size:.68rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em">
                    <i class="bi bi-person-badge me-1"></i>Chauffeurs
                </div>
                <div style="height:170px;position:relative;padding:.4rem .85rem">
                    <canvas id="chartDrivers"></canvas>
                </div>
                <div style="overflow-x:auto;padding:0 .5rem .5rem">
                    <table class="db-tbl" id="tableDrivers">
                        <thead><tr>
                            <th>Chauffeur</th>
                            <th style="text-align:right">Missions</th>
                            <th style="text-align:right">Km total</th>
                            <th style="text-align:right">Km moy.</th>
                            <th style="text-align:right">Durée moy.</th>
                            <th style="text-align:right">Dernière</th>
                        </tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            {{-- Véhicules --}}
            <div>
                <div style="padding:.5rem .85rem .2rem;font-size:.68rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em">
                    <i class="bi bi-car-front me-1"></i>Véhicules
                </div>
                <div style="height:170px;position:relative;padding:.4rem .85rem">
                    <canvas id="chartVehicles"></canvas>
                </div>
                <div style="overflow-x:auto;padding:0 .5rem .5rem">
                    <table class="db-tbl" id="tableVehicles">
                        <thead><tr>
                            <th>Véhicule</th>
                            <th style="text-align:right">Missions</th>
                            <th style="text-align:right">Km total</th>
                            <th style="text-align:right">Km moy.</th>
                            <th style="text-align:right">Durée moy.</th>
                            <th style="text-align:right">Dernière</th>
                        </tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MAINTENANCE & STOCK ══ --}}
    <div class="db-sec"><i class="bi bi-wrench-adjustable"></i> Maintenance &amp; Stock</div>
    <div class="db-g3">
        {{-- Chart réparations par catégorie --}}
        <div class="db-card">
            <div class="db-card-head"><i class="bi bi-bar-chart-steps" style="color:var(--pad-cyan)"></i>Réparations par type ({{ now()->year }})</div>
            <div class="db-card-body" style="height:235px;position:relative">
                <canvas id="chartRepairsByCategory"></canvas>
            </div>
        </div>

        {{-- Table réparations --}}
        <div class="db-card">
            <div class="db-card-head"><i class="bi bi-table" style="color:#6366f1"></i>Détail par catégorie</div>
            <div style="padding:.4rem .7rem .5rem;max-height:272px;overflow-y:auto">
                @if(count($repairsByCategory['rows']) > 0)
                <table class="db-tbl">
                    <thead><tr>
                        <th>Type</th>
                        <th style="text-align:center">En cours</th>
                        <th style="text-align:center">Terminées</th>
                        <th style="text-align:right">Coût FCFA</th>
                    </tr></thead>
                    <tbody>
                        @foreach($repairsByCategory['rows'] as $row)
                        <tr>
                            <td style="font-weight:500">{{ $row['category'] }}</td>
                            <td style="text-align:center">
                                @if($row['ongoing'] > 0)
                                    <span class="db-badge orange">{{ $row['ongoing'] }}</span>
                                @else <span style="color:var(--text-muted)">—</span> @endif
                            </td>
                            <td style="text-align:center"><span class="db-badge green">{{ $row['completed'] }}</span></td>
                            <td style="text-align:right;font-size:.75rem">{{ number_format($row['cost'], 0, ',', ' ') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p style="text-align:center;color:var(--text-muted);font-size:.8rem;padding:.75rem 0">Aucune réparation.</p>
                @endif
            </div>
        </div>

        {{-- Top pièces --}}
        <div class="db-card">
            <div class="db-card-head"><i class="bi bi-boxes" style="color:#7a9000"></i>Top 10 pièces consommées</div>
            <div style="padding:.4rem .7rem .5rem;max-height:272px;overflow-y:auto">
                @if($partsConsumption->count() > 0)
                    @php $maxQty = $partsConsumption->max('total_qty') ?: 1; @endphp
                    <table class="db-tbl">
                        <thead><tr>
                            <th>#</th><th>Désignation</th>
                            <th style="text-align:center">Qté</th>
                            <th style="text-align:right">Coût</th>
                        </tr></thead>
                        <tbody>
                            @foreach($partsConsumption as $i => $part)
                            <tr>
                                <td style="color:var(--text-muted);font-size:.7rem">{{ $i+1 }}</td>
                                <td>
                                    <div style="font-size:.79rem;font-weight:500">{{ $part['name'] }}</div>
                                    <div class="db-bar"><div class="db-bar-fill" style="width:{{ round($part['total_qty']/$maxQty*100) }}%"></div></div>
                                </td>
                                <td style="text-align:center">
                                    <span class="db-badge blue">{{ number_format($part['total_qty']) }} {{ $part['unit'] }}</span>
                                </td>
                                <td style="text-align:right;font-size:.75rem">{{ number_format($part['total_cost'], 0, ',', ' ') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                <p style="text-align:center;color:var(--text-muted);font-size:.8rem;padding:.75rem 0">Aucune pièce.</p>
                @endif
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const DATA = {
        status:            @json($chartStatus),
        category:          @json($chartCategory),
        missions:          @json($chartMissions),
        sinistres:         @json($chartSinistres),
        repairs:           @json($chartRepairs),
        drivers:           @json($driverTripStats),
        vehicles:          @json($vehicleTripStats),
        repairsByCategory: @json($repairsByCategory),
    };

    const CHARTS = {};

    function destroyAll() {
        Object.keys(CHARTS).forEach(id => { CHARTS[id].destroy(); delete CHARTS[id]; });
    }

    function make(id, config) {
        const el = document.getElementById(id);
        if (!el) return;
        if (CHARTS[id]) { CHARTS[id].destroy(); delete CHARTS[id]; }
        CHARTS[id] = new Chart(el, config);
    }

    /* ── Trip stats via fetch ── */
    const TRIP_URL  = '{{ route("dashboard.trip-stats") }}';
    const tripCharts = {};

    document.querySelectorAll('#trip-period-btns .db-period-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#trip-period-btns .db-period-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            loadTripStats(this.dataset.period);
        });
    });

    function loadTripStats(period) {
        fetch(`${TRIP_URL}?period=${period}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            renderTripChart('chartDrivers',  data.drivers,  'rgba(26,84,144,.75)',  'rgba(0,184,212,.5)');
            renderTripChart('chartVehicles', data.vehicles, 'rgba(122,144,0,.75)',  'rgba(201,107,107,.5)');
            renderTripTable('tableDrivers',  data.drivers);
            renderTripTable('tableVehicles', data.vehicles);
        })
        .catch(err => console.error('Trip stats:', err));
    }

    function renderTripChart(id, rows, c1, c2) {
        if (tripCharts[id]) { tripCharts[id].destroy(); delete tripCharts[id]; }
        const el = document.getElementById(id);
        if (!el || !rows.length) return;
        tripCharts[id] = new Chart(el, {
            type: 'bar',
            data: {
                labels: rows.map(r => r.name),
                datasets: [
                    { label: 'Missions', data: rows.map(r => r.missions_count), backgroundColor: c1, borderRadius: 4, yAxisID: 'y' },
                    { label: 'Km',       data: rows.map(r => r.total_km),       backgroundColor: c2, borderRadius: 4, yAxisID: 'y1' },
                ],
            },
            options: {
                responsive: true, maintainAspectRatio: false, animation: { duration: 280 },
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 9, font: { size: 10 } } } },
                scales: {
                    y:  { beginAtZero: true, position: 'left',  ticks: { font: { size: 10 } } },
                    y1: { beginAtZero: true, position: 'right', ticks: { font: { size: 10 } }, grid: { drawOnChartArea: false } },
                },
            },
        });
    }

    function renderTripTable(tableId, rows) {
        const tbody = document.querySelector(`#${tableId} tbody`);
        if (!tbody) return;
        if (!rows.length) {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:.6rem;font-size:.78rem">Aucune mission sur cette période</td></tr>`;
            return;
        }
        const maxM = Math.max(...rows.map(r => r.missions_count), 1);
        const fmt  = n => new Intl.NumberFormat('fr-FR').format(n);
        tbody.innerHTML = rows.map(row => {
            const pct = Math.round(row.missions_count / maxM * 100);
            return `<tr>
                <td><div style="font-weight:500;font-size:.79rem">${row.name}</div><div style="height:2.5px;background:#e8edf4;border-radius:2px;margin-top:3px"><div style="height:100%;width:${pct}%;background:var(--pad-blue);border-radius:2px"></div></div></td>
                <td style="text-align:right"><span style="background:rgba(26,84,144,.1);color:var(--pad-blue);padding:.15rem .45rem;border-radius:100px;font-size:.7rem;font-weight:600">${fmt(row.missions_count)}</span></td>
                <td style="text-align:right;font-size:.79rem">${fmt(row.total_km)}</td>
                <td style="text-align:right;font-size:.79rem">${fmt(row.avg_km)}</td>
                <td style="text-align:right;font-size:.79rem">${row.dur_label || '—'}</td>
                <td style="text-align:right;font-size:.75rem;color:var(--text-muted)">${row.last_mission}</td>
            </tr>`;
        }).join('');
    }

    loadTripStats('month');

    /* ── Main charts ── */
    function initAll() {
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.color       = '#64748b';
        Chart.defaults.borderColor = 'rgba(0,0,0,0.05)';

        if (DATA.status.values?.some(v => v > 0)) {
            make('chartStatus', {
                type: 'doughnut',
                data: { labels: DATA.status.names, datasets: [{ data: DATA.status.values, backgroundColor: DATA.status.colors, borderWidth: 0 }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 9, font: { size: 10 } } } } },
            });
        }

        if (DATA.category.values?.some(v => v > 0)) {
            make('chartCategory', {
                type: 'doughnut',
                data: { labels: DATA.category.names, datasets: [{ data: DATA.category.values, backgroundColor: DATA.category.colors, borderWidth: 0 }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 9, font: { size: 10 } } } } },
            });
        }

        make('chartMissions', {
            type: 'line',
            data: { labels: DATA.missions.labels, datasets: [{ label: 'Missions', data: DATA.missions.values, borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,.1)', fill: true, tension: 0.4, pointRadius: 3 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } }, x: { ticks: { font: { size: 10 } } } } },
        });

        make('chartSinistres', {
            type: 'bar',
            data: { labels: DATA.sinistres.labels, datasets: [{ label: 'Sinistres', data: DATA.sinistres.values, backgroundColor: 'rgba(201,107,107,.7)', borderRadius: 5 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } }, x: { ticks: { font: { size: 10 } } } } },
        });

        make('chartRepairs', {
            type: 'bar',
            data: {
                labels: DATA.repairs.labels,
                datasets: [
                    { label: 'Interne', data: DATA.repairs.internal, backgroundColor: 'rgba(0,184,212,.65)', borderRadius: 5 },
                    { label: 'Externe', data: DATA.repairs.external, backgroundColor: 'rgba(201,107,107,.65)', borderRadius: 5 },
                ],
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 9, font: { size: 10 } } }, tooltip: { callbacks: { label: ctx => ctx.dataset.label + ' : ' + new Intl.NumberFormat('fr-FR').format(ctx.parsed.y) + ' FCFA' } } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v), font: { size: 10 } } }, x: { ticks: { font: { size: 10 } } } },
            },
        });

        if (DATA.repairsByCategory.labels?.length) {
            make('chartRepairsByCategory', {
                type: 'bar',
                data: {
                    labels: DATA.repairsByCategory.labels,
                    datasets: [
                        { label: 'En cours',  data: DATA.repairsByCategory.ongoing,   backgroundColor: 'rgba(0,184,212,.75)',  borderRadius: 5 },
                        { label: 'Terminées', data: DATA.repairsByCategory.completed, backgroundColor: 'rgba(122,144,0,.7)',   borderRadius: 5 },
                    ],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 9, font: { size: 10 } } } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } }, x: { ticks: { font: { size: 10 } } } } },
            });
        }
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initAll);
    else initAll();

    document.addEventListener('livewire:navigated', initAll);
    document.addEventListener('livewire:update',    destroyAll);
    document.addEventListener('livewire:updated',   initAll);
})();
</script>
