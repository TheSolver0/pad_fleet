<div>
    <p class="section-label">Gestion des congés et absences des chauffeurs</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Congés chauffeurs</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width:200px"
                        placeholder="Nom du chauffeur..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width:140px" wire:model.live="status_filter">
                        <option value="">Tous statuts</option>
                        <option value="pending">En attente</option>
                        <option value="approved">Approuvé</option>
                        <option value="rejected">Refusé</option>
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i>Nouveau congé
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Chauffeur</th>
                        <th>Type</th>
                        <th>Du</th>
                        <th>Au</th>
                        <th class="text-center">Jours</th>
                        <th>Raison</th>
                        <th>Remplaçant</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                    <tr @if($leave->isActive()) style="background:rgba(201,107,107,.06)" @endif>
                        <td class="fw-semibold">
                            {{ $leave->driver?->full_name ?? '—' }}
                            @if($leave->isActive())
                                <span class="badge bg-danger ms-1" style="font-size:.65rem">En congé</span>
                            @endif
                        </td>
                        <td><span class="badge bg-secondary bg-opacity-50 text-dark">{{ $leave->type_label }}</span></td>
                        <td>{{ $leave->start_date->format('d/m/Y') }}</td>
                        <td>{{ $leave->end_date->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $leave->nb_jours ?? '—' }}</td>
                        <td><small>{{ Str::limit($leave->reason, 40) ?? '—' }}</small></td>
                        <td>
                            @if($leave->replacementDriver)
                                <small class="text-success"><i class="bi bi-person-check me-1"></i>{{ $leave->replacementDriver->full_name }}</small>
                            @else
                                <small class="text-muted">—</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $leave->status_color }}">{{ $leave->status_label }}</span>
                        </td>
                        <td class="text-end">
                            @if($leave->document_path)
                                <a href="{{ asset('storage/' . $leave->document_path) }}" target="_blank"
                                    class="btn btn-sm btn-outline-info" title="Justificatif">
                                    <i class="bi bi-paperclip"></i>
                                </a>
                            @endif
                            @if($leave->status === 'pending')
                                <button class="btn btn-sm btn-outline-success" wire:click="approve({{ $leave->id }})" title="Approuver">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" wire:click="reject({{ $leave->id }})" title="Refuser">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            @endif
                            <button class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $leave->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $leave->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">Aucun congé enregistré.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leaves->hasPages())
            <div class="p-3 border-top">{{ $leaves->links() }}</div>
        @endif
    </div>

    {{-- MODAL FORMULAIRE --}}
    @if($showFormModal)
    <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.45);">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-x me-2"></i>
                        {{ $editingId ? 'Modifier le congé' : 'Enregistrer un congé / absence' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
                </div>
                <form wire:submit="saveLeave" novalidate>
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
                                    @foreach($drivers as $d)
                                    <option value="{{ $d->id }}">{{ $d->last_name }} {{ $d->first_name }}</option>
                                    @endforeach
                                </select>
                                @error('driver_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Type de congé <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="type">
                                    @foreach($types as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" wire:model="start_date">
                                @error('start_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date de fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" wire:model="end_date">
                                @error('end_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Chauffeur remplaçant</label>
                                <select class="form-select" wire:model="replacement_driver_id">
                                    <option value="">— Aucun —</option>
                                    @foreach($drivers as $d)
                                    @if($d->id != $driver_id)
                                    <option value="{{ $d->id }}">{{ $d->last_name }} {{ $d->first_name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Raison / Motif</label>
                                <input type="text" class="form-control" wire:model="reason" placeholder="ex: Congé de maternité, Obsèques…" maxlength="500">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Justificatif (PDF / image)</label>
                                <input type="file" class="form-control" wire:model="justificatif" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text">Attestation médicale, demande signée, etc.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes internes</label>
                                <textarea class="form-control" rows="2" wire:model="notes"></textarea>
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
                <div class="modal-body">Supprimer ce congé ?</div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" wire:click="$set('showDeleteModal', false)">Annuler</button>
                    <button class="btn btn-danger" wire:click="deleteLeave">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
