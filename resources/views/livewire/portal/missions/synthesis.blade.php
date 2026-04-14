@push('styles')
<style>
.synth-section {
    background: var(--card-bg);
    border-radius: 14px;
    border: 1px solid var(--border);
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
    padding: 1.5rem;
    margin-bottom: 1.25rem;
}
.synth-section-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1rem;
    padding-bottom: .5rem;
    border-bottom: 2px solid var(--border);
    display: flex;
    align-items: center;
    gap: .5rem;
}
.synth-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.25rem;
}
@media (max-width: 992px) {
    .synth-kpi-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 576px) {
    .synth-kpi-grid { grid-template-columns: 1fr 1fr; }
}
.synth-kpi {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    text-align: center;
}
.synth-kpi-value {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1.1;
}
.synth-kpi-label {
    font-size: .8rem;
    color: var(--text-muted);
    margin-top: .25rem;
}
.synth-kpi-accent { color: rgba(26,84,144,.9); }
.synth-kpi-accent-green { color: #7a9000; }
.synth-kpi-accent-teal  { color: rgba(0,184,212,.9); }
.synth-kpi-accent-red   { color: rgba(201,107,107,.9); }

.bar-row {
    display: flex;
    align-items: center;
    gap: .75rem;
    margin-bottom: .6rem;
    font-size: .875rem;
}
.bar-label { width: 220px; flex-shrink: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.bar-track { flex: 1; background: var(--border); border-radius: 4px; height: 10px; }
.bar-fill  { height: 10px; border-radius: 4px; background: rgba(26,84,144,.7); transition: width .4s; }
.bar-count { width: 42px; text-align: right; font-weight: 600; color: var(--text-primary); flex-shrink: 0; }

.region-badge {
    display: inline-block;
    padding: .25rem .65rem;
    border-radius: 20px;
    font-size: .8rem;
    font-weight: 600;
    background: rgba(26,84,144,.1);
    color: rgba(26,84,144,.9);
    margin: .2rem;
}

.mission-table thead th {
    font-size: .8rem;
    font-weight: 700;
    background: var(--border);
    white-space: nowrap;
}
.mission-table tbody td { font-size: .85rem; }

@media print {
    /* ── Page setup ──────────────────────────────────────────────── */
    @page { size: A4 portrait; margin: 1.5cm 1.2cm; }

    /* ── Hide chrome ─────────────────────────────────────────────── */
    .no-print,
    aside, #sidebar, .sidebar,
    #sidebar-overlay, .sidebar-overlay,
    .topbar, .app-footer,
    #toast-container,
    .module-toolbar-actions,
    .module-toolbar-filters { display: none !important; }

    /* ── Full-width layout ───────────────────────────────────────── */
    html, body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
        font-size: 11pt !important;
        color: #111 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        min-height: auto !important;
        box-shadow: none !important;
    }
    .content-inner { padding: 0 !important; }

    /* ── KPI grid — force 4 columns ──────────────────────────────── */
    .synth-kpi-grid {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: .5cm !important;
        margin-bottom: .6cm !important;
    }
    .synth-kpi {
        border: 1px solid #ccc !important;
        border-radius: 6px !important;
        padding: .35cm .4cm !important;
        background: #fff !important;
        page-break-inside: avoid;
    }
    .synth-kpi-value { font-size: 18pt !important; font-weight: 800 !important; }
    .synth-kpi-label { font-size: 8pt !important; color: #555 !important; }

    /* ── Sections ────────────────────────────────────────────────── */
    .synth-section {
        border: 1px solid #bbb !important;
        border-radius: 6px !important;
        box-shadow: none !important;
        padding: .4cm .5cm !important;
        margin-bottom: .5cm !important;
        background: #fff !important;
        page-break-inside: avoid;
    }
    .synth-section-title {
        font-size: 11pt !important;
        font-weight: 700 !important;
        color: #1a5490 !important;
        border-bottom: 1.5pt solid #1a5490 !important;
        margin-bottom: .3cm !important;
        padding-bottom: .15cm !important;
    }

    /* ── Print-only org header ───────────────────────────────────── */
    .print-org-header { display: block !important; }

    /* ── Header block ────────────────────────────────────────────── */
    .synth-section h4 {
        font-size: 13pt !important;
        font-weight: 800 !important;
    }

    /* ── Bar charts ──────────────────────────────────────────────── */
    .bar-row { font-size: 9pt !important; margin-bottom: .2cm !important; }
    .bar-label { font-size: 9pt !important; }
    .bar-track {
        background: #e0e0e0 !important;
        height: 8px !important;
        border-radius: 3px !important;
    }
    .bar-fill { height: 8px !important; border-radius: 3px !important; }
    .bar-count { font-size: 9pt !important; }

    /* ── Region badges ───────────────────────────────────────────── */
    .region-badge {
        border: 1px solid #1a5490 !important;
        background: #e8f0fb !important;
        color: #1a5490 !important;
        font-size: 8pt !important;
    }
    .col-md-6, .col-lg-4 { width: 33.33% !important; float: left !important; }

    /* ── Main table ──────────────────────────────────────────────── */
    .mission-table { width: 100% !important; border-collapse: collapse !important; font-size: 8pt !important; }
    .mission-table thead th {
        background: #dde4ed !important;
        border: 1px solid #bbb !important;
        padding: 4px 6px !important;
        font-size: 8pt !important;
        white-space: nowrap !important;
    }
    .mission-table tbody td {
        border: 1px solid #ccc !important;
        padding: 3px 6px !important;
        font-size: 8pt !important;
        vertical-align: top !important;
    }
    .mission-table tfoot td {
        border-top: 2px solid #bbb !important;
        font-weight: 700 !important;
        font-size: 8.5pt !important;
        padding: 4px 6px !important;
    }

    /* ── Badges ──────────────────────────────────────────────────── */
    .badge {
        border: 1px solid #888 !important;
        background: #f0f0f0 !important;
        color: #111 !important;
        font-size: 7.5pt !important;
        padding: 1px 4px !important;
        border-radius: 3px !important;
    }
    .bg-success  { background: #d4edda !important; border-color: #28a745 !important; color: #155724 !important; }
    .bg-info     { background: #d1ecf1 !important; border-color: #17a2b8 !important; color: #0c5460 !important; }
    .bg-warning  { background: #fff3cd !important; border-color: #ffc107 !important; color: #856404 !important; }
    .bg-danger   { background: #f8d7da !important; border-color: #dc3545 !important; color: #721c24 !important; }
    .bg-secondary{ background: #e2e3e5 !important; border-color: #6c757d !important; color: #383d41 !important; }

    /* ── Page breaks ─────────────────────────────────────────────── */
    h4, h5 { page-break-after: avoid !important; }
    .synth-section:last-child { page-break-after: avoid; }
    .synth-section { page-break-inside: avoid; }
    /* Allow the large table section to break across pages */
    .synth-section:last-of-type { page-break-inside: auto; }
    .mission-table tbody tr { page-break-inside: avoid; }

    /* ── Section label ───────────────────────────────────────────── */
    .section-label { font-size: 8pt !important; margin-bottom: .3cm !important; }

    /* ── Links — show plain text ─────────────────────────────────── */
    a { color: inherit !important; text-decoration: none !important; }
}
</style>
@endpush

<div>
    <p class="section-label">Analyse et statistiques — Déplacements des véhicules</p>

    {{-- Filtres période --}}
    <div class="activity-card mb-4 no-print">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Période d'analyse</span>
                <div class="module-toolbar-filters">
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0">Du</label>
                        <input type="date" class="form-control form-control-sm" style="width:150px" wire:model.live="period_start">
                        <label class="small text-muted mb-0">Au</label>
                        <input type="date" class="form-control form-control-sm" style="width:150px" wire:model.live="period_end">
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" wire:click="setPeriod('month')">Ce mois</button>
                        <button type="button" class="btn btn-outline-secondary" wire:click="setPeriod('quarter')">Trimestre</button>
                        <button type="button" class="btn btn-outline-secondary" wire:click="setPeriod('year')">Année</button>
                    </div>
                </div>
                <div class="module-toolbar-actions">
                    <a href="{{ route('missions.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Retour
                    </a>
                    <button type="button" class="btn btn-sm btn-primary" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>Imprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if($data['total'] === 0)
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Aucune mission trouvée sur la période
            @if($period_start) du <strong>{{ \Carbon\Carbon::parse($period_start)->translatedFormat('d F Y') }}</strong> @endif
            @if($period_end) au <strong>{{ \Carbon\Carbon::parse($period_end)->translatedFormat('d F Y') }}</strong> @endif.
        </div>
    @else
    @php
        $total        = $data['total'];
        $totalJours   = $data['total_jours'];
        $avgDuration  = $data['avg_duration'];
        $topDest      = $data['top_destination'];
        $byDemandeur  = $data['by_demandeur'];
        $byDest       = $data['by_destination'];
        $byRaison     = $data['by_raison'];
        $byRegion     = $data['by_region'];
        $missions     = $data['missions'];
        $maxDemandeur = $byDemandeur->max() ?: 1;
        $maxDest      = $byDest->max() ?: 1;
    @endphp

    {{-- En-tête synthèse --}}
    <div class="synth-section">
        {{-- Only shown when printing --}}
        <div class="print-org-header" style="display:none;text-align:center;margin-bottom:.5cm;padding-bottom:.3cm;border-bottom:2px solid #1a5490;">
            <div style="font-size:14pt;font-weight:800;color:#1a5490;letter-spacing:.02em;">{{ config('app.name') }}</div>
            <div style="font-size:9pt;color:#555;margin-top:2px;">Direction des Services Généraux — Gestion de la Flotte</div>
        </div>
        <div style="text-align:center; margin-bottom:1rem;">
            <h4 style="font-weight:800;font-size:1.2rem;color:var(--text-primary)">
                SYNTHÈSE — ÉTAT DES DÉPLACEMENTS ET DEMANDES DE VÉHICULES
            </h4>
            <p class="text-muted mb-0" style="font-size:.9rem">
                Période d'analyse :
                @if($period_start && $period_end)
                    Du <strong>{{ \Carbon\Carbon::parse($period_start)->translatedFormat('d F Y') }}</strong>
                    au <strong>{{ \Carbon\Carbon::parse($period_end)->translatedFormat('d F Y') }}</strong>
                @elseif($period_start)
                    À compter du <strong>{{ \Carbon\Carbon::parse($period_start)->translatedFormat('d F Y') }}</strong>
                @endif
            </p>
        </div>
    </div>

    {{-- 1. Indicateurs clés --}}
    <div class="synth-kpi-grid">
        <div class="synth-kpi">
            <div class="synth-kpi-value synth-kpi-accent">{{ number_format($total) }}</div>
            <div class="synth-kpi-label">Missions enregistrées</div>
        </div>
        <div class="synth-kpi">
            <div class="synth-kpi-value synth-kpi-accent-teal">{{ number_format($totalJours) }}</div>
            <div class="synth-kpi-label">Jours de déplacement (total)</div>
        </div>
        <div class="synth-kpi">
            <div class="synth-kpi-value synth-kpi-accent-green">{{ $avgDuration }}</div>
            <div class="synth-kpi-label">Durée moyenne par mission (jours)</div>
        </div>
        <div class="synth-kpi">
            @if($topDest)
                <div class="synth-kpi-value synth-kpi-accent-red">{{ $topDest['percent'] }}%</div>
                <div class="synth-kpi-label">{{ $topDest['name'] }} — destination principale</div>
            @else
                <div class="synth-kpi-value">—</div>
                <div class="synth-kpi-label">Destination principale</div>
            @endif
        </div>
    </div>

    {{-- 2. Par direction / demandeur --}}
    <div class="synth-section">
        <h5 class="synth-section-title">
            <i class="bi bi-building"></i>
            2. Analyse par Direction / Demandeur
        </h5>
        @if($byDemandeur->count() > 0)
            @foreach($byDemandeur as $name => $count)
            <div class="bar-row">
                <span class="bar-label" title="{{ $name }}">{{ $name }}</span>
                <div class="bar-track">
                    <div class="bar-fill" style="width:{{ round($count / $maxDemandeur * 100) }}%"></div>
                </div>
                <span class="bar-count">{{ $count }}</span>
            </div>
            @endforeach
        @else
            <p class="text-muted mb-0">Aucun demandeur renseigné.</p>
        @endif
    </div>

    {{-- 3. Top destinations --}}
    <div class="synth-section">
        <h5 class="synth-section-title">
            <i class="bi bi-geo-alt"></i>
            3. Top Destinations
        </h5>
        @if($byDest->count() > 0)
            @foreach($byDest as $dest => $count)
            @php $pct = round($count / $total * 100); @endphp
            <div class="bar-row">
                <span class="bar-label" title="{{ $dest }}">{{ $dest }}</span>
                <div class="bar-track">
                    <div class="bar-fill" style="width:{{ round($count / $maxDest * 100) }}%;background:rgba(122,144,0,.7)"></div>
                </div>
                <span class="bar-count">{{ $count }}</span>
                <span class="text-muted" style="font-size:.75rem;width:40px;text-align:right">{{ $pct }}%</span>
            </div>
            @endforeach
        @else
            <p class="text-muted mb-0">Aucune destination renseignée.</p>
        @endif
    </div>

    {{-- 4. Motifs / Raisons --}}
    @if($byRaison->count() > 0)
    @php $maxRaison = $byRaison->max() ?: 1; @endphp
    <div class="synth-section">
        <h5 class="synth-section-title">
            <i class="bi bi-tag"></i>
            4. Motifs de Déplacement
        </h5>
        @foreach($byRaison as $raison => $count)
        <div class="bar-row">
            <span class="bar-label" title="{{ $raison }}" style="width:280px">{{ $raison }}</span>
            <div class="bar-track">
                <div class="bar-fill" style="width:{{ round($count / $maxRaison * 100) }}%;background:rgba(0,184,212,.7)"></div>
            </div>
            <span class="bar-count">{{ $count }}</span>
        </div>
        @endforeach
    </div>
    @endif

    {{-- 5. Couverture géographique --}}
    @if($byRegion->count() > 0)
    <div class="synth-section">
        <h5 class="synth-section-title">
            <i class="bi bi-map"></i>
            5. Couverture Géographique par Région
        </h5>
        <div class="row g-3">
            @foreach($byRegion->sortDesc() as $region => $info)
            <div class="col-md-6 col-lg-4">
                <div style="background:var(--border);border-radius:10px;padding:1rem;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong style="font-size:.9rem">{{ $region }}</strong>
                        <span class="badge bg-primary bg-opacity-10 text-primary">{{ $info['count'] }} mission{{ $info['count'] > 1 ? 's' : '' }}</span>
                    </div>
                    <div>
                        @foreach($info['villes'] as $ville)
                            <span class="region-badge">{{ $ville }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- 6. Liste détaillée --}}
    <div class="synth-section">
        <h5 class="synth-section-title">
            <i class="bi bi-list-columns"></i>
            6. Liste Détaillée des Missions
        </h5>
        <div class="table-responsive">
            <table class="table table-sm table-hover mission-table mb-0">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Date</th>
                        <th>Direction / Demandeur</th>
                        <th>Chauffeur</th>
                        <th>Véhicule</th>
                        <th>Destination</th>
                        <th class="text-center">Jours</th>
                        <th>Raison du déplacement</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($missions as $i => $m)
                    <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td style="white-space:nowrap">
                            {{ $m->date_start->format('d/m/Y') }}
                            @if($m->date_end->format('d/m/Y') !== $m->date_start->format('d/m/Y'))
                                → {{ $m->date_end->format('d/m/Y') }}
                            @endif
                        </td>
                        <td>{{ $m->demandeur?->name ?? '—' }}</td>
                        <td>{{ $m->driver ? $m->driver->full_name : '—' }}</td>
                        <td>{{ $m->vehicle?->registration ?? '—' }}</td>
                        <td>{{ $m->city?->name ?? ($m->destination ?? '—') }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary bg-opacity-75">{{ $m->nb_jours }}</span>
                        </td>
                        <td>{{ $m->raison ?? '—' }}</td>
                        <td>
                            @php
                                $badge = match($m->status) {
                                    'pending'   => 'bg-warning text-dark',
                                    'approved'  => 'bg-info',
                                    'completed' => 'bg-success',
                                    'rejected'  => 'bg-danger',
                                    default     => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badge }}" style="font-size:.72rem">{{ $m->status_label }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="6" class="text-end">Total jours :</td>
                        <td class="text-center">{{ $totalJours }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif
</div>
