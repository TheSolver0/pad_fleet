<div>
    <p class="section-label">Analyse de réforme — État et rentabilité du parc</p>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 col-lg">
            <div class="card border-0 bg-light h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-2 fw-bold text-secondary">{{ $stats['total'] }}</div>
                    <div class="small text-muted">Véhicules analysés</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg">
            <div class="card border-0 h-100" style="background:#fff0f0;">
                <div class="card-body text-center py-3">
                    <div class="fs-2 fw-bold text-danger">{{ $stats['already_reformed'] }}</div>
                    <div class="small text-danger fw-semibold">En réforme</div>
                    <div class="small text-muted">Âge ≥ 12 ans</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg">
            <div class="card border-0 h-100" style="background:#fff8e1;">
                <div class="card-body text-center py-3">
                    <div class="fs-2 fw-bold text-warning">{{ $stats['near_reform'] }}</div>
                    <div class="small text-warning fw-semibold">Proche réforme</div>
                    <div class="small text-muted">Moins de 2 ans</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg">
            <div class="card border-0 h-100" style="background:#f0fff4;">
                <div class="card-body text-center py-3">
                    <div class="fs-2 fw-bold text-success">{{ $stats['ok'] }}</div>
                    <div class="small text-success fw-semibold">Conformes</div>
                    <div class="small text-muted">Plus de 2 ans</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg">
            <div class="card border-0 h-100" style="background:#f5f0ff;">
                <div class="card-body text-center py-3">
                    <div class="fs-2 fw-bold text-purple" style="color:#7c3aed;">{{ $stats['economic_alerts'] }}</div>
                    <div class="small fw-semibold" style="color:#7c3aed;">Alertes économiques</div>
                    <div class="small text-muted">Coût entretien &gt; valeur</div>
                </div>
            </div>
        </div>
    </div>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Tableau de bord réforme</span>
                <div class="module-toolbar-filters">
                    <select class="form-select form-select-sm" style="width:160px;" wire:model.live="filter">
                        <option value="all">Tous les véhicules</option>
                        <option value="reformed">En réforme</option>
                        <option value="near_reform">Proche réforme</option>
                        <option value="ok">Conformes</option>
                    </select>
                    <select class="form-select form-select-sm" style="width:150px;" wire:model.live="vehicle_category">
                        <option value="">Toutes catégories</option>
                        <option value="leger">Léger</option>
                        <option value="utilitaire">Utilitaire</option>
                        <option value="camionnette">Camionnette</option>
                        <option value="4x4">4×4</option>
                        <option value="lourd">Lourd</option>
                        <option value="bus">Bus</option>
                        <option value="moto">Moto</option>
                        <option value="autre">Autre</option>
                    </select>
                    <select class="form-select form-select-sm" style="width:175px;" wire:model.live="sort_by">
                        <option value="reform_urgency">Trier : urgence réforme</option>
                        <option value="maintenance_cost">Trier : coût entretien</option>
                        <option value="cost_ratio">Trier : ratio coût/valeur</option>
                    </select>
                </div>
            </div>
        </div>

        @if(count($vehiclesData) === 0)
        <div class="text-center text-muted py-5">
            <i class="bi bi-search fs-2 d-block mb-2"></i>
            Aucun véhicule trouvé pour ce filtre.
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="bg-light">
                    <tr>
                        <th>Immatriculation</th>
                        <th>Modèle</th>
                        <th class="text-center">Âge</th>
                        <th class="text-center">Date réforme</th>
                        <th class="text-center">Délai</th>
                        <th class="text-end">Prix achat</th>
                        <th class="text-end">Valeur vénale</th>
                        <th class="text-end">Coût entretien</th>
                        <th class="text-center">Ratio</th>
                        <th class="text-center">Statut</th>
                        <th class="text-center">Rentabilité</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehiclesData as $row)
                    @php
                        $v = $row['vehicle'];
                        $days = $row['days_to_reform'];
                        $past = $days < 0;
                        $absDays = abs($days);

                        if ($past) {
                            $delaiLabel = 'Dépassée de ' . floor($absDays / 30) . ' mois';
                            $delaiClass = 'text-danger';
                        } elseif ($absDays < 30) {
                            $delaiLabel = 'Moins d\'un mois';
                            $delaiClass = 'text-danger fw-bold';
                        } elseif ($absDays < 365) {
                            $delaiLabel = floor($absDays / 30) . ' mois';
                            $delaiClass = 'text-warning fw-semibold';
                        } else {
                            $years = floor($absDays / 365);
                            $months = floor(($absDays % 365) / 30);
                            $delaiLabel = $years . ' an' . ($years > 1 ? 's' : '') . ($months > 0 ? ' ' . $months . ' mois' : '');
                            $delaiClass = 'text-success';
                        }

                        $ratio = $row['cost_ratio'];
                        if ($row['economic_alert']) {
                            $ratioClass = 'bg-danger text-white';
                        } elseif ($ratio >= 60) {
                            $ratioClass = 'bg-warning text-dark';
                        } elseif ($ratio >= 30) {
                            $ratioClass = 'bg-info text-dark';
                        } else {
                            $ratioClass = 'bg-success text-white';
                        }
                    @endphp
                    <tr class="{{ $row['status'] === 'reformed' ? 'table-danger' : ($row['status'] === 'near_reform' ? 'table-warning' : '') }}">
                        <td>
                            <strong>{{ $v->registration }}</strong>
                            @if($v->category)
                                <br><span class="text-muted" style="font-size:0.75em;">{{ $v->category_label }}</span>
                            @endif
                        </td>
                        <td>
                            @if($v->vehicleModel)
                                {{ $v->vehicleModel->brand->name ?? '' }} {{ $v->vehicleModel->name }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="{{ $row['age_years'] >= 12 ? 'text-danger fw-bold' : ($row['age_years'] >= 10 ? 'text-warning fw-semibold' : '') }}">
                                {{ $row['age_years'] }} ans
                            </span>
                            @if($v->purchase_date)
                                <br><span class="text-muted" style="font-size:0.75em;">Achat {{ $v->purchase_date->format('d/m/Y') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $row['reform_date']->format('d/m/Y') }}
                        </td>
                        <td class="text-center {{ $delaiClass }}">
                            @if($past)
                                <i class="bi bi-exclamation-circle-fill me-1"></i>
                            @endif
                            {{ $delaiLabel }}
                        </td>
                        <td class="text-end">
                            @if($row['purchase_price'] > 0)
                                {{ number_format($row['purchase_price'], 0, ',', ' ') }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($row['venal_value'] > 0)
                                {{ number_format($row['venal_value'], 0, ',', ' ') }}
                            @elseif($row['purchase_price'] > 0)
                                <span class="text-muted">≈ 0</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end fw-semibold">
                            @if($row['total_maintenance'] > 0)
                                {{ number_format($row['total_maintenance'], 0, ',', ' ') }}
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($row['purchase_price'] > 0)
                                <span class="badge {{ $ratioClass }}">{{ $ratio }} %</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($row['status'] === 'reformed')
                                <span class="badge bg-danger">En réforme</span>
                            @elseif($row['status'] === 'near_reform')
                                <span class="badge bg-warning text-dark">Proche réforme</span>
                            @else
                                <span class="badge bg-success">Conforme</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($row['economic_alert'])
                                <span class="badge" style="background:#7c3aed;">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Envoyer en réforme
                                </span>
                            @elseif($row['cost_ratio'] >= 60)
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-exclamation-circle me-1"></i>À surveiller
                                </span>
                            @else
                                <span class="badge bg-light text-muted border">OK</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Légende --}}
        <div class="px-3 py-3 border-top bg-light d-flex flex-wrap gap-3 align-items-center" style="font-size:0.8em;">
            <span class="fw-semibold text-muted">Légende ratio coût / prix achat :</span>
            <span><span class="badge bg-success">0–29 %</span> Sain</span>
            <span><span class="badge bg-info text-dark">30–59 %</span> Surveillance</span>
            <span><span class="badge bg-warning text-dark">60–79 %</span> Préoccupant</span>
            <span><span class="badge bg-danger">≥ 80 % ou coût &gt; valeur vénale</span> Envoyer en réforme</span>
            <span class="ms-auto text-muted">Réforme recommandée à 12 ans · Valeur vénale : amortissement linéaire 8 ans</span>
        </div>
        @endif
    </div>

    {{-- Bloc explicatif --}}
    <div class="alert alert-info d-flex gap-3 align-items-start">
        <i class="bi bi-info-circle-fill fs-4 mt-1 flex-shrink-0"></i>
        <div>
            <strong>Comment lire ce tableau ?</strong><br>
            La colonne <em>Coût entretien</em> cumule toutes les réparations (main d'œuvre, pièces, dépenses annexes) depuis l'enregistrement du véhicule.
            Quand ce coût dépasse la <em>valeur vénale</em> actuelle, il n'est économiquement plus rentable d'investir davantage dans le véhicule :
            il faut l'envoyer en réforme plutôt que de continuer à le réparer.
            <br><em>Exemple : acheter et remettre en état un véhicule à 22 000 000 pour une valeur résiduelle de 18 000 000 n'est pas rentable.</em>
        </div>
    </div>
</div>
