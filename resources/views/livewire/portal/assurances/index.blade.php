<div>
    <p class="section-label">Assurances globales — Marchés par lots, alertes échéances, upload contrats</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Marchés assurance</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 200px;" placeholder="Nom, assureur..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouveau marché
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Nom</th>
                        <th>Assureur</th>
                        <th>Lot</th>
                        <th>Période</th>
                        <th>Prime optionnelle</th>
                        <th>Alerte</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $c)
                        <tr>
                            <td><span class="fw-medium">{{ $c->name }}</span></td>
                            <td>{{ $c->assureur?->name ?? $c->insurer }}</td>
                            <td class="small">{{ Str::limit($c->lot_description, 30) ?? '—' }}</td>
                            <td class="small">{{ $c->start_date->format('d/m/Y') }} → {{ $c->end_date->format('d/m/Y') }}</td>
                            <td>{{ $c->optional_prime ? format_money($c->optional_prime, 0) . ' F' : '—' }}</td>
                            <td>
                                @if($c->isExpired())
                                    <span class="badge bg-danger">Expiré</span>
                                @elseif($c->isExpiringSoon(30))
                                    <span class="badge bg-warning text-dark">Expire bientôt</span>
                                @else
                                    <span class="badge bg-success">OK</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $c->id }})"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="openDocModal({{ $c->id }})"><i class="bi bi-file-earmark"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $c->id }})"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucun marché.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($contracts->hasPages())
            <div class="p-3 border-top">{{ $contracts->links() }}</div>
        @endif
    </div>

    <!-- Modal Formulaire Assurance -->
    @if($showFormModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $editingId ? 'Modifier le marché d\'assurance' : 'Nouveau marché d\'assurance' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeFormModal"></button>
                </div>
                <form wire:submit="saveContract">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nom du marché *</label>
                                    <input type="text" class="form-control" wire:model="name" required>
                                    @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Assureur *</label>
                                    <select class="form-select" wire:model="assureur_id" required>
                                        <option value="">Sélectionner un assureur...</option>
                                        @foreach($assureurs as $assureur)
                                            <option value="{{ $assureur->id }}">{{ $assureur->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('assureur_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Assureur (texte libre)</label>
                                    <input type="text" class="form-control" wire:model="insurer" placeholder="Nom de l'assureur si non listé">
                                    @error('insurer') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Prime optionnelle</label>
                                    <input type="text" class="form-control" wire:model="optional_prime" placeholder="ex: 5 000 000">
                                    @error('optional_prime') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date de début *</label>
                                    <input type="date" class="form-control" wire:model="start_date" required>
                                    @error('start_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date de fin *</label>
                                    <input type="date" class="form-control" wire:model="end_date" required>
                                    @error('end_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description du lot</label>
                            <textarea class="form-control" rows="2" wire:model="lot_description"></textarea>
                            @error('lot_description') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                            @error('notes') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeFormModal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $editingId ? 'Mettre à jour' : 'Créer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Documents -->
    @if($showDocModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Documents du contrat</h5>
                    <button type="button" class="btn-close" wire:click="closeDocModal"></button>
                </div>
                <div class="modal-body">
                    @if($docContract)
                        <div class="mb-3">
                            <label class="form-label">Uploader un document</label>
                            <input type="file" class="form-control" wire:model="contract_file">
                            @error('contract_file') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <button type="button" class="btn btn-primary mb-3" wire:click="uploadDocument">
                            <i class="bi bi-upload me-1"></i> Uploader
                        </button>
                        
                        <h6>Documents existants</h6>
                        @if($docContract->documents->count() > 0)
                            <div class="list-group">
                                @foreach($docContract->documents as $doc)
                                    <div wire:key="assurance-doc-{{ $doc->id }}" class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-file-earmark-text me-2"></i>
                                            {{ $doc->original_name }}
                                            <br><small class="text-muted">{{ $doc->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Aucun document</p>
                        @endif
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeDocModal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Suppression -->
    @if($showDeleteModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" wire:click="closeFormModal"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer ce marché d'assurance ?</p>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Cette action est irréversible.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeFormModal">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteContract">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
