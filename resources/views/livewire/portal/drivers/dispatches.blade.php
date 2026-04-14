<div>
    <p class="section-label">Mise à disposition des chauffeurs auprès des directions et directeurs</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Mises à disposition</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width:180px"
                        placeholder="Nom du chauffeur..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width:140px" wire:model.live="status_filter">
                        <option value="">Tous</option>
                        <option value="active">Actives</option>
                        <option value="ended">Terminées</option>
                        <option value="suspended">Suspendues</option>
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-person-plus me-1"></i>Nouvelle mise à disposition
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Chauffeur</th>
                        <th>Mis à disposition de</th>
                        <th>Direction / Agent</th>
                        <th>Début</th>
                        <th>Fin prévue</th>
                        <th>Notes</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dispatches as $d)
                    <tr>
                        <td class="fw-semibold">{{ $d->driver?->full_name ?? '—' }}</td>
                        <td>
                            @if($d->type === 'direction')
                                <span class="badge bg-primary bg-opacity-10 text-primary">Direction</span>
                            @else
                                <span class="badge bg-info bg-opacity-10 text-info">Agent / Directeur</span>
                            @endif
                        </td>
                        <td>{{ $d->assigned_to_label }}</td>
                        <td>{{ $d->started_at?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ $d->ended_at?->format('d/m/Y') ?? '<span class="text-muted small">Indéfinie</span>' }}</td>
                        <td><small class="text-muted">{{ Str::limit($d->notes, 40) }}</small></td>
                        <td>
                            <span class="badge bg-{{ $d->statusColor() }}">{{ $d->statusLabel() }}</span>
                        </td>
                        <td class="text-end">
                            @if($d->status === 'active')
                                <button class="btn btn-sm btn-outline-warning" wire:click="endDispatch({{ $d->id }})" title="Clôturer">
                                    <i class="bi bi-stop-circle"></i>
                                </button>
                            @endif
                            <button class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $d->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $d->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Aucune mise à disposition.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dispatches->hasPages())
            <div class="p-3 border-top">{{ $dispatches->links() }}</div>
        @endif
    </div>

    {{-- MODAL FORMULAIRE --}}
    @if($showFormModal)
    <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.45);">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-gear me-2"></i>
                        {{ $editingId ? 'Modifier la mise à disposition' : 'Nouvelle mise à disposition' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
                </div>
                <form wire:submit="saveDispatch" novalidate>
                    <div class="modal-body">
                        @if($errors->any())
                        <div class="alert alert-danger"><ul class="mb-0">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul></div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Chauffeur <span class="text-danger">*</span></label>
                                <select class="form-select @error('driver_id') is-invalid @enderror" wire:model="driver_id">
                                    <option value="">—</option>
                                    @foreach($drivers as $dr)
                                    <option value="{{ $dr->id }}">{{ $dr->last_name }} {{ $dr->first_name }}</option>
                                    @endforeach
                                </select>
                                @error('driver_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mis à disposition de</label>
                                <div class="btn-group w-100">
                                    <button type="button" class="btn {{ $type === 'direction' ? 'btn-primary' : 'btn-outline-primary' }}"
                                        wire:click="$set('type', 'direction')">
                                        <i class="bi bi-building me-1"></i>Une Direction
                                    </button>
                                    <button type="button" class="btn {{ $type === 'person' ? 'btn-primary' : 'btn-outline-primary' }}"
                                        wire:click="$set('type', 'person')">
                                        <i class="bi bi-person me-1"></i>Un Agent / Directeur
                                    </button>
                                </div>
                            </div>

                            @if($type === 'direction')
                            <div class="col-12">
                                <label class="form-label">Direction <span class="text-danger">*</span></label>
                                <select class="form-select @error('direction_id') is-invalid @enderror" wire:model="direction_id">
                                    <option value="">—</option>
                                    @foreach($directions as $dir)
                                    <option value="{{ $dir->id }}">{{ $dir->name }}</option>
                                    @endforeach
                                </select>
                                @error('direction_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            @else
                            <div class="col-12">
                                <label class="form-label">Agent / Directeur <span class="text-danger">*</span></label>
                                <select class="form-select @error('person_id') is-invalid @enderror" wire:model="person_id">
                                    <option value="">—</option>
                                    @foreach($persons as $p)
                                    <option value="{{ $p->id }}">{{ $p->last_name }} {{ $p->first_name }}</option>
                                    @endforeach
                                </select>
                                @error('person_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            @endif

                            <div class="col-md-6">
                                <label class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('started_at') is-invalid @enderror" wire:model="started_at">
                                @error('started_at') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date de fin (optionnel)</label>
                                <input type="date" class="form-control" wire:model="ended_at">
                                <div class="form-text">Laisser vide si indéfinie</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" rows="2" wire:model="notes"
                                    placeholder="ex: Mis à disposition du Directeur Général pour ses déplacements officiels"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" wire:click="$set('showFormModal', false)">Annuler</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $editingId ? 'Enregistrer' : 'Créer' }}</span>
                            <span wire:loading><i class="bi bi-hourglass-split me-1"></i>Traitement…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL SUPPRESSION --}}
    @if($showDeleteModal)
    <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.45);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Confirmer la suppression</h5></div>
                <div class="modal-body">Supprimer cette mise à disposition ?</div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" wire:click="$set('showDeleteModal', false)">Annuler</button>
                    <button class="btn btn-danger" wire:click="deleteDispatch">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
