<div>
    <p class="section-label">Diagnostics des véhicules - Fiche de pré-diagnostic digitalisée</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Diagnostics</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Véhicule, référence, utilisateur..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="status_filter">
                        <option value="">Tous statuts</option>
                        <option value="pending">En attente</option>
                        <option value="in_progress">En cours</option>
                        <option value="treated">Traité</option>
                    </select>
                    <input type="date" class="form-control form-control-sm" style="width: 160px;" wire:model.live="date_filter">
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouveau diagnostic
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
                        <th>Utilisateur</th>
                        <th>Km arrivée</th>
                        <th>Mécanicien</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($diagnostics as $diagnostic)
                        <tr>
                            <td><span class="badge bg-info">{{ $diagnostic->reference }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $diagnostic->vehicle->registration }}</div>
                            </td>
                            <td>{{ $diagnostic->diagnostic_date->format('d/m/Y') }}</td>
                            <td>
                                <div>{{ $diagnostic->user_name }}</div>
                                <small class="text-muted">{{ $diagnostic->user_role }}</small>
                            </td>
                            <td class="fw-semibold">{{ number_format($diagnostic->km_arrival, 0, '.', ' ') }}</td>
                            <td>
                                @if($diagnostic->mechanic)
                                    <div>{{ $diagnostic->mechanic->last_name }} {{ $diagnostic->mechanic->first_name }}</div>
                                @else
                                    <span class="text-muted">Non assigné</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $diagnostic->status_color }}">
                                    {{ $diagnostic->status }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-info" wire:click="viewDiagnostic({{ $diagnostic->id }})" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEdit({{ $diagnostic->id }})" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-success" wire:click="createWorkOrder({{ $diagnostic->id }})" title="Créer bon de travail">
                                        <i class="bi bi-hammer"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-warning" wire:click="downloadPDF({{ $diagnostic->id }})" title="Télécharger PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" wire:click="confirmDelete({{ $diagnostic->id }})" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-clipboard-check fs-1 d-block mb-2"></i>
                                Aucun diagnostic trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light">
            {{ $diagnostics->links() }}
        </div>
    </div>

    <!-- Modal Formulaire Diagnostic -->
    @if($showFormModal)
    <div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-height: 90vh;">
            <div class="modal-content" style="max-height: 90vh; overflow: hidden;">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editingId ? 'Modifier le diagnostic' : 'Nouveau diagnostic' }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
                </div>
                <form wire:submit="saveDiagnostic">
                    <div class="modal-body" style="max-height: calc(90vh - 120px); overflow-y: auto;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Véhicule <span class="text-danger">*</span></label>
                                <select class="form-select @error('vehicle_id') is-invalid @enderror" wire:model="vehicle_id">
                                    <option value="">—</option>
                                    @foreach($vehicles as $v)
                                        <option value="{{ $v->id }}">{{ $v->registration }}</option>
                                    @endforeach
                                </select>
                                @error('vehicle_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mécanicien</label>
                                <select class="form-select" wire:model="mechanic_id">
                                    <option value="">Non assigné</option>
                                    @foreach($mechanics as $m)
                                        <option value="{{ $m->id }}">{{ $m->last_name }} {{ $m->first_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date diagnostic <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('diagnostic_date') is-invalid @enderror" wire:model="diagnostic_date">
                                @error('diagnostic_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Utilisateur <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('user_name') is-invalid @enderror" wire:model="user_name" placeholder="Nom complet">
                                @error('user_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fonction <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('user_role') is-invalid @enderror" wire:model="user_role" placeholder="ex: Chauffeur">
                                @error('user_role') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Km arrivée <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('km_arrival') is-invalid @enderror" wire:model="km_arrival" min="0">
                                @error('km_arrival') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Observations</label>
                                <textarea class="form-control" rows="2" wire:model="observations" placeholder="Observations générales..."></textarea>
                            </div>
                        </div>

                        <div class="row g-3 mt-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Constats par catégorie</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Moteur</label>
                                <textarea class="form-control" rows="2" wire:model="engine_issues" placeholder="Problèmes moteur identifiés..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Suspension / Transmission</label>
                                <textarea class="form-control" rows="2" wire:model="suspension_transmission" placeholder="Problèmes suspension/transmission..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Freinage</label>
                                <textarea class="form-control" rows="2" wire:model="braking_system" placeholder="Problèmes freinage..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Électronique / Électricité</label>
                                <textarea class="form-control" rows="2" wire:model="electronics_electricity" placeholder="Problèmes électronique/électricité..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Carrosserie / Peinture</label>
                                <textarea class="form-control" rows="2" wire:model="bodywork_paint" placeholder="Problèmes carrosserie/peinture..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Climatisation</label>
                                <textarea class="form-control" rows="2" wire:model="air_conditioning" placeholder="Problèmes climatisation..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Autres</label>
                                <textarea class="form-control" rows="2" wire:model="other_issues" placeholder="Autres problèmes..."></textarea>
                            </div>
                        </div>

                        <div class="row g-3 mt-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Travaux recommandés</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Travaux en interne</label>
                                <textarea class="form-control" rows="3" wire:model="internal_works" placeholder="Travaux à effectuer en interne..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Travaux en externe</label>
                                <textarea class="form-control" rows="3" wire:model="external_works" placeholder="Travaux à effectuer en externe..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Conclusion</label>
                                <textarea class="form-control" rows="2" wire:model="conclusion" placeholder="Conclusion du diagnostic..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showFormModal', false)">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Vue Diagnostic -->
    @if($showViewModal)
    <div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-height: 90vh;">
            <div class="modal-content" style="max-height: 90vh; overflow: hidden;">
                <div class="modal-header">
                    <h5 class="modal-title">Voir le diagnostic</h5>
                    <button type="button" class="btn-close" wire:click="$set('showViewModal', false)"></button>
                </div>
                <div class="modal-body" style="max-height: calc(90vh - 120px); overflow-y: auto;">
                    @php
                        $diagnostic = \App\Models\Diagnostic::find($editingId);
                    @endphp
                    
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Référence:</strong> {{ $diagnostic->reference }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Date:</strong> {{ $diagnostic->diagnostic_date->format('d/m/Y') }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Km arrivée:</strong> {{ number_format($diagnostic->km_arrival, 0, '.', ' ') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Véhicule</label>
                            <div class="form-control bg-light">{{ $diagnostic->vehicle->registration }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Mécanicien</label>
                            <div class="form-control bg-light">
                                @if($diagnostic->mechanic)
                                    {{ $diagnostic->mechanic->last_name }} {{ $diagnostic->mechanic->first_name }}
                                @else
                                    Non assigné
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Utilisateur</label>
                            <div class="form-control bg-light">{{ $diagnostic->user_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fonction</label>
                            <div class="form-control bg-light">{{ $diagnostic->user_role }}</div>
                        </div>
                        @if($diagnostic->observations)
                        <div class="col-12">
                            <label class="form-label fw-bold">Observations</label>
                            <div class="form-control bg-light">{{ $diagnostic->observations }}</div>
                        </div>
                        @endif
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Constats par catégorie</h6>
                        </div>
                        @if($diagnostic->engine_issues)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Moteur</label>
                            <div class="form-control bg-light" style="min-height: 80px;">{{ $diagnostic->engine_issues }}</div>
                        </div>
                        @endif
                        @if($diagnostic->suspension_transmission)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Suspension / Transmission</label>
                            <div class="form-control bg-light" style="min-height: 80px;">{{ $diagnostic->suspension_transmission }}</div>
                        </div>
                        @endif
                        @if($diagnostic->braking_system)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Freinage</label>
                            <div class="form-control bg-light" style="min-height: 80px;">{{ $diagnostic->braking_system }}</div>
                        </div>
                        @endif
                        @if($diagnostic->electronics_electricity)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Électronique / Électricité</label>
                            <div class="form-control bg-light" style="min-height: 80px;">{{ $diagnostic->electronics_electricity }}</div>
                        </div>
                        @endif
                        @if($diagnostic->bodywork_paint)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Carrosserie / Peinture</label>
                            <div class="form-control bg-light" style="min-height: 80px;">{{ $diagnostic->bodywork_paint }}</div>
                        </div>
                        @endif
                        @if($diagnostic->air_conditioning)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Climatisation</label>
                            <div class="form-control bg-light" style="min-height: 80px;">{{ $diagnostic->air_conditioning }}</div>
                        </div>
                        @endif
                        @if($diagnostic->other_issues)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Autres</label>
                            <div class="form-control bg-light" style="min-height: 80px;">{{ $diagnostic->other_issues }}</div>
                        </div>
                        @endif
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Travaux recommandés</h6>
                        </div>
                        @if($diagnostic->internal_works)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Travaux en interne</label>
                            <div class="form-control bg-light" style="min-height: 100px;">{{ $diagnostic->internal_works }}</div>
                        </div>
                        @endif
                        @if($diagnostic->external_works)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Travaux en externe</label>
                            <div class="form-control bg-light" style="min-height: 100px;">{{ $diagnostic->external_works }}</div>
                        </div>
                        @endif
                        @if($diagnostic->conclusion)
                        <div class="col-12">
                            <label class="form-label fw-bold">Conclusion</label>
                            <div class="form-control bg-light" style="min-height: 80px;">{{ $diagnostic->conclusion }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" wire:click="downloadPDF({{ $diagnostic->id }})">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Télécharger PDF
                    </button>
                    <button type="button" class="btn btn-secondary" wire:click="$set('showViewModal', false)">Fermer</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Suppression -->
    @if($showDeleteModal)
    <div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer ce diagnostic ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteDiagnostic">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
