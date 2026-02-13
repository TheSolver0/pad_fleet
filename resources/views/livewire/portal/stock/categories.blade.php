<div>
    <p class="section-label">Gestion des catégories d'articles</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Catégories</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Nom, code..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouvelle catégorie
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Nom</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Articles</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $category->name }}</div>
                                @if($category->code)
                                    <small class="text-muted">({{ $category->code }})</small>
                                @endif
                            </td>
                            <td>
                                @if($category->code)
                                    <span class="badge bg-light text-dark">{{ $category->code }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($category->description)
                                    <small class="text-muted">{{ Str::limit($category->description, 80) }}</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $category->articles_count }} article{{ $category->articles_count > 1 ? 's' : '' }}</span>
                            </td>
                            <td>
                                @if($category->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEdit({{ $category->id }})" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" wire:click="confirmDelete({{ $category->id }})" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-tags fs-1 d-block mb-2"></i>
                                Aucune catégorie trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light">
            {{ $categories->links() }}
        </div>
    </div>

    <!-- Modal Formulaire Catégorie -->
    @if($showFormModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $editingId ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeFormModal"></button>
                </div>
                <form wire:submit="saveCategory">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" class="form-control" wire:model="name" required>
                            @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" wire:model="code" placeholder="ex: PIECES, PNEUS, OUTILLAGE...">
                            @error('code') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="3" wire:model="description"></textarea>
                            @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="is_active" id="is_active">
                                <label class="form-check-label" for="is_active">
                                    Catégorie active
                                </label>
                            </div>
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
                    <p>Êtes-vous sûr de vouloir supprimer cette catégorie ?</p>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Si des articles sont associés à cette catégorie, la suppression sera impossible.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeFormModal">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteCategory">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
