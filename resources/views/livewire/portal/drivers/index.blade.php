
<div>
    <p class="section-label">Chauffeurs — Profils, permis, affectation direction, personne ressource</p>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h4 class="text-primary mb-1">{{ $stats['total'] }}</h4>
                    <small class="text-muted">Total chauffeurs</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h4 class="text-success mb-1">{{ $stats['active_licenses'] }}</h4>
                    <small class="text-muted">Permis actifs</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h4 class="text-warning mb-1">{{ $stats['expired_licenses'] }}</h4>
                    <small class="text-muted">Permis expirés</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h4 class="text-danger mb-1">{{ $stats['no_licenses'] }}</h4>
                    <small class="text-muted">Sans permis</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h4 class="text-info mb-1">{{ $stats['available'] }}</h4>
                    <small class="text-muted">Disponibles</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <h4 class="text-secondary mb-1">{{ $stats['total'] - $stats['available'] }}</h4>
                    <small class="text-muted">Indisponibles</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 mt-3">
            <div class="card border-dark">
                <div class="card-body text-center">
                    <h4 class="text-dark mb-1">{{ $stats['garage_available'] }}/{{ $stats['garage_drivers'] }}</h4>
                    <small class="text-muted">Chauffeurs garage dispo</small>
                </div>
            </div>
        </div>
    </div>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Chauffeurs</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Nom, matricule..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="direction_filter">
                        <option value="">Toutes directions</option>
                        @foreach($directions as $direction)
                            <option value="{{ $direction->id }}">{{ $direction->name }}</option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="person_filter">
                        <option value="">Toutes personnes</option>
                        @foreach($persons as $person)
                            <option value="{{ $person->id }}">{{ $person->name }}</option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="license_status_filter">
                        <option value="">Tous permis</option>
                        <option value="active">Permis actifs</option>
                        <option value="expired">Permis expirés</option>
                        <option value="none">Sans permis</option>
                    </select>
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="availability_filter">
                        <option value="">Tous statuts</option>
                        <option value="1">Disponibles</option>
                        <option value="0">Indisponibles</option>
                    </select>
                    <select class="form-select form-select-sm" style="width: 170px;" wire:model.live="garage_driver_filter">
                        <option value="">Tous profils</option>
                        <option value="1">Chauffeur du Garage</option>
                        <option value="0">Chauffeur standard</option>
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-person-plus me-1"></i> Nouveau chauffeur
                    </button>
                    <a href="{{ route('drivers.driving-licenses') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-card-text me-1"></i> Gérer les permis
                    </a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Matricule</th>
                        <th>Nom complet</th>
                        <th>Direction</th>
                        <th>Personne ressource</th>
                        <th>Permis de conduire</th>
                        <th>Statut</th>
                        <th>Profil</th>
                        <th>Missions</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drivers as $d)
                        <tr>
                            <td>{{ $d->matricule ?? '—' }}</td>
                            <td><span class="fw-medium">{{ $d->full_name }}</span></td>
                            <td>{{ $d->direction?->name ?? '—' }}</td>
                            <td>{{ $d->resourcePerson?->full_name ?? '—' }}</td>
                            <td>
                                @if($d->active_licenses_count > 0)
                                    <span class="badge bg-success">{{ $d->active_licenses_count }} permis actif(s)</span>
                                @else
                                    <span class="text-muted">Aucun permis</span>
                                @endif
                                @if($d->expired_licenses_count > 0)
                                    <br><small class="text-danger">{{ $d->expired_licenses_count }} expiré(s)</small>
                                @endif
                            </td>
                            <td>
    @if($d->is_available)
        <span class="badge bg-success">Disponible</span>
    @else
        <span class="badge bg-secondary">Indisponible</span>
    @endif
    @if($d->activeAssignment)
        <br><small class="text-muted" style="font-size:.75rem">
            <i class="bi bi-car-front"></i>
            {{ $d->activeAssignment->vehicle?->registration ?? '—' }}
        </small>
    @endif
