<div>
    <p class="section-label">Gestion des fournisseurs</p>
    <div class="activity-card mb-3">
        <div class="p-3">
            <div class="fw-semibold mb-2">Top fournisseurs par nombre de commandes</div>
            <div class="row g-2">
                @forelse($topSuppliers as $ts)
                    <div class="col-md-4">
                        <div class="border rounded p-2 small">
                            <div class="fw-semibold">{{ $ts->name }}</div>
                            <div class="text-muted">{{ $ts->purchase_orders_count }} commandes</div>
                            <div class="text-muted">Montant cumulé: {{ number_format((float)($ts->purchase_orders_sum_total_amount ?? 0), 0, ',', ' ') }} F</div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-muted small">Aucune commande fournisseur pour le moment.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Fournisseurs</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Nom, code, contact..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-building me-1"></i> Nouveau fournisseur
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
                        <th>Qualité / Délais / Réputation</th>
                        <th>Commandes</th>
                        <th>Contact principal</th>
                        <th>Contact personne</th>
                        <th>Téléphone contact</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $supplier->name }}</div>
                                @if($supplier->address)
                                    <small class="text-muted">{{ Str::limit($supplier->address, 50) }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $supplier->code ?? '-' }}</span></td>
                            <td>
                                @if($supplier->evaluations_count > 0)
                                    <span class="badge bg-primary me-1" title="Qualité">{{ number_format((float)$supplier->evaluations_avg_quality_score, 1) }}/5</span>
                                    @if($supplier->evaluations_avg_delivery_score !== null)
                                        <span class="badge bg-success me-1" title="Délais">{{ number_format((float)$supplier->evaluations_avg_delivery_score, 1) }}/5</span>
                                    @endif
                                    @if($supplier->evaluations_avg_reputation_score !== null)
                                        <span class="badge bg-warning text-dark" title="Réputation">{{ number_format((float)$supplier->evaluations_avg_reputation_score, 1) }}/5</span>
                                    @endif
                                    <br><a href="{{ route('prestataire-evaluations.index', ['type_filter' => 'supplier', 'search' => $supplier->name]) }}" class="small">Voir évals</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $supplier->purchase_orders_count ?? 0 }}</span>
                                <div class="small text-muted">{{ number_format((float)($supplier->purchase_orders_sum_total_amount ?? 0), 0, ',', ' ') }} F</div>
                            </td>
                            <td>
                                @if($supplier->phone)
                                    <div><i class="bi bi-telephone me-1"></i>{{ $supplier->phone }}</div>
                                @endif
                                @if($supplier->email)
                                    <div><i class="bi bi-envelope me-1"></i>{{ $supplier->email }}</div>
                                @endif
                            </td>
                            <td>{{ $supplier->contact_person ?? '-' }}</td>
                            <td>{{ $supplier->contact_phone ?? '-' }}</td>
                            <td>
                                @if($supplier->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('prestataire-evaluations.index', ['evaluable_type' => 'Supplier', 'evaluable_id' => $supplier->id]) }}" class="btn btn-outline-success" title="Évaluer"><i class="bi bi-star"></i></a>
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEdit({{ $supplier->id }})" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" wire:click="confirmDelete({{ $supplier->id }})" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-building fs-1 d-block mb-2"></i>
                                Aucun fournisseur trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light">
            {{ $suppliers->links() }}
        </div>
    </div>

    <!-- Modal Formulaire Fournisseur -->
    @if($showFormModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $editingId ? 'Modifier le fournisseur' : 'Nouveau fournisseur' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeFormModal"></button>
                </div>
                <form wire:submit="saveSupplier">
                    <div class="modal-body">
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
                                    <label class="form-label">Code</label>
                                    <input type="text" class="form-control" wire:model="code">
                                    @error('code') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" wire:model="phone">
                                    @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" wire:model="email">
                                    @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Adresse</label>
                            <textarea class="form-control" rows="2" wire:model="address"></textarea>
                            @error('address') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Personne à contacter</label>
                                    <input type="text" class="form-control" wire:model="contact_person">
                                    @error('contact_person') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Téléphone contact</label>
                                    <input type="tel" class="form-control" wire:model="contact_phone">
                                    @error('contact_phone') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="3" wire:model="notes"></textarea>
                            @error('notes') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="is_active" id="is_active">
                                <label class="form-check-label" for="is_active">
                                    Fournisseur actif
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
                    <p>Êtes-vous sûr de vouloir supprimer ce fournisseur ? Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeFormModal">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteSupplier">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
