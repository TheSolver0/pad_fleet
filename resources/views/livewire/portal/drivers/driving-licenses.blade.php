<div>
    <p class="section-label">Gestion des permis de conduire</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Permis de conduire</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Numéro, catégorie, chauffeur..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="status_filter">
                        <option value="">Tous les statuts</option>
                        <option value="active">Actifs</option>
                        <option value="expiring">Expiration proche</option>
                        <option value="expired">Expirés</option>
                        <option value="inactive">Inactifs</option>
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouveau permis
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Chauffeur</th>
                        <th>Numéro</th>
                        <th>Type</th>
                        <th>Catégorie</th>
                        <th>Date d'émission</th>
                        <th>Date d'expiration</th>
                        <th>Autorité</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($licenses as $license)
                        <tr wire:key="license-{{ $license->id }}">
                            <td>
                                <div class="fw-semibold">{{ $license->driver->full_name }}</div>
                                <small class="text-muted">{{ $license->driver->matricule }}</small>
                            </td>
                            <td><span class="badge bg-info">{{ $license->license_number }}</span></td>
                            <td>
                                <span class="badge bg-primary">{{ $license->license_type_label }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $license->category_label }}</span>
                            </td>
                            <td>{{ $license->issue_date->format('d/m/Y') }}</td>
                            <td>
                                <div class="fw-semibold {{ $license->isExpired() ? 'text-danger' : ($license->isExpiringSoon() ? 'text-warning' : '') }}">
                                    {{ $license->expiry_date->format('d/m/Y') }}
                                </div>
                                @if($license->isExpiringSoon())
                                    <small class="text-warning">({{ $license->expiry_date->diffInDays(now()) }} jours)</small>
                                @endif
                            </td>
                            <td>{{ $license->issuing_authority }}</td>
                            <td>
                                <span class="badge bg-{{ $license->status_color }}">
                                    {{ $license->status }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @if($license->file_path)
                                        <a href="{{ $license->file_url }}" target="_blank" class="btn btn-outline-info" title="Voir le permis">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endif
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEdit({{ $license->id }})" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-{{ $license->is_active ? 'warning' : 'success' }}" 
                                            wire:click="toggleActive({{ $license->id }})" 
                                            title="{{ $license->is_active ? 'Désactiver' : 'Activer' }}">
                                        <i class="bi bi-{{ $license->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" wire:click="confirmDelete({{ $license->id }})" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-card-text fs-1 d-block mb-2"></i>
                                Aucun permis de conduire trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light">
            {{ $licenses->links() }}
        </div>
    </div>

    <!-- Modal Formulaire Permis -->
    @if($showFormModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $editingId ? 'Modifier le permis de conduire' : 'Nouveau permis de conduire' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeFormModal"></button>
                </div>
                <form wire:submit="saveLicense">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Chauffeur *</label>
                                    <select class="form-select" wire:model="driver_id">
                                        <option value="">Sélectionner...</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->full_name }} ({{ $driver->matricule }})</option>
                                        @endforeach
                                    </select>
                                    @error('driver_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Numéro du permis *</label>
                                    <input type="text" class="form-control" wire:model="license_number" placeholder="ex: 1234567890">
                                    @error('license_number') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Type de permis *</label>
                                    <select class="form-select" wire:model="license_type">
                                        <option value="A">A - Moto</option>
                                        <option value="B">B - Voiture</option>
                                        <option value="C">C - Poids lourd</option>
                                        <option value="D">D - Autobus</option>
                                        <option value="E">E - Remorque</option>
                                        <option value="F">F - Agricole</option>
                                        <option value="G">G - Engin spécial</option>
                                        <option value="H">H - Transport en commun</option>
                                        <option value="I">I - Transport marchandises</option>
                                    </select>
                                    @error('license_type') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Catégorie *</label>
                                    <input type="text" class="form-control" wire:model="category" placeholder="ex: B, C1, D, etc.">
                                    @error('category') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Pays d'émission *</label>
                                    <input type="text" class="form-control" wire:model="issuing_country" maxlength="100">
                                    @error('issuing_country') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Date d'émission *</label>
                                    <input type="date" class="form-control" wire:model="issue_date">
                                    @error('issue_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Date d'expiration *</label>
                                    <input type="date" class="form-control" wire:model="expiry_date">
                                    @error('expiry_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Statut</label>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" wire:model="is_active" id="is_active">
                                        <label class="form-check-label" for="is_active">
                                            Permis actif
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Autorité d'émission *</label>
                            <input type="text" class="form-control" wire:model="issuing_authority" placeholder="ex: DGSN, Préfecture, etc.">
                            @error('issuing_authority') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                                    @error('notes') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Scan du permis</label>
                                    <input type="file" class="form-control" wire:model="license_file" accept=".pdf,.jpg,.jpeg,.png">
                                    @error('license_file') <span class="text-danger small">{{ $message }}</span> @enderror
                                    <div wire:loading wire:target="license_file" class="small text-muted mt-1">Téléversement…</div>

                                    @if($license_file)
                                        {{-- Aperçu du fichier nouvellement sélectionné --}}
                                        <div class="mt-2">
                                            @if(in_array(strtolower($license_file->getClientOriginalExtension()), ['jpg','jpeg','png']))
                                                <img src="{{ $license_file->temporaryUrl() }}" class="img-thumbnail" style="max-height:120px">
                                            @else
                                                <div class="small"><i class="bi bi-file-earmark-pdf text-danger me-1"></i>{{ $license_file->getClientOriginalName() }}</div>
                                            @endif
                                        </div>
                                    @elseif($existing_file_path)
                                        {{-- Fichier déjà enregistré : aperçu + téléchargement --}}
                                        <div class="mt-2">
                                            @if(preg_match('/\.(jpe?g|png)$/i', $existing_file_path))
                                                <a href="{{ asset('storage/' . $existing_file_path) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $existing_file_path) }}" class="img-thumbnail" style="max-height:120px">
                                                </a>
                                            @else
                                                <div class="small"><i class="bi bi-file-earmark-pdf text-danger me-1"></i>Fichier déjà enregistré</div>
                                            @endif
                                            <div>
                                                <a href="{{ asset('storage/' . $existing_file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary mt-1">
                                                    <i class="bi bi-download me-1"></i>Télécharger
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <small class="text-muted d-block mt-1">Aucun fichier enregistré.</small>
                                    @endif
                                </div>
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
                    <p>Êtes-vous sûr de vouloir supprimer ce permis de conduire ?</p>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Cette action est irréversible.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeFormModal">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteLicense">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
