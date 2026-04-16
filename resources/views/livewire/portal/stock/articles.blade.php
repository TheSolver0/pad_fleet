<div>
    <p class="section-label">Gestion des articles</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Articles</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Référence, nom, marque..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="category_filter">
                        <option value="">Toutes catégories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouvel article
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Référence</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Marque/Modèle</th>
                        <th>Stock total</th>
                        <th>Prix unitaire</th>
                        <th>Stock min</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                        <tr>
                            <td class="font-monospace">{{ Str::upper($article->reference) }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($article->photo_path)
                                        <img src="{{ asset('storage/' . $article->photo_path) }}" 
                                             alt="{{ $article->name }}" 
                                             class="me-2" 
                                             style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px;">
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $article->name }}</div>
                                        @if($article->description)
                                            <small class="text-muted">{{ Str::limit($article->description, 50) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $article->category->name }}</td>
                            <td>
                                @if($article->brand)
                                    <div>{{ $article->brand }}</div>
                                @endif
                                @if($article->model)
                                    <small class="text-muted">{{ $article->model }}</small>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $article->total_stock }}</td>
                            <td class="text-end">
                                @if($article->purchase_price)
                                    {{ number_format($article->purchase_price, 2, ',', ' ') }} FCFA
                                @endif
                            </td>
                            <td>{{ $article->min_stock_level }}</td>
                            <td>
                                @if($article->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEdit({{ $article->id }})" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" wire:click="confirmDelete({{ $article->id }})" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
                                Aucun article trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light">
            {{ $articles->links() }}
        </div>
    </div>

    <!-- Modal Formulaire Article -->
    @if($showFormModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $editingId ? 'Modifier l\'article' : 'Nouvel article' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeFormModal"></button>
                </div>
                <form wire:submit="saveArticle">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Référence *</label>
                                    <input type="text" class="form-control" wire:model="reference" required>
                                    @error('reference') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Catégorie *</label>
                                    <select class="form-select" wire:model="article_category_id" required>
                                        <option value="">Sélectionner...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('article_category_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" class="form-control" wire:model="name" required>
                                    @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Unité</label>
                                    <input type="text" class="form-control" wire:model="unit">
                                    @error('unit') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="2" wire:model="description"></textarea>
                            @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Marque</label>
                                    <input type="text" class="form-control" wire:model="brand">
                                    @error('brand') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Modèle</label>
                                    <input type="text" class="form-control" wire:model="model">
                                    @error('model') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Prix unitaire (FCFA)</label>
                                    <input type="text" class="form-control" wire:model="purchase_price" placeholder="0,00">
                                    @error('purchase_price') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Stock minimum</label>
                                    <input type="number" class="form-control" wire:model="min_stock_level" placeholder="1">
                                    @error('min_stock_level') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Stock maximum</label>
                                    <input type="number" class="form-control" wire:model="max_stock_level" min="0">
                                    @error('max_stock_level') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Taille de pneu (si applicable)</label>
                                    <input type="text" class="form-control" wire:model="tire_size" placeholder="ex: 205/55R16">
                                    @error('tire_size') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Photo</label>
                                    <input type="file" class="form-control" wire:model="photo_file" accept="image/*">
                                    @error('photo_file') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catégories de véhicules compatibles</label>
                            <div class="row">
                                @foreach(\App\Models\Vehicle::categoryOptions() as $value => $label)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   value="{{ $value }}" 
                                                   wire:model="compatible_vehicle_categories"
                                                   id="cat_{{ $value }}">
                                            <label class="form-check-label" for="cat_{{ $value }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('compatible_vehicle_categories') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="is_active" id="is_active">
                                <label class="form-check-label" for="is_active">
                                    Article actif
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
                    <p>Êtes-vous sûr de vouloir supprimer cet article ? Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeFormModal">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteArticle">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
