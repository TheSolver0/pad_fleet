<div>
    <p class="section-label">Cartes grises — Liste et gestion par véhicule</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Cartes grises</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 200px;" placeholder="Réf., véhicule, notes..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 180px;" wire:model.live="vehicle_filter">
                        <option value="">Tous les véhicules</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}">{{ $v->registration }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouvelle carte grise
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Véhicule</th>
                        <th>N° récép. / ID</th>
                        <th>Délivrance</th>
                        <th>Expiration</th>
                        <th>Notes</th>
                        <th>Fichier</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cartesGrises as $cg)
                        <tr wire:key="carte-grise-{{ $cg->id }}">
                            <td><span class="fw-medium">{{ $cg->vehicle->registration ?? '—' }}</span></td>
                            <td>{{ $cg->reference_number ?? '—' }}</td>
                            <td>{{ $cg->issued_at?->format('d/m/Y') ?? '—' }}</td>
                            <td>
                                @if($cg->expires_at)
                                    @if($cg->expires_at->isPast())
                                        <span class="text-danger">{{ $cg->expires_at->format('d/m/Y') }}</span> <span class="badge bg-danger">Expirée</span>
                                    @else
                                        {{ $cg->expires_at->format('d/m/Y') }}
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td class="small">{{ \Str::limit($cg->notes, 40) ?? '—' }}</td>
                            <td>
                                @if($cg->file_path)
                                    <a href="{{ asset('storage/' . $cg->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i></a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEdit({{ $cg->id }})" title="Modifier"><i class="bi bi-pencil"></i></button>
                                    <button type="button" class="btn btn-outline-danger" wire:click="confirmDelete({{ $cg->id }})" title="Supprimer"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucune carte grise. Cliquez sur « Nouvelle carte grise » pour en ajouter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-light">{{ $cartesGrises->links() }}</div>
    </div>

    @if($showFormModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editingId ? 'Modifier la carte grise' : 'Nouvelle carte grise' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeFormModal"></button>
                </div>
                <form wire:submit="saveCarteGrise">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Véhicule <span class="text-danger">*</span></label>
                            <select class="form-select @error('vehicle_id') is-invalid @enderror" wire:model="vehicle_id" {{ $editingId ? 'disabled' : '' }}>
                                <option value="">Sélectionner...</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->registration }}</option>
                                @endforeach
                            </select>
                            @if($editingId)<input type="hidden" wire:model="vehicle_id">@endif
                            @error('vehicle_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">N° récépissé / ID</label>
                            <input type="text" class="form-control" wire:model="reference_number" placeholder="ex. 12345">
                            @error('reference_number') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date délivrance</label>
                                <input type="date" class="form-control" wire:model="issued_at">
                                @error('issued_at') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date expiration</label>
                                <input type="date" class="form-control" wire:model="expires_at">
                                @error('expires_at') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Scan (PDF / image)</label>
                            <input type="file" class="form-control" wire:model="cg_file" accept=".pdf,.jpg,.jpeg,.png">
                            @error('cg_file') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeFormModal">Annuler</button>
                        <button type="submit" class="btn btn-primary">{{ $editingId ? 'Enregistrer' : 'Créer' }}</button>
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
                    <p>Êtes-vous sûr de vouloir supprimer cette carte grise ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteCarteGrise">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
