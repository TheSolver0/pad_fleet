<div>
    <p class="section-label">Déplacements véhicules — Trajets planifiés, destinations, kilométrage et documents (distinct du Planning missions)</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Déplacements</span>
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
                                    <button type="button" class="btn btn-outline-info" wire:click="openDocumentModal({{ $schedule->id }})" title="Documents">
                                        <i class="bi bi-file-earmark"></i>
                                        @if($schedule->documents_count > 0)
                                            <span class="badge bg-info ms-1">{{ $schedule->documents_count }}</span>
                                        @endif
                                    </button>
                                    <a href="{{ route('vehicles.control-sheets', ['schedule_id' => $schedule->id]) }}" class="btn btn-outline-warning" title="Fiche de contrôle véhicule">
                                        <i class="bi bi-clipboard-check"></i>
                                        @if($schedule->control_sheets_count > 0)
                                            <span class="badge bg-success ms-1">{{ $schedule->control_sheets_count }}</span>
                                        @endif
                                    </a>
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
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label mb-0">Destination *</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" wire:model.live="create_city" id="schedule_create_city">
                                            <label class="form-check-label small" for="schedule_create_city">Nouvelle ville</label>
                                        </div>
                                    </div>

                                    @if(!$create_city)
                                        <div class="input-group">
                                            <input type="text" class="form-control" wire:model="destination" placeholder="Lieu précis (optionnel)">
                                            <select class="form-select" style="max-width: 220px;" wire:model="city_id">
                                                <option value="">Ville...</option>
                                                @foreach($cities as $city)
                                                    <option value="{{ $city->id }}">{{ $city->display_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-text">Sélectionnez une ville existante. Si besoin, ajoutez un lieu précis.</div>
                                    @else
                                        <div class="border rounded p-2 bg-light">
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <label class="form-label small">Nom de la ville <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm" wire:model="new_city_name" placeholder="ex: Douala">
                                                    @error('new_city_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small">Région <span class="text-danger">*</span></label>
                                                    <select class="form-select form-select-sm" wire:model="new_region_id">
                                                        <option value="">Sélectionner...</option>
                                                        @foreach($regions as $region)
                                                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('new_region_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small">Lieu précis (optionnel)</label>
                                                    <input type="text" class="form-control form-control-sm" wire:model="destination" placeholder="ex: Site portuaire">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
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

                        <hr class="my-2">
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label fw-semibold mb-0">
                                    <i class="bi bi-paperclip me-1"></i> Documents de validation
                                </label>
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addFormDocRow">
                                    <i class="bi bi-plus-lg me-1"></i> Ajouter
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-1 align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Fichier <span class="text-muted fw-normal small">(PDF, JPG, PNG)</span></th>
                                            <th style="width:170px">Type</th>
                                            <th>Note de validation</th>
                                            <th style="width:36px"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($form_doc_rows as $i => $row)
                                        <tr>
                                            <td>
                                                <input type="file" class="form-control form-control-sm"
                                                    wire:model="form_doc_rows.{{ $i }}.file"
                                                    accept=".pdf,image/*">
                                                @error("form_doc_rows.$i.file")
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm"
                                                    wire:model="form_doc_rows.{{ $i }}.type">
                                                    <option value="ordre_mission">Ordre de mission</option>
                                                    <option value="rapport">Rapport de déplacement</option>
                                                    <option value="facture">Facture</option>
                                                    <option value="recu">Reçu</option>
                                                    <option value="autre">Autre document</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    wire:model="form_doc_rows.{{ $i }}.note"
                                                    placeholder="Note de validation…">
                                            </td>
                                            <td class="text-center">
                                                @if(count($form_doc_rows) > 1)
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    wire:click="removeFormDocRow({{ $i }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-text">10 Mo max par fichier.</div>
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

    <!-- Modal Documents -->
    @if($showDocumentModal)
    <div class="modal show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-file-earmark me-2"></i>Documents du déplacement</h5>
                    <button type="button" class="btn-close" wire:click="$set('showDocumentModal', false)"></button>
                </div>
                <form wire:submit="saveDocuments">
                    <div class="modal-body">
                        @php
                            $schedule = \App\Models\VehicleSchedule::find($documentScheduleId);
                            $existingDocs = $schedule ? $schedule->documents()->orderBy('sort_order')->get() : collect();
                        @endphp

                        {{-- Documents existants --}}
                        @if($existingDocs->count() > 0)
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-2">Documents enregistrés</h6>
                                <div class="row g-2">
                                    @foreach($existingDocs as $doc)
                                        <div class="col-md-6">
                                            <div class="card border">
                                                <div class="card-body p-2 d-flex align-items-center">
                                                    <i class="bi {{ $doc->file_type === 'image' ? 'bi-image' : 'bi-file-earmark-pdf' }} fs-4 text-muted me-2"></i>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <div class="fw-semibold small text-truncate">{{ $doc->original_name }}</div>
                                                        <div class="text-muted small">{{ $doc->document_type_label }} · {{ $doc->formatted_file_size }}</div>
                                                        @if($doc->caption)
                                                            <div class="text-info small"><i class="bi bi-chat-left-text me-1"></i>{{ $doc->caption }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="ms-2 d-flex gap-1">
                                                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Voir">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteDocument({{ $doc->id }})" title="Supprimer">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="text-muted small mb-3"><i class="bi bi-info-circle me-1"></i>Aucun document enregistré pour ce déplacement.</p>
                        @endif

                        {{-- Ajout de nouveaux documents --}}
                        <hr class="my-3">
                        <h6 class="fw-semibold mb-2">Ajouter un document</h6>
                        <div class="row g-2 mb-2">
                            <div class="col-md-5">
                                <label class="form-label small">Type de document</label>
                                <select class="form-select form-select-sm" wire:model="document_type">
                                    <option value="ordre_mission">Ordre de mission</option>
                                    <option value="rapport">Rapport de déplacement</option>
                                    <option value="facture">Facture</option>
                                    <option value="recu">Reçu</option>
                                    <option value="autre">Autre document</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label small">Note de validation</label>
                                <input type="text" class="form-control form-control-sm" wire:model="document_note" placeholder="Note de validation…">
                            </div>
                            <div class="col-12">
                                <label class="form-label small">Fichier(s)</label>
                                <input type="file" class="form-control form-control-sm" wire:model="documents" multiple accept=".pdf,image/*">
                                @error('documents.*') <span class="text-danger small">{{ $message }}</span> @enderror
                                <div class="form-text">PDF, JPG, PNG — 10 Mo max.</div>
                            </div>
                        </div>

                        @if($documents)
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($documents as $file)
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-file-earmark me-1"></i>{{ $file->getClientOriginalName() }}
                                        <span class="text-muted ms-1">({{ number_format($file->getSize() / 1024, 1) }} KB)</span>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" wire:click="$set('showDocumentModal', false)">Fermer</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="bi bi-upload me-1"></i>Enregistrer</span>
                            <span wire:loading><i class="bi bi-hourglass-split me-1"></i>Envoi...</span>
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
