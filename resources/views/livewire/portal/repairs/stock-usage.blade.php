<div>
    <p class="section-label">Consommation de stock — Réparations et atelier (usage interne)</p>
    <p class="small text-muted mb-3">Sorties de pièces et consommables pour les réparations véhicules. Le matériel n'est pas vendu mais consommé en interne.</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Historique de consommation</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="N° réparation, immatriculation..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouvelle utilisation
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Réparation</th>
                        <th>Véhicule</th>
                        <th>Pièces utilisées</th>
                        <th>Matériel usé</th>
                        <th>Coût pièces</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($repairs as $repair)
                        <tr>
                            <td>
                                <span class="badge bg-primary">#{{ $repair->id }}</span>
                                @if($repair->mechanic)
                                    <small class="text-muted">{{ $repair->mechanic->first_name }} {{ $repair->mechanic->last_name }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $repair->vehicle->registration }}</div>
                                <small class="text-muted">{{ $repair->vehicle->vehicleModel->name }}</small>
                            </td>
                            <td>
                                @if($repair->repairParts->count() > 0)
                                    <div class="small">
                                        @foreach($repair->repairParts as $part)
                                            <div>• {{ $part->quantity_used }}x {{ $part->article->name }}</div>
                                        @endforeach
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($repair->usedMaterials->count() > 0)
                                    <div class="small">
                                        @foreach($repair->usedMaterials as $material)
                                            <div>• {{ $material->quantity }}x {{ $material->article->name }} ({{ $material->condition }})</div>
                                        @endforeach
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="fw-semibold">
                                @if($repair->parts_cost)
                                    {{ number_format($repair->parts_cost, 2, ',', ' ') }} €
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $repair->created_at->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" wire:click="editRepair({{ $repair->id }})" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-tools fs-1 d-block mb-2"></i>
                                Aucune utilisation de stock trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light">
            {{ $repairs->links() }}
        </div>
    </div>

    <!-- Modal Formulaire Utilisation Stock -->
    @if($showFormModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Utilisation du stock pour réparation</h5>
                    <button type="button" class="btn-close" wire:click="closeFormModal"></button>
                </div>
                <form wire:submit="saveStockUsage">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Réparation *</label>
                                <select class="form-select" wire:model="repair_id">
                                    <option value="">Sélectionner...</option>
                                    @foreach(\App\Models\Repair::orderBy('created_at', 'desc')->get() as $repair)
                                        <option value="{{ $repair->id }}">
                                            #{{ $repair->id }} - {{ $repair->vehicle->registration }} ({{ $repair->vehicle->vehicleModel->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('repair_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Pièces utilisées -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Pièces utilisées</h6>
                                <button type="button" class="btn btn-sm btn-outline-success" wire:click="addPart">
                                    <i class="bi bi-plus"></i> Ajouter une pièce
                                </button>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Article</th>
                                            <th>Quantité</th>
                                            <th>Prix unitaire</th>
                                            <th>Emplacement</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($parts as $index => $part)
                                            <tr>
                                                <td>
                                                    <select class="form-select form-select-sm" wire:model="parts.{{ $index }}.article_id">
                                                        <option value="">Sélectionner...</option>
                                                        @foreach($articles as $article)
                                                            <option value="{{ $article->id }}">{{ $article->name }} ({{ $article->reference }})</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm" wire:model="parts.{{ $index }}.quantity_used" min="1">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" wire:model="parts.{{ $index }}.unit_price" placeholder="0,00">
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" wire:model="parts.{{ $index }}.stock_location">
                                                        <option value="main">Magasin principal</option>
                                                        <option value="garage">Magasin garage</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removePart({{ $index }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Matériel usé -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Matériel usé (doit être égal aux pièces utilisées)</h6>
                                <button type="button" class="btn btn-sm btn-outline-warning" wire:click="addUsedMaterial">
                                    <i class="bi bi-plus"></i> Ajouter du matériel usé
                                </button>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Important :</strong> La quantité totale de matériel usé doit être égale à la quantité totale de pièces changées.
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Article</th>
                                            <th>Quantité</th>
                                            <th>État</th>
                                            <th>Disposition</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($used_materials as $index => $material)
                                            <tr>
                                                <td>
                                                    <select class="form-select form-select-sm" wire:model="used_materials.{{ $index }}.article_id">
                                                        <option value="">Sélectionner...</option>
                                                        @foreach($articles as $article)
                                                            <option value="{{ $article->id }}">{{ $article->name }} ({{ $article->reference }})</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm" wire:model="used_materials.{{ $index }}.quantity" min="1">
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" wire:model="used_materials.{{ $index }}.condition">
                                                        <option value="usé">Usé</option>
                                                        <option value="très usé">Très usé</option>
                                                        <option value="défectueux">Défectueux</option>
                                                        <option value="réparable">Réparable</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" wire:model="used_materials.{{ $index }}.disposition">
                                                        <option value="stocké">Stocké</option>
                                                        <option value="jeté">Jeté</option>
                                                        <option value="recyclé">Recyclé</option>
                                                        <option value="réparé">Réparé</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeUsedMaterial({{ $index }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
                            Enregistrer l'utilisation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
