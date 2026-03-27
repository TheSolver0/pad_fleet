<div>
    <p class="section-label">Visites techniques — Suivi, alertes, historique par véhicule</p>

    {{-- KPIs --}}
    <div class="kpi-grid mb-4" style="grid-template-columns: repeat(6,1fr)">
        <div class="kpi-card">
            <div class="kpi-card-header">
                <span class="kpi-card-label">Total</span>
                <span class="kpi-card-icon vehicules"><i class="bi bi-clipboard-check"></i></span>
            </div>
            <div class="kpi-card-value">{{ $stats['total'] }}</div>
            <div class="kpi-card-sub">Visites enregistrées</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-card-header">
                <span class="kpi-card-label">Valides</span>
                <span class="kpi-card-icon dispo"><i class="bi bi-check-circle"></i></span>
            </div>
            <div class="kpi-card-value">{{ $stats['valid'] }}</div>
            <div class="kpi-card-sub">Admis & non expirés</div>
        </div>
        <div class="kpi-card" style="cursor:pointer" wire:click="$set('status_filter','expiring')">
            <div class="kpi-card-header">
                <span class="kpi-card-label">Bientôt expirées</span>
                <span class="kpi-card-icon" style="background:rgba(255,193,7,0.15);color:#d39e00"><i class="bi bi-clock-history"></i></span>
            </div>
            <div class="kpi-card-value text-warning">{{ $stats['expiring_soon'] }}</div>
            <div class="kpi-card-sub">Dans les 30 jours</div>
        </div>
        <div class="kpi-card" style="cursor:pointer" wire:click="$set('status_filter','expired')">
            <div class="kpi-card-header">
                <span class="kpi-card-label">Expirées</span>
                <span class="kpi-card-icon sinistres"><i class="bi bi-exclamation-circle"></i></span>
            </div>
            <div class="kpi-card-value text-danger">{{ $stats['expired'] }}</div>
            <div class="kpi-card-sub">À renouveler</div>
        </div>
        <div class="kpi-card" style="cursor:pointer" wire:click="$set('status_filter','pending')">
            <div class="kpi-card-header">
                <span class="kpi-card-label">En attente</span>
                <span class="kpi-card-icon repa"><i class="bi bi-hourglass-split"></i></span>
            </div>
            <div class="kpi-card-value text-warning">{{ $stats['pending'] }}</div>
            <div class="kpi-card-sub">Ajourné / refusé</div>
        </div>
        <div class="kpi-card" style="cursor:pointer" wire:click="$set('status_filter','without')">
            <div class="kpi-card-header">
                <span class="kpi-card-label">Sans visite</span>
                <span class="kpi-card-icon" style="background:rgba(100,116,139,0.12);color:#64748b"><i class="bi bi-shield-x"></i></span>
            </div>
            <div class="kpi-card-value text-secondary">{{ $stats['without'] }}</div>
            <div class="kpi-card-sub">Véhicules non suivis</div>
        </div>
    </div>

    {{-- Tableau principal --}}
    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">
                    @if($modeWithout)
                        Véhicules sans visite technique
                    @else
                        Visites techniques
                    @endif
                </span>
                <div class="module-toolbar-filters">
                    @if(!$modeWithout)
                        <input type="text" class="form-control form-control-sm" style="width:200px"
                               placeholder="Véhicule, centre, n° cert..." wire:model.live.debounce.300ms="search">
                        <select class="form-select form-select-sm" style="width:140px" wire:model.live="result_filter">
                            <option value="">Tous résultats</option>
                            <option value="admitted">Admis</option>
                            <option value="adjourned">Ajourné</option>
                            <option value="refused">Refusé</option>
                        </select>
                        <select class="form-select form-select-sm" style="width:160px" wire:model.live="status_filter">
                            <option value="">Tous statuts</option>
                            <option value="expiring">Bientôt expirée</option>
                            <option value="expired">Expirée</option>
                            <option value="pending">En attente (ajourné/refusé)</option>
                            <option value="without">Sans visite</option>
                        </select>
                    @else
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                wire:click="$set('status_filter','')">
                            <i class="bi bi-arrow-left me-1"></i> Retour à la liste
                        </button>
                    @endif
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouvelle visite
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">

            {{-- Mode : véhicules sans visite --}}
            @if($modeWithout)
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Immatriculation</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehiclesWithout as $v)
                            <tr>
                                <td class="fw-medium">{{ $v->registration }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            wire:click="openCreate"
                                            title="Enregistrer une visite">
                                        <i class="bi bi-plus-lg me-1"></i>Enregistrer une visite
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-4">Tous les véhicules ont une visite enregistrée.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if(method_exists($vehiclesWithout, 'hasPages') && $vehiclesWithout->hasPages())
                    <div class="p-3 border-top">{{ $vehiclesWithout->links() }}</div>
                @endif

            {{-- Mode normal --}}
            @else
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Véhicule</th>
                            <th>Date visite</th>
                            <th>Expiration</th>
                            <th>Validité</th>
                            <th>Résultat</th>
                            <th class="col-hide-md">Centre de contrôle</th>
                            <th class="col-hide-md">N° Certificat</th>
                            <th class="col-hide-sm">Coût</th>
                            <th class="col-hide-sm">Enregistré par</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inspections as $i)
                            @php
                                $expired      = $i->isExpired();
                                $expiringSoon = $i->isExpiringSoon();
                            @endphp
                            <tr class="{{ $expired ? 'table-danger' : ($expiringSoon ? 'table-warning' : '') }}"
                                style="--bs-table-bg-type: transparent">
                                <td class="fw-medium">{{ $i->vehicle?->registration ?? '—' }}</td>
                                <td style="font-size:.85rem">{{ $i->inspected_at->format('d/m/Y') }}</td>
                                <td style="font-size:.85rem">
                                    {{ $i->expires_at->format('d/m/Y') }}
                                    @if($expired)
                                        <br><small class="text-danger fw-semibold">Expirée</small>
                                    @elseif($expiringSoon)
                                        <br><small class="text-warning fw-semibold">
                                            dans {{ now()->diffInDays($i->expires_at) }} j
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $i->statusColor() }}">
                                        {{ $i->statusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $i->resultColor() }}">
                                        {{ $i->resultLabel() }}
                                    </span>
                                </td>
                                <td class="col-hide-md" style="font-size:.85rem">{{ $i->control_center ?? '—' }}</td>
                                <td class="col-hide-md" style="font-size:.85rem">{{ $i->certificate_number ?? '—' }}</td>
                                <td class="col-hide-sm" style="font-size:.85rem">
                                    {{ $i->cost ? number_format((float)$i->cost, 0, ',', ' ').' F' : '—' }}
                                </td>
                                <td class="col-hide-sm" style="font-size:.82rem;color:var(--text-muted)">
                                    {{ $i->createdBy?->name ?? '—' }}
                                    <br>{{ $i->created_at->format('d/m/Y') }}
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        @if($i->document_path)
                                            <a href="{{ Storage::url($i->document_path) }}"
                                               target="_blank"
                                               class="btn btn-outline-secondary"
                                               title="Voir le document">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </a>
                                        @endif
                                        <button type="button" class="btn btn-outline-primary"
                                                wire:click="openEdit({{ $i->id }})" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger"
                                                wire:click="confirmDelete({{ $i->id }})" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    Aucune visite technique trouvée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($inspections->hasPages())
                    <div class="p-3 border-top">{{ $inspections->links() }}</div>
                @endif
            @endif
        </div>
    </div>

    {{-- Modal formulaire --}}
    @if($showFormModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color:rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-clipboard-check me-2"></i>
                        {{ $editingId ? 'Modifier la visite technique' : 'Nouvelle visite technique' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showFormModal',false)"></button>
                </div>
                <form wire:submit="save">
                    <div class="modal-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Véhicule *</label>
                                <select class="form-select" wire:model="vehicle_id">
                                    <option value="">Sélectionner...</option>
                                    @foreach($vehicles as $v)
                                        <option value="{{ $v->id }}">{{ $v->registration }}</option>
                                    @endforeach
                                </select>
                                @error('vehicle_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Date de visite *</label>
                                <input type="date" class="form-control" wire:model="inspected_at">
                                @error('inspected_at') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Date d'expiration *</label>
                                <input type="date" class="form-control" wire:model="expires_at">
                                @error('expires_at') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Résultat *</label>
                                <select class="form-select" wire:model="result">
                                    <option value="admitted">Admis</option>
                                    <option value="adjourned">Ajourné</option>
                                    <option value="refused">Refusé</option>
                                </select>
                                @error('result') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Centre de contrôle</label>
                                <input type="text" class="form-control" wire:model="control_center"
                                       placeholder="Ex : CAMI, Autovision...">
                                @error('control_center') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">N° Certificat</label>
                                <input type="text" class="form-control" wire:model="certificate_number"
                                       placeholder="Ex : VT-2024-001234">
                                @error('certificate_number') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Coût (FCFA)</label>
                                <input type="text" class="form-control" wire:model="cost"
                                       placeholder="Ex : 35 000">
                                @error('cost') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Document (PDF / image)</label>
                                <input type="file" class="form-control" wire:model="document_file"
                                       accept=".pdf,.jpg,.jpeg,.png">
                                @error('document_file') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea class="form-control" rows="2" wire:model="notes"
                                          placeholder="Observations, points à corriger..."></textarea>
                                @error('notes') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                wire:click="$set('showFormModal',false)">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $editingId ? 'Mettre à jour' : 'Enregistrer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal suppression --}}
    @if($showDeleteModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color:rgba(0,0,0,0.5)">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button type="button" class="btn-close"
                            wire:click="$set('showDeleteModal',false)"></button>
                </div>
                <div class="modal-body">
                    <p>Supprimer cette visite technique ?</p>
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Cette action est irréversible.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            wire:click="$set('showDeleteModal',false)">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>