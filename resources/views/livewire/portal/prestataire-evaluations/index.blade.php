<div>
    <p class="section-label">Évaluations des prestataires — Qualité, délais de livraison, score de réputation</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Évaluations</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 200px;" placeholder="Prestataire, contexte, commentaire..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 160px;" wire:model.live="type_filter">
                        <option value="">Tous types</option>
                        <option value="supplier">Fournisseurs</option>
                        <option value="garage">Garages</option>
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-star me-1"></i> Nouvelle évaluation
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Date</th>
                        <th>Prestataire</th>
                        <th>Type</th>
                        <th class="text-center">Qualité</th>
                        <th class="text-center">Délais</th>
                        <th class="text-center">Réputation</th>
                        <th class="text-center">Note globale</th>
                        <th>Contexte / Commentaire</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluations as $e)
                        <tr>
                            <td>{{ $e->evaluated_at->format('d/m/Y') }}</td>
                            <td>
                                <span class="fw-medium">{{ $e->evaluable?->name ?? '—' }}</span>
                            </td>
                            <td>
                                @if($e->evaluable_type === \App\Models\Supplier::class)
                                    <span class="badge bg-info">Fournisseur</span>
                                @else
                                    <span class="badge bg-secondary">Garage</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary" title="Qualité">{{ number_format($e->quality_score, 1) }}/5</span>
                            </td>
                            <td class="text-center">
                                @if($e->delivery_score !== null)
                                    <span class="badge bg-success" title="Délais livraison">{{ number_format($e->delivery_score, 1) }}/5</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($e->reputation_score !== null)
                                    <span class="badge bg-warning text-dark" title="Réputation">{{ number_format($e->reputation_score, 1) }}/5</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <strong>{{ number_format($e->overall_score, 1) }}/5</strong>
                            </td>
                            <td class="small">
                                @if($e->context)
                                    <span class="text-muted">{{ Str::limit($e->context, 30) }}</span>
                                @endif
                                @if($e->comment)
                                    <br>{{ Str::limit($e->comment, 50) }}
                                @endif
                                @if(!$e->context && !$e->comment)
                                    —
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEdit({{ $e->id }})" title="Modifier"><i class="bi bi-pencil"></i></button>
                                    <button type="button" class="btn btn-outline-danger" wire:click="confirmDelete({{ $e->id }})" title="Supprimer"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-star fs-1 d-block mb-2"></i>
                                Aucune évaluation. Cliquez sur « Nouvelle évaluation » pour noter un fournisseur ou un garage.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-light">{{ $evaluations->links() }}</div>
    </div>

    @if($showFormModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editingId ? 'Modifier l\'évaluation' : 'Nouvelle évaluation' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeFormModal"></button>
                </div>
                <form wire:submit="saveEvaluation">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type de prestataire <span class="text-danger">*</span></label>
                                <select class="form-select @error('evaluable_type') is-invalid @enderror" wire:model.live="evaluable_type">
                                    <option value="Supplier">Fournisseur</option>
                                    <option value="Garage">Garage</option>
                                </select>
                                @error('evaluable_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prestataire <span class="text-danger">*</span></label>
                                <select class="form-select @error('evaluable_id') is-invalid @enderror" wire:model="evaluable_id">
                                    <option value="">Sélectionner...</option>
                                    @if($evaluable_type === 'Supplier')
                                        @foreach($suppliers as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    @else
                                        @foreach($garages as $g)
                                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('evaluable_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Qualité <span class="text-danger">*</span> (1 à 5)</label>
                                <select class="form-select" wire:model="quality_score">
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}">{{ $i }} — {{ $i <= 2 ? 'Insuffisant' : ($i == 3 ? 'Correct' : ($i == 4 ? 'Bien' : 'Très bien')) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Délais de livraison (1 à 5)</label>
                                <select class="form-select" wire:model="delivery_score">
                                    <option value="">— Non renseigné</option>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Score réputation (1 à 5)</label>
                                <select class="form-select" wire:model="reputation_score">
                                    <option value="">— Non renseigné</option>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date d'évaluation <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" wire:model="evaluated_at">
                                @error('evaluated_at') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contexte (ex: Commande #12, Réparation véhicule XX)</label>
                                <input type="text" class="form-control" wire:model="context" placeholder="Optionnel">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Commentaire</label>
                            <textarea class="form-control" rows="3" wire:model="comment" placeholder="Remarques sur la prestation..."></textarea>
                            @error('comment') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeFormModal">Annuler</button>
                        <button type="submit" class="btn btn-primary">{{ $editingId ? 'Enregistrer' : 'Enregistrer l\'évaluation' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if($showDeleteModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer cette évaluation ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteEvaluation">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
