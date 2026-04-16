<div>
    <p class="section-label">Bons de travail - Sortie des pièces de rechange et travaux sur véhicule</p>
    <div class="row g-2 mb-3">
        <div class="col-md-2">
            <div class="alert alert-light border mb-0 py-2">En attente: <strong>{{ $statusStats['pending'] }}</strong>
            </div>
        </div>
        <div class="col-md-2">
            <div class="alert alert-light border mb-0 py-2">En cours: <strong>{{ $statusStats['in_progress'] }}</strong>
            </div>
        </div>
        <div class="col-md-2">
            <div class="alert alert-light border mb-0 py-2">Terminés: <strong>{{ $statusStats['completed'] }}</strong>
            </div>
        </div>
        <div class="col-md-2">
            <div class="alert alert-light border mb-0 py-2">Validés: <strong>{{ $statusStats['validated'] }}</strong>
            </div>
        </div>
        <div class="col-md-4">
            <div class="alert alert-info mb-0 py-2">Progression moyenne:
                <strong>{{ $statusStats['avg_progress'] }}%</strong></div>
        </div>
    </div>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Bons de travail</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;"
                        placeholder="Véhicule, référence, mécanicien..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="status_filter">
                        <option value="">Tous statuts</option>
                        <option value="pending">En attente</option>
                        <option value="in_progress">En cours</option>
                        <option value="completed">Terminé</option>
                        <option value="validated">Validé</option>
                    </select>
                    <input type="date" class="form-control form-control-sm" style="width: 160px;"
                        wire:model.live="date_filter">
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouveau bon de travail
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Référence</th>
                        <th>Véhicule</th>
                        <th>Date</th>
                        <th>Mécanicien</th>
                        <th>Description</th>
                        <th>Progression</th>
                        <th>Coût total</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workOrders as $workOrder)
                        <tr>
                            <td><span class="badge bg-info">{{ $workOrder->reference }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $workOrder->vehicle->registration }}</div>
                                @if ($workOrder->diagnostic)
                                    <small class="text-muted">Diagnostic:
                                        {{ $workOrder->diagnostic->reference }}</small>
                                @endif
                            </td>
                            <td>{{ $workOrder->work_date->format('d/m/Y') }}</td>
                            <td>
                                <div>{{ $workOrder->mechanic->last_name }} {{ $workOrder->mechanic->first_name }}</div>
                                @if ($workOrder->work_duration)
                                    <small class="text-muted">{{ $workOrder->work_duration }}</small>
                                @endif
                            </td>
                            <td>
                                <div>{{ Str::limit($workOrder->work_description, 50) }}</div>
                                @if (strlen($workOrder->work_description) > 50)
                                    <small class="text-muted">...</small>
                                @endif
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $workOrder->completion_percent }}%</div>
                                <div class="progress" style="height:4px">
                                    <div class="progress-bar" style="width: {{ $workOrder->completion_percent }}%">
                                    </div>
                                </div>
                            </td>
                            <td class="fw-semibold text-end">
                                @if ($workOrder->total_cost)
                                    {{ number_format($workOrder->total_cost, 2, ',', ' ') }} FCFA
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $workOrder->status_color }}">
                                    {{ $workOrder->status_label }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-info"
                                        wire:click="viewWorkOrder({{ $workOrder->id }})" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary"
                                        wire:click="openPhotos({{ $workOrder->id }})" title="Photos">
                                        <i class="bi bi-camera"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary"
                                        wire:click="openEdit({{ $workOrder->id }})" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-success"
                                        wire:click="validateWorkOrder({{ $workOrder->id }})" title="Valider"
                                        wire:loading.attr="validating{{ $workOrder->id }}">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-warning"
                                        wire:click="downloadPDF({{ $workOrder->id }})" title="Télécharger PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger"
                                        wire:click="confirmDelete({{ $workOrder->id }})" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-hammer fs-1 d-block mb-2"></i>
                                Aucun bon de travail trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light">
            {{ $workOrders->links() }}
        </div>
    </div>

    <!-- Modal Formulaire Bon de travail -->
    @if ($showFormModal)
        <div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-height: 90vh;">
                <div class="modal-content" style="max-height: 90vh; overflow: hidden;">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $editingId ? 'Modifier le bon de travail' : 'Nouveau bon de travail' }}</h5>
                        <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
                    </div>
                    <form wire:submit="saveWorkOrder">
                        <div class="modal-body" style="max-height: calc(90vh - 120px); overflow-y: auto;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Véhicule <span class="text-danger">*</span></label>
                                    <select class="form-select @error('vehicle_id') is-invalid @enderror"
                                        wire:model="vehicle_id" @if ($diagnostic_id) disabled @endif>
                                        <option value="">—</option>
                                        @foreach ($vehicles as $v)
                                            <option value="{{ $v->id }}">{{ $v->registration }}</option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Diagnostic associé</label>
                                    <select class="form-select" wire:model.live="diagnostic_id">
                                        <option value="">Aucun</option>
                                        @foreach ($diagnostics as $d)
                                            <option value="{{ $d->id }}">{{ $d->reference }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Réf. fiche transfert</label>
                                    <input type="text" class="form-control" wire:model="transfer_reference"
                                        placeholder="TR-...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date transfert</label>
                                    <input type="date" class="form-control" wire:model="transfer_date">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mécanicien <span class="text-danger">*</span></label>
                                    <select class="form-select @error('mechanic_id') is-invalid @enderror"
                                        wire:model="mechanic_id">
                                        <option value="">—</option>
                                        @foreach ($mechanics as $m)
                                            <option value="{{ $m->id }}">{{ $m->last_name }}
                                                {{ $m->first_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('mechanic_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date travail <span class="text-danger">*</span></label>
                                    <input type="date"
                                        class="form-control @error('work_date') is-invalid @enderror"
                                        wire:model="work_date">
                                    @error('work_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Heure début</label>
                                    <input type="time" class="form-control" wire:model="start_time"
                                        placeholder="08:00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Heure fin</label>
                                    <input type="time" class="form-control" wire:model="end_time"
                                        placeholder="17:00">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description des travaux <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control js-rich-text @error('work_description') is-invalid @enderror" rows="3"
                                        wire:model="work_description" placeholder="Description détaillée des travaux à effectuer..."></textarea>
                                    @error('work_description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            {{-- ─── SECTION : INCIDENT ──────────────────────────────────────── --}}
                            <div class="row g-3 mt-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="bi bi-exclamation-triangle me-1"></i> Incident / Description du
                                        dysfonctionnement
                                    </h6>
                                </div>

                                {{-- Kilométrage --}}
                                <div class="col-md-4">
                                    <label class="form-label">Kilométrage</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control" wire:model="mileage"
                                            placeholder="ex: 125000" min="0">
                                        <span class="input-group-text">km</span>
                                    </div>
                                </div>

                                {{-- ─── État des systèmes ─────────────────────────────────── --}}
                                <div class="col-12 mt-2">
                                    <label class="form-label fw-semibold">État des systèmes</label>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0 align-middle">
                                            <thead class="bg-light text-center">
                                                <tr>
                                                    <th style="width:40%">Système</th>
                                                    @foreach (\App\Models\WorkOrder::systemStates() as $val => $label)
                                                        @if ($val !== '')
                                                            <th>{{ $label }}</th>
                                                        @endif
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach (\App\Models\WorkOrder::vehicleSystems() as $field => $systemLabel)
                                                    <tr>
                                                        <td class="fw-semibold small">{{ $systemLabel }}</td>
                                                        @foreach (\App\Models\WorkOrder::systemStates() as $val => $stateLabel)
                                                            @if ($val !== '')
                                                                <td class="text-center">
                                                                    <input type="radio" class="form-check-input"
                                                                        name="{{ $field }}"
                                                                        wire:model="{{ $field }}"
                                                                        value="{{ $val }}">
                                                                </td>
                                                            @endif
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- ─── 4 colonnes : Cause / Type défaillance / Type maintenance / Opération --}}
                                <div class="col-12 mt-3">
                                    <div class="row g-3">

                                        {{-- Cause défaillance --}}
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small text-uppercase text-muted">Cause
                                                défaillance</label>
                                            @foreach (\App\Models\WorkOrder::failureCauses() as $val => $label)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        wire:model="failure_cause" value="{{ $val }}"
                                                        id="fc_{{ $val }}">
                                                    <label class="form-check-label small"
                                                        for="fc_{{ $val }}">{{ $label }}</label>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- Type défaillance --}}
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small text-uppercase text-muted">Type
                                                défaillance</label>
                                            @foreach (\App\Models\WorkOrder::failureTypes() as $val => $label)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        wire:model="failure_type" value="{{ $val }}"
                                                        id="ft_{{ $val }}">
                                                    <label class="form-check-label small"
                                                        for="ft_{{ $val }}">{{ $label }}</label>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- Type de maintenance --}}
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small text-uppercase text-muted">Type
                                                de maintenance</label>
                                            @foreach (\App\Models\WorkOrder::maintenanceTypes() as $val => $label)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        wire:model="maintenance_type" value="{{ $val }}"
                                                        id="mt_{{ $val }}">
                                                    <label class="form-check-label small"
                                                        for="mt_{{ $val }}">{{ $label }}</label>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- Opération --}}
                                        <div class="col-md-3">
                                            <label
                                                class="form-label fw-semibold small text-uppercase text-muted">Opération</label>
                                            @foreach (\App\Models\WorkOrder::operationTypes() as $val => $label)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        wire:model="operation_type" value="{{ $val }}"
                                                        id="ot_{{ $val }}">
                                                    <label class="form-check-label small"
                                                        for="ot_{{ $val }}">{{ $label }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Commentaire cause défaillance --}}
                                <div class="col-12 mt-2">
                                    <label class="form-label">Commentaire (cause défaillance)</label>
                                    <textarea class="form-control" rows="2" wire:model="failure_cause_comment"
                                        placeholder="Détails sur la cause du dysfonctionnement..."></textarea>
                                </div>
                            </div>
                            {{-- ─── FIN SECTION INCIDENT ────────────────────────────────────── --}}


                            {{--
    ══════════════════════════════════════════════════════════════════
    PATCH TABLEAU PIÈCES DE RECHANGE
    Remplacer les colonnes du thead et des lignes tbody dans la table
    "Sorties stock" existante par ce qui suit :
    ══════════════════════════════════════════════════════════════════
--}}

                            {{-- thead amélioré --}}
                            {{--
<thead class="bg-light">
    <tr>
        <th>Article</th>
        <th style="width:90px">Unité</th>
        <th style="width:90px">Qté dem.</th>
        <th style="width:90px">Qté servie</th>
        <th style="width:110px">CMUP</th>
        <th style="width:160px">Magasin</th>
        <th style="width:200px">Marque / N° série</th>
        <th style="width:60px"></th>
    </tr>
</thead>
--}}

                            {{-- tbody — une ligne exemple (répliquer dans @foreach) --}}
                            {{--
<tr>
    <td>
        <select class="form-select form-select-sm" wire:model="parts_lines.{{ $i }}.article_id">
            <option value="">—</option>
            @foreach ($articles as $a)
                <option value="{{ $a->id }}">{{ $a->name }} ({{ $a->reference }})</option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="text" class="form-control form-control-sm"
               wire:model="parts_lines.{{ $i }}.unit" placeholder="pce">
    </td>
    <td>
        <input type="number" class="form-control form-control-sm"
               wire:model="parts_lines.{{ $i }}.qty_requested" min="0" step="0.001">
    </td>
    <td>
        <input type="number" class="form-control form-control-sm"
               wire:model="parts_lines.{{ $i }}.qty_served" min="0" step="0.001">
    </td>
    <td>
        <input type="number" class="form-control form-control-sm"
               wire:model="parts_lines.{{ $i }}.cmup" min="0" step="0.01" placeholder="FCFA">
    </td>
    <td>
        <select class="form-select form-select-sm" wire:model="parts_lines.{{ $i }}.stock_location">
            <option value="main">Magasin principal</option>
            <option value="garage">Magasin garage</option>
        </select>
    </td>
    <td>
        <input type="text" class="form-control form-control-sm"
               wire:model="parts_lines.{{ $i }}.part_description"
               placeholder="Marque / N° série">
    </td>
    <td>
        @if (count($parts_lines) > 1)
            <button type="button" class="btn btn-sm btn-outline-danger"
                    wire:click="removePartLine({{ $i }})">
                <i class="bi bi-trash"></i>
            </button>
        @endif
    </td>
</tr>
--}}





                            <div class="row g-3 mt-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">Pièces et matériel</h6>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold">Sorties stock (consommation interne)</span>
                                        <div class="d-flex gap-2">
                                            <a class="btn btn-sm btn-outline-success"
                                                href="{{ route('stock.purchase-orders', ['work_order_id' => $editingId]) }}"
                                                target="_blank">
                                                <i class="bi bi-cart-check me-1"></i> Bon de commande lié
                                            </a>
                                            @if ($editingId)
                                                <button type="button"
                                                    class="btn btn-sm {{ $stock_applied ? 'btn-outline-secondary' : 'btn-outline-primary' }}"
                                                    wire:click="applyStockExit" @disabled($stock_applied)>
                                                    <i class="bi bi-box-arrow-down me-1"></i> Appliquer sorties stock
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-2 align-middle">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Article <span class="text-muted fw-normal">(recherche par mots-clés)</span></th>
                                                    <th style="width:90px">Qté</th>
                                                    <th style="width:130px">Prix unit. (FCFA)</th>
                                                    <th style="width:120px">Total ligne</th>
                                                    <th>Description / Marque</th>
                                                    <th style="width:150px">Magasin</th>
                                                    <th style="width:36px"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $partsTotal = 0; @endphp
                                                @foreach ($parts_lines as $i => $line)
                                                    @php
                                                        $lineTotal = (filled($line['unit_price'] ?? null) && filled($line['quantity'] ?? null))
                                                            ? round((float) $line['unit_price'] * (int) $line['quantity'], 0)
                                                            : null;
                                                        if ($lineTotal !== null) $partsTotal += $lineTotal;
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <input type="text"
                                                                class="form-control form-control-sm mb-1"
                                                                placeholder="Taper un mot-clé pour filtrer…"
                                                                oninput="filterArticleSelect(this, 'art-sel-{{ $i }}')">
                                                            <select id="art-sel-{{ $i }}"
                                                                class="form-select form-select-sm"
                                                                wire:model.live="parts_lines.{{ $i }}.article_id">
                                                                <option value="">— Sélectionner —</option>
                                                                @foreach ($articles as $a)
                                                                    <option value="{{ $a->id }}"
                                                                        data-price="{{ $a->purchase_price ?? '' }}"
                                                                        data-stock="{{ $a->total_stock ?? 0 }}">
                                                                        {{ $a->name }} ({{ $a->reference }})
                                                                        @if(($a->total_stock ?? 0) > 0)
                                                                            — Stock: {{ $a->total_stock }}
                                                                        @else
                                                                            — Rupture
                                                                        @endif
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control form-control-sm"
                                                                wire:model.live="parts_lines.{{ $i }}.quantity"
                                                                min="1">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control form-control-sm"
                                                                wire:model.live="parts_lines.{{ $i }}.unit_price"
                                                                min="0" step="1" placeholder="FCFA">
                                                        </td>
                                                        <td class="fw-semibold text-end">
                                                            @if($lineTotal !== null)
                                                                {{ number_format($lineTotal, 0, ',', ' ') }}
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control form-control-sm"
                                                                wire:model="parts_lines.{{ $i }}.part_description"
                                                                placeholder="Marque, N° série…">
                                                        </td>
                                                        <td>
                                                            <select class="form-select form-select-sm"
                                                                wire:model="parts_lines.{{ $i }}.stock_location">
                                                                <option value="main">Magasin principal</option>
                                                                <option value="garage">Magasin garage</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            @if (count($parts_lines) > 1)
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    wire:click="removePartLine({{ $i }})">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @if($partsTotal > 0)
                                                <tr class="table-light fw-semibold">
                                                    <td colspan="3" class="text-end">Total pièces :</td>
                                                    <td class="text-end text-primary">{{ number_format($partsTotal, 0, ',', ' ') }} FCFA</td>
                                                    <td colspan="3"></td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        wire:click="addPartLine"><i class="bi bi-plus-lg me-1"></i>Ajouter une
                                        pièce</button>
                                    @if ($stock_applied)
                                        <div class="small text-muted mt-1">Sorties stock déjà appliquées pour ce bon.
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pièces utilisées</label>
                                    <textarea class="form-control js-rich-text" rows="3" wire:model="parts_used"
                                        placeholder="Liste des pièces utilisées avec quantités..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pièces retirées</label>
                                    <textarea class="form-control js-rich-text" rows="3" wire:model="parts_removed"
                                        placeholder="Pièces retirées du véhicule..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Matériel utilisé</label>
                                    <textarea class="form-control" rows="2" wire:model="equipment_used"
                                        placeholder="Outils et matériel utilisés..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Outils utilisés</label>
                                    <textarea class="form-control" rows="2" wire:model="tools_used" placeholder="Outils de travail utilisés..."></textarea>
                                </div>
                            </div>

                            <div class="row g-3 mt-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">Détails techniques</h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Problèmes identifiés</label>
                                    <textarea class="form-control js-rich-text" rows="3" wire:model="problems_found"
                                        placeholder="Problèmes supplémentaires découverts..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Solutions appliquées</label>
                                    <textarea class="form-control js-rich-text" rows="3" wire:model="solutions_applied"
                                        placeholder="Solutions mises en œuvre..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Notes techniques</label>
                                    <textarea class="form-control" rows="2" wire:model="technical_notes"
                                        placeholder="Notes techniques importantes..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contrôle qualité</label>
                                    <textarea class="form-control" rows="2" wire:model="quality_control"
                                        placeholder="Vérifications qualité effectuées..."></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Vérifications finales</label>
                                    <textarea class="form-control" rows="2" wire:model="final_checks"
                                        placeholder="Contrôles finaux avant livraison..."></textarea>
                                </div>
                            </div>

                            <div class="row g-3 mt-4">
                                <div class="col-md-4">
                                    <label class="form-label">Coût main d'œuvre (FCFA)</label>
                                    <input type="text" class="form-control" wire:model="labor_cost"
                                        placeholder="ex: 25000">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Coût pièces (FCFA)</label>
                                    <input type="text" class="form-control" wire:model="parts_cost"
                                        placeholder="ex: 45000">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Coût total (FCFA)</label>
                                    <input type="text" class="form-control bg-light" readonly
                                        value="{{ isset($totalCost) ? number_format($totalCost, 2, ',', ' ') : '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Statut</label>
                                    <select class="form-select" wire:model="status">
                                        <option value="pending">En attente</option>
                                        <option value="in_progress">En cours</option>
                                        <option value="completed">Terminé</option>
                                        <option value="validated">Validé</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Progression (%)</label>
                                    <input type="number" class="form-control" wire:model="completion_percent"
                                        min="0" max="100">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Notes de fin de travaux</label>
                                    <textarea class="form-control" rows="2" wire:model="completion_notes"
                                        placeholder="Notes sur l'achèvement des travaux..."></textarea>
                                </div>
                            </div>
                            <div class="row g-3 mt-4">
                                <div class="col-12 d-flex justify-content-between align-items-center">
                                    <h6 class="text-primary mb-0">Programme de tâches</h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        wire:click="addTaskLine"><i class="bi bi-plus-lg me-1"></i>Ajouter</button>
                                </div>
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Tâche</th>
                                                    <th style="width:130px">Durée (min)</th>
                                                    <th style="width:220px">Ressource</th>
                                                    <th style="width:90px">Fait</th>
                                                    <th style="width:60px"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tasks as $i => $task)
                                                    <tr>
                                                        <td><input type="text" class="form-control form-control-sm"
                                                                wire:model="tasks.{{ $i }}.title"></td>
                                                        <td><input type="number" class="form-control form-control-sm"
                                                                wire:model="tasks.{{ $i }}.estimated_minutes"
                                                                min="0"></td>
                                                        <td>
                                                            <select class="form-select form-select-sm"
                                                                wire:model="tasks.{{ $i }}.mechanic_id">
                                                                <option value="">—</option>
                                                                @foreach ($mechanics as $m)
                                                                    <option value="{{ $m->id }}">
                                                                        {{ $m->last_name }} {{ $m->first_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td class="text-center"><input type="checkbox"
                                                                wire:model="tasks.{{ $i }}.is_done"></td>
                                                        <td>
                                                            @if (count($tasks) > 1)
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    wire:click="removeTaskLine({{ $i }})"><i
                                                                        class="bi bi-trash"></i></button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mt-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-2">Photos avant / après</h6>
                                    @if (!$editingId)
                                        <div class="alert alert-light border small mb-0">Enregistrez d’abord le bon,
                                            puis ajoutez les photos.</div>
                                    @endif
                                </div>
                                @if ($editingId)
                                    <div class="col-md-3">
                                        <label class="form-label">Date prise</label>
                                        <input type="date" class="form-control form-control-sm"
                                            wire:model="photo_taken_at">
                                    </div>
                                    <div class="col-md-9">
                                        <label class="form-label">Légende</label>
                                        <input type="text" class="form-control form-control-sm"
                                            wire:model="photo_caption">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Ajouter photo AVANT</label>
                                        <div class="d-flex gap-2">
                                            <input type="file" class="form-control form-control-sm"
                                                wire:model="before_photo_file" accept="image/*">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                wire:click="uploadBeforePhoto">Ajouter</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Ajouter photo APRÈS</label>
                                        <div class="d-flex gap-2">
                                            <input type="file" class="form-control form-control-sm"
                                                wire:model="after_photo_file" accept="image/*">
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                wire:click="uploadAfterPhoto">Ajouter</button>
                                        </div>
                                    </div>

                                    @php
                                        $workOrderPhotos = \App\Models\WorkOrder::with('photos')->find($editingId);
                                    @endphp
                                    @if ($workOrderPhotos && $workOrderPhotos->photos->isNotEmpty())
                                        <div class="col-12 mt-3">
                                            <label class="form-label fw-semibold">Photos déjà ajoutées</label>
                                            <div class="row g-2">
                                                @foreach ($workOrderPhotos->photos as $photo)
                                                    <div class="col-6 col-md-3 text-center">
                                                        <div class="card">
                                                            <img src="{{ asset('storage/' . $photo->file_path) }}"
                                                                class="card-img-top img-fluid" alt="{{ $photo->caption ?? 'Photo' }}">
                                                            <div class="card-body p-2">
                                                                <div class="small mb-1">{{ ucfirst($photo->phase) }}</div>
                                                                <div class="small text-muted">{{ $photo->caption ?? '-' }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                wire:click="$set('showFormModal', false)">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Vue Bon de travail -->
    @if ($showViewModal)
        <div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-height: 90vh;">
                <div class="modal-content" style="max-height: 90vh; overflow: hidden;">
                    <div class="modal-header">
                        <h5 class="modal-title">Voir le bon de travail</h5>
                        <button type="button" class="btn-close" wire:click="$set('showViewModal', false)"></button>
                    </div>
                    <div class="modal-body" style="max-height: calc(90vh - 120px); overflow-y: auto;">
                        @php
                            $workOrder = \App\Models\WorkOrder::find($editingId);
                        @endphp

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Référence:</strong> {{ $workOrder->reference }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Date:</strong> {{ $workOrder->work_date->format('d/m/Y') }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Statut:</strong>
                                                <span class="badge bg-{{ $workOrder->status_color }}">
                                                    {{ $workOrder->status_label }}
                                                </span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Durée:</strong> {{ $workOrder->work_duration ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($workOrder->photos->isNotEmpty())
                                <div class="col-12">
                                    <div class="card mb-3">
                                        <div class="card-header">Photos avant / après</div>
                                        <div class="card-body">
                                            <div class="row g-2">
                                                @foreach ($workOrder->photos as $photo)
                                                    <div class="col-6 col-md-3 text-center">
                                                        <img src="{{ asset('storage/' . $photo->file_path) }}" class="img-fluid rounded" alt="{{ $photo->caption ?? 'Photo' }}">
                                                        <div class="small mt-1">{{ ucfirst($photo->phase) }} - {{ $photo->caption ?? '-' }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Véhicule</label>
                                <div class="form-control bg-light">{{ $workOrder->vehicle->registration }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mécanicien</label>
                                <div class="form-control bg-light">{{ $workOrder->mechanic->last_name }}
                                    {{ $workOrder->mechanic->first_name }}</div>
                            </div>
                            @if ($workOrder->diagnostic)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Diagnostic associé</label>
                                    <div class="form-control bg-light">{{ $workOrder->diagnostic->reference }}</div>
                                </div>
                            @endif
                            @if ($workOrder->start_time && $workOrder->end_time)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Heures</label>
                                    <div class="form-control bg-light">{{ $workOrder->start_time->format('H:i') }} -
                                        {{ $workOrder->end_time->format('H:i') }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label fw-bold">Description des travaux</label>
                                <div class="form-control bg-light" style="min-height: 100px;">
                                    {{ $workOrder->work_description }}</div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Pièces et matériel</h6>
                            </div>
                            @if ($workOrder->parts_used)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Pièces utilisées</label>
                                    <div class="form-control bg-light" style="min-height: 100px;">
                                        {{ $workOrder->parts_used }}</div>
                                </div>
                            @endif
                            @if ($workOrder->parts_removed)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Pièces retirées</label>
                                    <div class="form-control bg-light" style="min-height: 100px;">
                                        {{ $workOrder->parts_removed }}</div>
                                </div>
                            @endif
                            @if ($workOrder->equipment_used)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Matériel utilisé</label>
                                    <div class="form-control bg-light" style="min-height: 80px;">
                                        {{ $workOrder->equipment_used }}</div>
                                </div>
                            @endif
                            @if ($workOrder->tools_used)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Outils utilisés</label>
                                    <div class="form-control bg-light" style="min-height: 80px;">
                                        {{ $workOrder->tools_used }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Détails techniques</h6>
                            </div>
                            @if ($workOrder->problems_found)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Problèmes identifiés</label>
                                    <div class="form-control bg-light" style="min-height: 100px;">
                                        {{ $workOrder->problems_found }}</div>
                                </div>
                            @endif
                            @if ($workOrder->solutions_applied)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Solutions appliquées</label>
                                    <div class="form-control bg-light" style="min-height: 100px;">
                                        {{ $workOrder->solutions_applied }}</div>
                                </div>
                            @endif
                            @if ($workOrder->technical_notes)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Notes techniques</label>
                                    <div class="form-control bg-light" style="min-height: 80px;">
                                        {{ $workOrder->technical_notes }}</div>
                                </div>
                            @endif
                            @if ($workOrder->quality_control)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Contrôle qualité</label>
                                    <div class="form-control bg-light" style="min-height: 80px;">
                                        {{ $workOrder->quality_control }}</div>
                                </div>
                            @endif
                            @if ($workOrder->final_checks)
                                <div class="col-12">
                                    <label class="form-label fw-bold">Vérifications finales</label>
                                    <div class="form-control bg-light" style="min-height: 80px;">
                                        {{ $workOrder->final_checks }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Coûts</h6>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Coût main d'œuvre</label>
                                <div class="form-control bg-light">
                                    @if ($workOrder->labor_cost)
                                        {{ number_format($workOrder->labor_cost, 2, ',', ' ') }} FCFA
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Coût pièces</label>
                                <div class="form-control bg-light">
                                    @if ($workOrder->parts_cost)
                                        {{ number_format($workOrder->parts_cost, 2, ',', ' ') }} FCFA
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Coût total</label>
                                <div class="form-control bg-light fw-bold text-primary">
                                    @if ($workOrder->total_cost)
                                        {{ number_format($workOrder->total_cost, 2, ',', ' ') }} FCFA
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            @if ($workOrder->completion_notes)
                                <div class="col-12">
                                    <label class="form-label fw-bold">Notes de fin de travaux</label>
                                    <div class="form-control bg-light" style="min-height: 80px;">
                                        {{ $workOrder->completion_notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning"
                            wire:click="downloadPDF({{ $workOrder->id }})">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Télécharger PDF
                        </button>
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('showViewModal', false)">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Suppression -->
    @if ($showDeleteModal)
        <div class="modal show d-block" tabindex="-1" style="background: rgba(00,0,0,0.4);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmer la suppression</h5>
                        <button type="button" class="btn-close"
                            wire:click="$set('showDeleteModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer ce bon de travail ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('showDeleteModal', false)">Annuler</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteWorkOrder">Supprimer</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Photos -->
    @if ($showPhotosModal)
        <div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Photos du bon de travail</h5>
                        <button type="button" class="btn-close" wire:click="$set('showPhotosModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        @php
                            $workOrderPhotos = \App\Models\WorkOrder::with('photos')->find($editingId);
                        @endphp

                        @if ($workOrderPhotos)
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Date prise</label>
                                    <input type="date" class="form-control form-control-sm"
                                        wire:model="photo_taken_at">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Légende</label>
                                    <input type="text" class="form-control form-control-sm"
                                        wire:model="photo_caption">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Ajouter photo AVANT</label>
                                    <div class="d-flex gap-2">
                                        <input type="file" class="form-control form-control-sm"
                                            wire:model="before_photo_file" accept="image/*">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            wire:click="uploadBeforePhoto">Ajouter</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Ajouter photo APRÈS</label>
                                    <div class="d-flex gap-2">
                                        <input type="file" class="form-control form-control-sm"
                                            wire:model="after_photo_file" accept="image/*">
                                        <button type="button" class="btn btn-sm btn-outline-success"
                                            wire:click="uploadAfterPhoto">Ajouter</button>
                                    </div>
                                </div>

                                @if ($workOrderPhotos->photos->isNotEmpty())
                                    <div class="col-12 mt-3">
                                        <label class="form-label fw-semibold">Photos déjà ajoutées</label>
                                        <div class="row g-2">
                                            @foreach ($workOrderPhotos->photos as $photo)
                                                <div class="col-6 col-md-3 text-center">
                                                    <div class="card">
                                                        <img src="{{ asset('storage/' . $photo->file_path) }}"
                                                            class="card-img-top img-fluid" alt="{{ $photo->caption ?? 'Photo' }}">
                                                        <div class="card-body p-2">
                                                            <div class="small mb-1">{{ ucfirst($photo->phase) }}</div>
                                                            <div class="small text-muted">{{ $photo->caption ?? '-' }}</div>
                                                            <button type="button" class="btn btn-sm btn-outline-danger mt-1"
                                                                wire:click="deletePhoto({{ $photo->id }})"
                                                                wire:confirm="Êtes-vous sûr de vouloir supprimer cette photo ?">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('showPhotosModal', false)">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
<script>
function filterArticleSelect(input, selectId) {
    const select = document.getElementById(selectId);
    if (!select) return;
    const filter = input.value.toLowerCase().trim();
    let firstVisible = null;
    Array.from(select.options).forEach(opt => {
        if (opt.value === '') { opt.style.display = ''; return; }
        const match = filter === '' || opt.text.toLowerCase().includes(filter);
        opt.style.display = match ? '' : 'none';
        if (match && !firstVisible) firstVisible = opt;
    });
}
</script>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    (function() {
        function initEditors() {
            if (!window.tinymce) return;
            document.querySelectorAll('textarea.js-rich-text').forEach((el) => {
                if (el.dataset.richInited === '1') return;
                el.dataset.richInited = '1';
                tinymce.init({
                    target: el,
                    menubar: false,
                    height: 180,
                    plugins: 'lists link table',
                    toolbar: 'undo redo | bold italic underline | bullist numlist | alignleft aligncenter alignright | table | removeformat',
                    setup: function(editor) {
                        editor.on('change keyup', function() {
                            editor.save();
                            el.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                        });
                    }
                });
            });
        }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initEditors);
        else initEditors();
        document.addEventListener('livewire:navigated', initEditors);
    })();
</script>
