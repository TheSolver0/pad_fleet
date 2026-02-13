<div>
    <p class="section-label">Bons de travail - Sortie des pièces de rechange et travaux sur véhicule</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Bons de travail</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Véhicule, référence, mécanicien..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="status_filter">
                        <option value="">Tous statuts</option>
                        <option value="pending">En attente</option>
                        <option value="in_progress">En cours</option>
                        <option value="completed">Terminé</option>
                        <option value="validated">Validé</option>
                    </select>
                    <input type="date" class="form-control form-control-sm" style="width: 160px;" wire:model.live="date_filter">
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
                                @if($workOrder->diagnostic)
                                    <small class="text-muted">Diagnostic: {{ $workOrder->diagnostic->reference }}</small>
                                @endif
                            </td>
                            <td>{{ $workOrder->work_date->format('d/m/Y') }}</td>
                            <td>
                                <div>{{ $workOrder->mechanic->last_name }} {{ $workOrder->mechanic->first_name }}</div>
                                @if($workOrder->work_duration)
                                    <small class="text-muted">{{ $workOrder->work_duration }}</small>
                                @endif
                            </td>
                            <td>
                                <div>{{ Str::limit($workOrder->work_description, 50) }}</div>
                                @if(strlen($workOrder->work_description) > 50)
                                    <small class="text-muted">...</small>
                                @endif
                            </td>
                            <td class="fw-semibold text-end">
                                @if($workOrder->total_cost)
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
                                    <button type="button" class="btn btn-outline-info" wire:click="viewWorkOrder({{ $workOrder->id }})" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEdit({{ $workOrder->id }})" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-success" wire:click="validateWorkOrder({{ $workOrder->id }})" title="Valider" wire:loading.attr="validating{{ $workOrder->id }}">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-warning" wire:click="downloadPDF({{ $workOrder->id }})" title="Télécharger PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" wire:click="confirmDelete({{ $workOrder->id }})" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
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
    @if($showFormModal)
    <div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-height: 90vh;">
            <div class="modal-content" style="max-height: 90vh; overflow: hidden;">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editingId ? 'Modifier le bon de travail' : 'Nouveau bon de travail' }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
                </div>
                <form wire:submit="saveWorkOrder">
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
                                <label class="form-label">Diagnostic associé</label>
                                <select class="form-select" wire:model="diagnostic_id">
                                    <option value="">Aucun</option>
                                    @foreach($diagnostics as $d)
                                        <option value="{{ $d->id }}" {{ $d->reference }} - {{ $d->vehicle->registration }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mécanicien <span class="text-danger">*</span></label>
                                <select class="form-select @error('mechanic_id') is-invalid @enderror" wire:model="mechanic_id">
                                    <option value="">—</option>
                                    @foreach($mechanics as $m)
                                        <option value="{{ $m->id }}">{{ $m->last_name }} {{ $m->first_name }}</option>
                                    @endforeach
                                </select>
                                @error('mechanic_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date travail <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('work_date') is-invalid @enderror" wire:model="work_date">
                                @error('work_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Heure début</label>
                                <input type="time" class="form-control" wire:model="start_time" placeholder="08:00">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Heure fin</label>
                                <input type="time" class="form-control" wire:model="end_time" placeholder="17:00">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description des travaux <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('work_description') is-invalid @enderror" rows="3" wire:model="work_description" placeholder="Description détaillée des travaux à effectuer..."></textarea>
                                @error('work_description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Pièces et matériel</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pièces utilisées</label>
                                <textarea class="form-control" rows="3" wire:model="parts_used" placeholder="Liste des pièces utilisées avec quantités..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pièces retirées</label>
                                <textarea class="form-control" rows="3" wire:model="parts_removed" placeholder="Pièces retirées du véhicule..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Matériel utilisé</label>
                                <textarea class="form-control" rows="2" wire:model="equipment_used" placeholder="Outils et matériel utilisés..."></textarea>
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
                                <textarea class="form-control" rows="3" wire:model="problems_found" placeholder="Problèmes supplémentaires découverts..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Solutions appliquées</label>
                                <textarea class="form-control" rows="3" wire:model="solutions_applied" placeholder="Solutions mises en œuvre..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Notes techniques</label>
                                <textarea class="form-control" rows="2" wire:model="technical_notes" placeholder="Notes techniques importantes..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contrôle qualité</label>
                                <textarea class="form-control" rows="2" wire:model="quality_control" placeholder="Vérifications qualité effectuées..."></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Vérifications finales</label>
                                <textarea class="form-control" rows="2" wire:model="final_checks" placeholder="Contrôles finaux avant livraison..."></textarea>
                            </div>
                        </div>

                        <div class="row g-3 mt-4">
                            <div class="col-md-4">
                                <label class="form-label">Coût main d'œuvre (FCFA)</label>
                                <input type="text" class="form-control" wire:model="labor_cost" placeholder="ex: 25000">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Coût pièces (FCFA)</label>
                                <input type="text" class="form-control" wire:model="parts_cost" placeholder="ex: 45000">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Coût total (FCFA)</label>
                                <input type="text" class="form-control bg-light" readonly value="{{ isset($totalCost) ? number_format($totalCost, 2, ',', ' ') : '' }}">
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
                            <div class="col-md-6">
                                <label class="form-label">Notes de fin de travaux</label>
                                <textarea class="form-control" rows="2" wire:model="completion_notes" placeholder="Notes sur l'achèvement des travaux..."></textarea>
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

    <!-- Modal Vue Bon de travail -->
    @if($showViewModal)
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
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Véhicule</label>
                            <div class="form-control bg-light">{{ $workOrder->vehicle->registration }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Mécanicien</label>
                            <div class="form-control bg-light">{{ $workOrder->mechanic->last_name }} {{ $workOrder->mechanic->first_name }}</div>
                        </div>
                        @if($workOrder->diagnostic)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Diagnostic associé</label>
                            <div class="form-control bg-light">{{ $workOrder->diagnostic->reference }}</div>
                        </div>
                        @endif
                        @if($workOrder->start_time && $workOrder->end_time)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Heures</label>
                            <div class="form-control bg-light">{{ $workOrder->start_time->format('H:i') }} - {{ $workOrder->end_time->format('H:i') }}</div>
                        </div>
                        @endif
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label fw-bold">Description des travaux</label>
                            <div class="form-control bg-light" style="min-height: 100px;">{{ $workOrder->work_description }}</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Pièces et matériel</h6>
                        </div>
                        @if($workOrder->parts_used)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pièces utilisées</label>
                            <div class="form-control bg-light" style="min-height: 100px;">{{ $workOrder->parts_used }}</div>
                        </div>
                        @endif
                        @if($workOrder->parts_removed)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pièces retirées</label>
                            <div class="form-control bg-light" style="min-height: 100px;">{{ $workOrder->parts_removed }}</div>
                        </div>
                        @endif
                        @if($workOrder->equipment_used)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Matériel utilisé</label>
                            <div class="form-control bg-light" style="min-height: 80px;">{{ $workOrder->equipment_used }}</div>
                        </div>
                        @endif
                        @if($workOrder->tools_used)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Outils utilisés</label>
                            <div class="form-control bg-light" style="min-height: 80px;">{{ $workOrder->tools_used }}</div>
                        </div>
                        @endif
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Détails techniques</h6>
                        </div>
                        @if($workOrder->problems_found)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Problèmes identifiés</label>
                            <div class="form-control bg-light" style="min-height: 100px;">{{ $workOrder->problems_found }}</div>
                        </div>
                        @endif
                        @if($workOrder->solutions_applied)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Solutions appliquées</label>
                            <div class="form-control bg-light" style="min-height: 100px;">{{ $workOrder->solutions_applied }}</div>
                        </div>
                        @endif
                        @if($workOrder->technical_notes)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Notes techniques</label>
                            <div class="form-control bg-light" style="min-height: 80px;">{{ $workOrder->technical_notes }}</div>
                        </div>
                        @endif
                        @if($workOrder->quality_control)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Contrôle qualité</label>
                            <div class="form-control bg-light" style="min-height: 80px;">{{ $workOrder->quality_control }}</div>
                        </div>
                        @endif
                        @if($workOrder->final_checks)
                        <div class="col-12">
                            <label class="form-label fw-bold">Vérifications finales</label>
                            <div class="form-control bg-light" style="min-height: 80px;">{{ $workOrder->final_checks }}</div>
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
                                @if($workOrder->labor_cost)
                                    {{ number_format($workOrder->labor_cost, 2, ',', ' ') }} FCFA
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Coût pièces</label>
                            <div class="form-control bg-light">
                                @if($workOrder->parts_cost)
                                    {{ number_format($workOrder->parts_cost, 2, ',', ' ') }} FCFA
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Coût total</label>
                            <div class="form-control bg-light fw-bold text-primary">
                                @if($workOrder->total_cost)
                                    {{ number_format($workOrder->total_cost, 2, ',', ' ') }} FCFA
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        @if($workOrder->completion_notes)
                        <div class="col-12">
                            <label class="form-label fw-bold">Notes de fin de travaux</label>
                            <div class="form-control bg-light" style="min-height: 80px;">{{ $workOrder->completion_notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" wire:click="downloadPDF({{ $workOrder->id }})">
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
    <div class="modal show d-block" tabindex="-1" style="background: rgba(00,0,0,0.4);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer ce bon de travail ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteWorkOrder">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