</td>
                            <td>
                                @if($d->is_garage_driver)
                                    <span class="badge bg-dark">Chauffeur du Garage</span>
                                @else
                                    <span class="badge bg-light text-dark border">Standard</span>
                                @endif
                            </td>
                            <td>{{ $d->missions_count }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEdit({{ $d->id }})" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="{{ route('drivers.driving-licenses') }}" class="btn btn-outline-info" title="Voir les permis">
                                        <i class="bi bi-card-text"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-{{ $d->is_available ? 'warning' : 'success' }}" 
                                            wire:click="toggleAvailability({{ $d->id }})" 
                                            title="{{ $d->is_available ? 'Rendre indisponible' : 'Rendre disponible' }}">
                                        <i class="bi bi-{{ $d->is_available ? 'pause' : 'play' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary"
        wire:click="openAssignments({{ $d->id }})"
        title="Affectations">
    <i class="bi bi-diagram-3"></i>
</button>
                                    <button type="button" class="btn btn-outline-danger" wire:click="confirmDelete({{ $d->id }})" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">Aucun chauffeur</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($drivers->hasPages())
            <div class="p-3 border-top">{{ $drivers->links() }}</div>
        @endif
    </div>

    <!-- Modal Formulaire Chauffeur -->
    @if($showFormModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $editingId ? 'Modifier le chauffeur' : 'Nouveau chauffeur' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeFormModal"></button>
                </div>
                <form wire:submit="saveDriver">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Matricule</label>
                                    <input type="text" class="form-control" wire:model="matricule" placeholder="ex: CH001">
                                    @error('matricule') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" class="form-control" wire:model="first_name" required>
                                    @error('first_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Prénom *</label>
                                    <input type="text" class="form-control" wire:model="last_name" required>
                                    @error('last_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Téléphone</label>
                                    <input type="text" class="form-control" wire:model="phone" placeholder="+237 6XX XXX XXX">
                                    @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" wire:model="email" placeholder="chauffeur@example.com">
                                    @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Direction</label>
                                    <select class="form-select" wire:model="direction_id">
                                        <option value="">Sélectionner...</option>
                                        @foreach($directions as $direction)
                                            <option value="{{ $direction->id }}">{{ $direction->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('direction_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Personne ressource</label>
                                    <select class="form-select" wire:model="resource_person_id">
                                        <option value="">Sélectionner...</option>
                                        @foreach($persons as $person)
                                            <option value="{{ $person->id }}">{{ $person->full_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('resource_person_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
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
                                    <label class="form-label">Statut</label>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" wire:model="is_available" id="is_available">
                                        <label class="form-check-label" for="is_available">
                                            Disponible pour les missions
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Rubrique</label>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" wire:model="is_garage_driver" id="is_garage_driver">
                                        <label class="form-check-label" for="is_garage_driver">
                                            Chauffeur du Garage
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row border-top pt-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Pièce d'identité — Recto</label>
                                    <input type="file" class="form-control" wire:model="id_document_recto_file" accept="image/*,.pdf">
                                    @error('id_document_recto_file') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Pièce d'identité — Verso</label>
                                    <input type="file" class="form-control" wire:model="id_document_verso_file" accept="image/*,.pdf">
                                    @error('id_document_verso_file') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Permis de conduire -->
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Permis de conduire</h6>
                                <button type="button" class="btn btn-sm btn-success" wire:click="addLicense">
                                    <i class="bi bi-plus-lg me-1"></i> Ajouter un permis
                                </button>
                            </div>

                            @if(count($driving_licenses) > 0)
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>N° Permis *</th>
                                                <th>Type *</th>
                                                <th>Catégorie *</th>
                                                <th>Date d'émission *</th>
                                                <th>Échéance *</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($driving_licenses as $index => $license)
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm" 
                                                               wire:model="driving_licenses.{{ $index }}.license_number" 
                                                               placeholder="ex: 1234567890">
                                                        @error("driving_licenses.{$index}.license_number") 
                                                            <span class="text-danger small">{{ $message }}</span> 
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <select class="form-select form-select-sm" wire:model="driving_licenses.{{ $index }}.license_type">
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
                                                        @error("driving_licenses.{$index}.license_type") 
                                                            <span class="text-danger small">{{ $message }}</span> 
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm" 
                                                               wire:model="driving_licenses.{{ $index }}.category" 
                                                               placeholder="ex: B, C1, D, etc.">
                                                        @error("driving_licenses.{$index}.category") 
                                                            <span class="text-danger small">{{ $message }}</span> 
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="date" class="form-control form-control-sm" 
                                                               wire:model="driving_licenses.{{ $index }}.issue_date">
                                                        @error("driving_licenses.{$index}.issue_date") 
                                                            <span class="text-danger small">{{ $message }}</span> 
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="date" class="form-control form-control-sm" 
                                                               wire:model="driving_licenses.{{ $index }}.expiry_date">
                                                        @error("driving_licenses.{$index}.expiry_date") 
                                                            <span class="text-danger small">{{ $message }}</span> 
                                                        @enderror
                                                    </td>
                                                    <td class="text-end">
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                wire:click="removeLicense({{ $index }})" title="Supprimer">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-3 border">
                                    <i class="bi bi-card-text fs-1 d-block mb-2"></i>
                                    Aucun permis de conduire ajouté<br>
                                    <small>Cliquez sur "Ajouter un permis" pour en ajouter</small>
                                </div>
                            @endif
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
                    <p>Êtes-vous sûr de vouloir supprimer ce chauffeur ?</p>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Cette action est irréversible.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeFormModal">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteDriver">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($showAssignmentModal && $assignmentDriver)
<div class="modal fade show d-block" tabindex="-1" style="background-color:rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-diagram-3 me-2"></i>
                    Affectations — {{ $assignmentDriver->full_name }}
                </h5>
                <button type="button" class="btn-close" wire:click="closeAssignmentModal"></button>
            </div>

            <div class="modal-body">

                {{-- Formulaire création / édition --}}
                @if($showAssignmentForm)
                <div class="card border-primary mb-4">
                    <div class="card-header bg-primary text-white fw-semibold">
                        {{ $editingAssignmentId ? 'Modifier l\'affectation' : 'Nouvelle affectation' }}
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Type *</label>
                                <select class="form-select" wire:model.live="assign_type">
                                    <option value="vehicle">Véhicule fixe</option>
                                    <option value="mission">Mission ponctuelle</option>
                                </select>
                                @error('assign_type') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            @if($assign_type === 'vehicle')
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Véhicule *</label>
                                <select class="form-select" wire:model="assign_vehicle_id">
                                    <option value="">Sélectionner...</option>
                                    @foreach($vehicles as $v)
                                        <option value="{{ $v->id }}">{{ $v->registration }} — {{ $v->brand }} {{ $v->model }}</option>
                                    @endforeach
                                </select>
                                @error('assign_vehicle_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            @else
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Mission *</label>
                                <select class="form-select" wire:model="assign_mission_id">
                                    <option value="">Sélectionner...</option>
                                    @foreach($missions as $m)
                                        <option value="{{ $m->id }}">{{ $m->reference ?? '#'.$m->id }} — {{ $m->date_start?->format('d/m/Y') }}</option>
                                    @endforeach
                                </select>
                                @error('assign_mission_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            @endif

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Statut *</label>
                                <select class="form-select" wire:model="assign_status">
                                    <option value="active">Actif</option>
                                    <option value="suspended">Suspendu</option>
                                    <option value="ended">Terminé</option>
                                </select>
                                @error('assign_status') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Début *</label>
                                <input type="date" class="form-control" wire:model="assign_started_at">
                                @error('assign_started_at') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Fin</label>
                                <input type="date" class="form-control" wire:model="assign_ended_at">
                                @error('assign_ended_at') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Motif de fin</label>
                                <input type="text" class="form-control" wire:model="assign_end_reason"
                                       placeholder="Ex : Changement de poste, véhicule immobilisé...">
                                @error('assign_end_reason') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Notes</label>
                                <input type="text" class="form-control" wire:model="assign_notes">
                                @error('assign_notes') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-primary btn-sm" wire:click="saveAssignment">
                                <i class="bi bi-check-lg me-1"></i>
                                {{ $editingAssignmentId ? 'Mettre à jour' : 'Enregistrer' }}
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm"
                                    wire:click="$set('showAssignmentForm', false)">
                                Annuler
                            </button>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Bouton nouvelle affectation --}}
                @if(!$showAssignmentForm)
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openAssignmentCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouvelle affectation
                    </button>
                </div>
                @endif

                {{-- Historique --}}
                @php $assignments = $assignmentDriver->assignments; @endphp

                @if($assignments->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-diagram-3 fs-1 d-block mb-2"></i>
                        Aucune affectation enregistrée
                    </div>
                @else
                    {{-- Résumé rapide --}}
                    <div class="d-flex gap-3 mb-3 flex-wrap">
                        @php
                            $nbActive    = $assignments->where('status', 'active')->count();
                            $nbSuspended = $assignments->where('status', 'suspended')->count();
                            $nbEnded     = $assignments->where('status', 'ended')->count();
                        @endphp
                        <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 px-3 py-2">
                            {{ $nbActive }} actif(s)
                        </span>
                        <span class="badge bg-warning bg-opacity-15 text-warning border border-warning border-opacity-25 px-3 py-2">
                            {{ $nbSuspended }} suspendu(s)
                        </span>
                        <span class="badge bg-secondary bg-opacity-15 text-secondary border border-secondary border-opacity-25 px-3 py-2">
                            {{ $nbEnded }} terminé(s)
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Véhicule / Mission</th>
                                    <th>Statut</th>
                                    <th>Début</th>
                                    <th>Fin</th>
                                    <th>Motif fin</th>
                                    <th>Créé par</th>
                                    <th>Modifié par</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignments as $a)
                                <tr>
                                    <td>
                                        @if($a->type === 'vehicle')
                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                <i class="bi bi-car-front me-1"></i>Véhicule
                                            </span>
                                        @else
                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                <i class="bi bi-calendar3-week me-1"></i>Mission
                                            </span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">
                                        @if($a->type === 'vehicle')
                                            {{ $a->vehicle?->registration ?? '—' }}
                                            @if($a->vehicle)
                                                <br><small class="text-muted fw-normal">{{ $a->vehicle->brand }} {{ $a->vehicle->model }}</small>
                                            @endif
                                        @else
                                            {{ $a->mission?->reference ?? '#'.($a->mission_id ?? '—') }}
                                            @if($a->mission)
                                                <br><small class="text-muted fw-normal">{{ $a->mission->date_start?->format('d/m/Y') }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $a->statusColor() }}">{{ $a->statusLabel() }}</span>
                                    </td>
                                    <td style="font-size:.85rem">{{ $a->started_at?->format('d/m/Y') ?? '—' }}</td>
                                    <td style="font-size:.85rem">{{ $a->ended_at?->format('d/m/Y') ?? '—' }}</td>
                                    <td style="font-size:.82rem; color:var(--text-muted)">
                                        {{ $a->end_reason ? \Str::limit($a->end_reason, 40) : '—' }}
                                    </td>
                                    <td style="font-size:.82rem">
                                        {{ $a->createdBy?->name ?? '—' }}
                                        <br><span class="text-muted">{{ $a->created_at?->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td style="font-size:.82rem">
                                        {{ $a->updatedBy?->name ?? '—' }}
                                        <br><span class="text-muted">{{ $a->updated_at?->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary"
                                                    wire:click="openAssignmentEdit({{ $a->id }})"
                                                    title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger"
                                                    wire:click="deleteAssignment({{ $a->id }})"
                                                    wire:confirm="Supprimer cette affectation ?"
                                                    title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeAssignmentModal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endif
</div>
