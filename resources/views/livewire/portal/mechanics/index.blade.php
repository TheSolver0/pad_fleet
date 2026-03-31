<div>
    <p class="section-label">Gestion des mécaniciens</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Mécaniciens</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Nom, téléphone, email..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-person-plus me-1"></i> Nouveau mécanicien
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Nom</th>
                        <th>Contact</th>
                        <th>Spécialisation</th>
                        <th>Taux horaire</th>
                        <th>Date d'embauche</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mechanics as $mechanic)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $mechanic->first_name }} {{ $mechanic->last_name }}</div>
                                @if($mechanic->address)
                                    <small class="text-muted">{{ Str::limit($mechanic->address, 40) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($mechanic->phone)
                                    <div><i class="bi bi-telephone me-1"></i>{{ $mechanic->phone }}</div>
                                @endif
                                @if($mechanic->email)
                                    <div><i class="bi bi-envelope me-1"></i>{{ $mechanic->email }}</div>
                                @endif
                            </td>
                            <td>{{ $mechanic->specialization ?? '-' }}</td>
                            <td class="text-end">
                                @if($mechanic->hourly_rate)
                                    {{ number_format($mechanic->hourly_rate, 2, ',', ' ') }} €/h
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $mechanic->hire_date?->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                @if($mechanic->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEdit({{ $mechanic->id }})" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" wire:click="confirmDelete({{ $mechanic->id }})" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-people fs-1 d-block mb-2"></i>
                                Aucun mécanicien trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light">
            {{ $mechanics->links() }}
        </div>
    </div>

    <!-- Modal Formulaire Mécanicien -->
    @if($showFormModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $editingId ? 'Modifier le mécanicien' : 'Nouveau mécanicien' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeFormModal"></button>
                </div>
                <form wire:submit="saveMechanic">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Prénom *</label>
                                    <input type="text" class="form-control" wire:model="first_name" required>
                                    @error('first_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" class="form-control" wire:model="last_name" required>
                                    @error('last_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Compte utilisateur lié (accès plateforme)</label>
                            <select class="form-select" wire:model="user_id">
                                <option value="">—</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                            @error('user_id') <span class="text-danger small">{{ $message }}</span> @enderror
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
                                    <label class="form-label">Spécialisation</label>
                                    <input type="text" class="form-control" wire:model="specialization" 
                                           placeholder="ex: Mécanique générale, carrosserie, électricité...">
                                    @error('specialization') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date d'embauche</label>
                                    <input type="date" class="form-control" wire:model="hire_date">
                                    @error('hire_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Certificat/Diplôme</label>
                                    <input type="text" class="form-control" wire:model="certificate">
                                    @error('certificate') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Taux horaire (€/h)</label>
                                    <input type="text" class="form-control" wire:model="hourly_rate" placeholder="0,00">
                                    @error('hourly_rate') <span class="text-danger small">{{ $message }}</span> @enderror
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
                                    Mécanicien actif
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
                    <p>Êtes-vous sûr de vouloir supprimer ce mécanicien ? Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeFormModal">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteMechanic">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
