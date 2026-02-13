<div>
    <p class="section-label">Planning des déplacements</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Planning</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Titre, destination, véhicule..." wire:model.live.debounce.300ms="search">
                    <input type="date" class="form-control form-control-sm" style="width: 140px;" wire:model.live="date_filter">
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="status_filter">
                        <option value="">Tous statuts</option>
                        <option value="planned">Planifié</option>
                        <option value="in_progress">En cours</option>
                        <option value="completed">Terminé</option>
                        <option value="cancelled">Annulé</option>
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-calendar-plus me-1"></i> Nouveau déplacement
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Date</th>
                        <th>Véhicule</th>
                        <th>Chauffeur</th>
                        <th>Destination</th>
                        <th>Distance (km)</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $schedule->start_datetime->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $schedule->start_datetime->format('H:i') }} - {{ $schedule->end_datetime->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $schedule->vehicle->registration }}</div>
                                <div class="text-muted small">{{ $schedule->title }}</div>
                            </td>
                            <td>
                                @if($schedule->driver)
                                    {{ $schedule->driver->first_name }} {{ $schedule->driver->last_name }}
                                @else
                                    <span class="text-muted">Non assigné</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $schedule->destination }}</div>
                                @if($schedule->departure_location)
                                    <small class="text-muted">Départ: {{ $schedule->departure_location }}</small>
                                @endif
                            </td>
                            <td>
                                @if($schedule->actual_distance)
                                    <div class="fw-semibold">{{ number_format($schedule->actual_distance, 2, ',', ' ') }}</div>
                                    @if($schedule->average_consumption)
                                        <small class="text-muted">{{ number_format($schedule->average_consumption, 1, ',', ' ') }}L/100km</small>
                                    @endif
                                @elseif($schedule->estimated_distance)
                                    <div class="text-muted">{{ number_format($schedule->estimated_distance, 2, ',', ' ') }} (est.)</div>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $schedule->status === 'completed' ? 'success' : ($schedule->status === 'in_progress' ? 'primary' : ($schedule->status === 'cancelled' ? 'danger' : 'secondary')) }}">
                                    {{ $schedule->status_label }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" wire:click="openEdit({{ $schedule->id }})" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" wire:click="confirmDelete({{ $schedule->id }})" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-calendar-range fs-1 d-block mb-2"></i>
                                Aucun déplacement trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light">
            {{ $schedules->links() }}
        </div>
    </div>

    <!-- Modal Formulaire Planning -->
    @if($showFormModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $editingId ? 'Modifier le déplacement' : 'Nouveau déplacement' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeFormModal"></button>
                </div>
                <form wire:submit="saveSchedule">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Véhicule *</label>
                                    <select class="form-select" wire:model="vehicle_id" required>
                                        <option value="">Sélectionner...</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">{{ $vehicle->registration }}</option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Chauffeur</label>
                                    <select class="form-select" wire:model="driver_id">
                                        <option value="">Non assigné</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->first_name }} {{ $driver->last_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('driver_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Titre *</label>
                                    <input type="text" class="form-control" wire:model="title" required>
                                    @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Statut</label>
                                    <select class="form-select" wire:model="status">
                                        <option value="planned">Planifié</option>
                                        <option value="in_progress">En cours</option>
                                        <option value="completed">Terminé</option>
                                        <option value="cancelled">Annulé</option>
                                    </select>
                                    @error('status') <span class="text-danger small">{{ $message }}</span> @enderror
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
                                    <label class="form-label">Lieu de départ</label>
                                    <input type="text" class="form-control" wire:model="departure_location">
                                    @error('departure_location') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Destination *</label>
                                    <input type="text" class="form-control" wire:model="destination" required>
                                    @error('destination') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date/Heure début *</label>
                                    <input type="datetime-local" class="form-control" wire:model="start_datetime" required>
                                    @error('start_datetime') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date/Heure fin *</label>
                                    <input type="datetime-local" class="form-control" wire:model="end_datetime" required>
                                    @error('end_datetime') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Distance estimée (km)</label>
                                    <input type="text" class="form-control" wire:model="estimated_distance" placeholder="0,00">
                                    @error('estimated_distance') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Kilométrage début</label>
                                    <input type="number" class="form-control" wire:model="mileage_start" min="0">
                                    @error('mileage_start') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Kilométrage fin</label>
                                    <input type="number" class="form-control" wire:model="mileage_end" min="0">
                                    @error('mileage_end') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Carburant consommé (L)</label>
                                    <input type="text" class="form-control" wire:model="fuel_consumed" placeholder="0,00">
                                    @error('fuel_consumed') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Objectif</label>
                                    <select class="form-select" wire:model="purpose">
                                        <option value="">Sélectionner...</option>
                                        <option value="transport_personnel">Transport personnel</option>
                                        <option value="livraison">Livraison</option>
                                        <option value="mission">Mission</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                    @error('purpose') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="3" wire:model="notes"></textarea>
                            @error('notes') <span class="text-danger small">{{ $message }}</span> @enderror
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
                    <p>Êtes-vous sûr de vouloir supprimer ce déplacement ? Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeFormModal">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteSchedule">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
