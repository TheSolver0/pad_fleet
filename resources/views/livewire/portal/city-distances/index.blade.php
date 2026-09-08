<div>
    <p class="section-label">Distances entre villes — utilisées pour l'estimation automatique des missions</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Distances</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Ville..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouvelle distance
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Ville de départ</th>
                        <th>Ville de destination</th>
                        <th class="text-center">Distance (km)</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($distances as $d)
                        <tr>
                            <td>{{ $d->fromCity?->name ?? '—' }}</td>
                            <td>{{ $d->toCity?->name ?? '—' }}</td>
                            <td class="text-center fw-medium">{{ number_format($d->distance_km) }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $d->id }})"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $d->id }})"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Aucune distance enregistrée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($distances->hasPages())
            <div class="p-3 border-top">{{ $distances->links() }}</div>
        @endif
    </div>

    @if($showFormModal)
    <div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editingId ? 'Modifier la distance' : 'Nouvelle distance' }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
                </div>
                <form wire:submit="saveDistance">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Ville de départ <span class="text-danger">*</span></label>
                            <select class="form-select @error('from_city_id') is-invalid @enderror" wire:model="from_city_id">
                                <option value="">—</option>
                                @foreach($cities as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('from_city_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ville de destination <span class="text-danger">*</span></label>
                            <select class="form-select @error('to_city_id') is-invalid @enderror" wire:model="to_city_id">
                                <option value="">—</option>
                                @foreach($cities as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('to_city_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Distance (km) <span class="text-danger">*</span></label>
                            <input type="number" min="1" class="form-control @error('distance_km') is-invalid @enderror" wire:model="distance_km">
                            @error('distance_km') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <p class="small text-muted mb-0">La distance est symétrique : elle sera utilisée automatiquement dans les deux sens.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" wire:click="$set('showFormModal', false)">Annuler</button>
                        <button type="submit" class="btn btn-primary">{{ $editingId ? 'Enregistrer' : 'Créer' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if($showDeleteModal)
    <div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                </div>
                <div class="modal-body">Voulez-vous vraiment supprimer cette distance ?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" wire:click="$set('showDeleteModal', false)">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteDistance">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
