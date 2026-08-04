<div>
    <p class="section-label">Gestion des stocks — Usage interne atelier / réparations</p>
    <div class="alert alert-light border mb-3 py-2 small">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Stock à consommation interne.</strong> Les pièces et consommables sont utilisés en interne pour les réparations, l'entretien et l'atelier. Aucune vente de matériel : entrées (réception fournisseur, inventaire) et sorties (consommation pour réparation, atelier, ajustement).
    </div>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Stocks</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Référence, nom, marque..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="category_filter">
                        <option value="">Toutes catégories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="location_filter">
                        <option value="">Tous emplacements</option>
                        <option value="main">Magasin principal</option>
                        <option value="garage">Magasin garage</option>
                    </select>
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="stock_status">
                        <option value="all">Tous les stocks</option>
                        <option value="low">Stock faible</option>
                        <option value="out">Rupture</option>
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <a href="{{ route('stock.articles') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-box-seam me-1"></i> Gérer les articles
                    </a>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Référence</th>
                        <th>Article</th>
                        <th>Catégorie</th>
                        <th>Emplacement</th>
                        <th>Quantité</th>
                        <th>Réservé</th>
                        <th>Disponible</th>
                        <th>Stock min</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                        <tr>
                            <td>{{ $stock->article->reference }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($stock->article->photo_path)
                                        <img src="{{ asset('storage/' . $stock->article->photo_path) }}" 
                                             alt="{{ $stock->article->name }}" 
                                             class="me-2" 
                                             style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px;">
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $stock->article->name }}</div>
                                        @if($stock->article->brand)
                                            <small class="text-muted">{{ $stock->article->brand }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $stock->article->category->name }}</td>
                            <td>{{ $stock->location_label }}</td>
                            <td>{{ $this->formatQuantity($stock->quantity) }}</td>
                            <td>{{ $this->formatQuantity($stock->reserved_quantity) }}</td>
                            <td class="fw-semibold">{{ $this->formatQuantity($stock->available_quantity) }}</td>
                            <td>{{ $stock->article->min_stock_level }}</td>
                            <td>{!! $getStockStatusLabel($stock) !!}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
                                Aucun article en stock trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light">
            {{ $stocks->links() }}
        </div>
    </div>
</div>
