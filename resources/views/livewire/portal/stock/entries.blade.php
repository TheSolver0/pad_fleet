<div>
    <p class="section-label">Gestion des entrées de stock</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Entrées de stock</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Référence, article, fournisseur..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="entry_type">
                        <option value="direct">Achat direct</option>
                        <option value="purchase_order">Bon de commande</option>
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouvelle entrée
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Date</th>
                        <th>Référence</th>
                        <th>Article</th>
                        <th>Quantité</th>
                        <th>Emplacement</th>
                        <th>Fournisseur</th>
                        <th>Type</th>
                        <th>Utilisateur</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                        <tr>
                            <td>{{ $entry->created_at->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-info">{{ $entry->reference }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $entry->article->name }}</div>
                                <small class="text-muted">{{ $entry->article->reference }}</small>
                            </td>
                            <td class="fw-semibold text-success">+{{ $entry->quantity }}</td>
                            <td>
                                <span class="badge bg-{{ $entry->location === 'main' ? 'primary' : 'secondary' }}">
                                    {{ $entry->location === 'main' ? 'Magasin principal' : 'Magasin garage' }}
                                </span>
                            </td>
                            <td>
                                @if($entry->supplier)
                                    {{ $entry->supplier->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($entry->purchase_order)
                                    <span class="badge bg-warning">Bon de commande</span>
                                @else
                                    <span class="badge bg-success">Achat direct</span>
                                @endif
                            </td>
                            <td>{{ $entry->user->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-box-arrow-in-down fs-1 d-block mb-2"></i>
                                Aucune entrée de stock trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light">
            {{ $entries->links() }}
        </div>
    </div>

    <!-- Modal Formulaire Entrée -->
    @if($showFormModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nouvelle entrée de stock</h5>
                    <button type="button" class="btn-close" wire:click="closeFormModal"></button>
                </div>
                <form wire:submit="saveEntry">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Type d'entrée *</label>
                                <select class="form-select" wire:model.live="entry_type">
                                    <option value="direct">Achat direct (garage)</option>
                                    <option value="purchase_order">Bon de commande (magasin)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Emplacement *</label>
                                <select class="form-select" wire:model="location">
                                    <option value="main">Magasin principal</option>
                                    <option value="garage">Magasin garage</option>
                                </select>
                            </div>
                        </div>

                        @if($entry_type === 'direct')
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label">Article *</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" wire:model.live="create_article" id="create_article">
                                                <label class="form-check-label" for="create_article">
                                                    Créer un nouvel article
                                                </label>
                                            </div>
                                        </div>
                                        
                                        @if(!$create_article)
                                            <select class="form-select" wire:model="article_id">
                                                <option value="">Sélectionner...</option>
                                                @foreach($articles as $article)
                                                    <option value="{{ $article->id }}">{{ $article->name }} ({{ $article->reference }})</option>
                                                @endforeach
                                            </select>
                                            @error('article_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                        @else
                                            <div class="border rounded p-3 bg-light">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-2">
                                                            <label class="form-label small">Nom de l'article *</label>
                                                            <input type="text" class="form-control form-control-sm" wire:model="new_article_name" placeholder="ex: Plaquette frein AV">
                                                            @error('new_article_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-2">
                                                            <label class="form-label small">Référence</label>
                                                            <input type="text" class="form-control form-control-sm" wire:model="new_article_reference" placeholder="ex: PF-001">
                                                            @error('new_article_reference') <span class="text-danger small">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="mb-2">
                                                            <label class="form-label small">Catégorie *</label>
                                                            <select class="form-select form-select-sm" wire:model="new_article_category_id">
                                                                <option value="">Sélectionner...</option>
                                                                @foreach($categories as $category)
                                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('new_article_category_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-2">
                                                            <label class="form-label small">Marque</label>
                                                            <input type="text" class="form-control form-control-sm" wire:model="new_article_brand" placeholder="ex: BOSCH">
                                                            @error('new_article_brand') <span class="text-danger small">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-2">
                                                            <label class="form-label small">Unité</label>
                                                            <input type="text" class="form-control form-control-sm" wire:model="new_article_unit" placeholder="ex: unité">
                                                            @error('new_article_unit') <span class="text-danger small">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Quantité *</label>
                                        <input type="number" class="form-control" wire:model="quantity" min="1">
                                        @error('quantity') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Référence *</label>
                                        <input type="text" class="form-control" wire:model="reference" placeholder="ex: FACT-2024-001">
                                        @error('reference') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Fournisseur</label>
                                        <select class="form-select" wire:model="supplier_id">
                                            <option value="">Sélectionner...</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model.live="create_supplier" id="create_supplier">
                                    <label class="form-check-label" for="create_supplier">
                                        Créer un nouveau fournisseur
                                    </label>
                                </div>
                            </div>

                            @if($create_supplier)
                                <div class="border rounded p-3 mb-3 bg-light">
                                    <h6 class="mb-3">Nouveau fournisseur</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Nom *</label>
                                                <input type="text" class="form-control" wire:model="supplier_name">
                                                @error('supplier_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Téléphone</label>
                                                <input type="tel" class="form-control" wire:model="supplier_phone">
                                                @error('supplier_phone') <span class="text-danger small">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" wire:model="supplier_email">
                                        @error('supplier_email') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @endif

                        @else
                            <div class="mb-3">
                                <label class="form-label">Bon de commande *</label>
                                <select class="form-select" wire:model="purchase_order_id">
                                    <option value="">Sélectionner...</option>
                                    @foreach($purchaseOrders as $po)
                                        <option value="{{ $po->id }}">{{ $po->reference }} - {{ $po->supplier->name }}</option>
                                    @endforeach
                                </select>
                                        @error('purchase_order_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                            @error('notes') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeFormModal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            Enregistrer l'entrée
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
