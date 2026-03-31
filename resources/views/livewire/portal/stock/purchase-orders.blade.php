<div>
    <p class="section-label">Bons de commande — Créer, approuver, réceptionner en entrée de stock</p>
    <p class="small text-muted mb-3">Créez un bon de commande (fournisseur + lignes d'articles). Une fois approuvé, vous pourrez le réceptionner dans <a href="{{ route('stock.entries') }}">Entrées de stock</a> (option « Bon de commande »).</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Bons de commande</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 200px;" placeholder="Réf., fournisseur..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="status_filter">
                        <option value="">Tous</option>
                        <option value="pending">En attente</option>
                        <option value="approved">Approuvé</option>
                        <option value="received">Reçu</option>
                        <option value="cancelled">Annulé</option>
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouveau bon de commande
                    </button>
                    <a href="{{ route('stock.entries') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-box-arrow-in-down me-1"></i> Entrées de stock
                    </a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Référence</th>
                        <th>Fournisseur</th>
                        <th>Date commande</th>
                        <th>Livraison prévue</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $po)
                        <tr>
                            <td><span class="fw-medium">{{ $po->reference }}</span></td>
                            <td>{{ $po->supplier->name ?? '—' }}</td>
                            <td>{{ $po->order_date->format('d/m/Y') }}</td>
                            <td>{{ $po->expected_delivery_date?->format('d/m/Y') ?? '—' }}</td>
                            <td>{{ $po->total_amount ? number_format($po->total_amount, 0, ',', ' ') . ' F' : '—' }}</td>
                            <td>
                                @if($po->status === 'pending')
                                    <span class="badge bg-warning text-dark">En attente</span>
                                @elseif($po->status === 'approved')
                                    <span class="badge bg-info">Approuvé</span>
                                @elseif($po->status === 'received')
                                    <span class="badge bg-success">Reçu</span>
                                @else
                                    <span class="badge bg-secondary">Annulé</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEdit({{ $po->id }})" title="Modifier"><i class="bi bi-pencil"></i></button>
                                    @if($po->status === 'pending')
                                        <button type="button" class="btn btn-outline-success" wire:click="approve({{ $po->id }})" title="Approuver"><i class="bi bi-check-lg"></i></button>
                                        <button type="button" class="btn btn-outline-danger" wire:click="cancelOrder({{ $po->id }})" title="Annuler"><i class="bi bi-x-lg"></i></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-cart-check fs-1 d-block mb-2"></i>
                                Aucun bon de commande. Cliquez sur « Nouveau bon de commande » pour en créer un.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-light">{{ $orders->links() }}</div>
    </div>

    @if($showFormModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editingId ? 'Modifier le bon de commande' : 'Nouveau bon de commande' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeFormModal"></button>
                </div>
                <form wire:submit="saveOrder">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Fournisseur <span class="text-danger">*</span></label>
                                <select class="form-select @error('supplier_id') is-invalid @enderror" wire:model="supplier_id">
                                    <option value="">Sélectionner...</option>
                                    @foreach($suppliers as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bon de travail lié (optionnel)</label>
                                <select class="form-select" wire:model="work_order_id">
                                    <option value="">—</option>
                                    @foreach($workOrders as $wo)
                                        <option value="{{ $wo->id }}">{{ $wo->reference }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date commande <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" wire:model="order_date">
                                @error('order_date') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Livraison prévue</label>
                                <input type="date" class="form-control" wire:model="expected_delivery_date">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Lignes du bon</h6>
                            <button type="button" class="btn btn-sm btn-success" wire:click="addLine">
                                <i class="bi bi-plus-lg me-1"></i> Ajouter une ligne
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Article <span class="text-danger">*</span></th>
                                        <th style="width:100px">Quantité <span class="text-danger">*</span></th>
                                        <th style="width:120px">Prix unitaire (F)</th>
                                        <th style="width:80px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lines as $index => $line)
                                        <tr>
                                            <td>
                                                <select class="form-select form-select-sm @error('lines.'.$index.'.article_id') is-invalid @enderror" wire:model="lines.{{ $index }}.article_id">
                                                    <option value="">—</option>
                                                    @foreach($articles as $a)
                                                        <option value="{{ $a->id }}">{{ $a->name }} ({{ $a->reference }})</option>
                                                    @endforeach
                                                </select>
                                                @error('lines.'.$index.'.article_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm" wire:model="lines.{{ $index }}.quantity" min="1">
                                                @error('lines.'.$index.'.quantity') <span class="text-danger small">{{ $message }}</span> @enderror
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm" wire:model="lines.{{ $index }}.unit_price" min="0" step="0.01" placeholder="0">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeLine({{ $index }})"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if(count($lines) === 0)
                            <p class="text-muted small">Ajoutez au moins une ligne (article + quantité).</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeFormModal">Annuler</button>
                        <button type="submit" class="btn btn-primary" @if(count($lines) === 0) disabled @endif>{{ $editingId ? 'Enregistrer' : 'Créer le bon' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
