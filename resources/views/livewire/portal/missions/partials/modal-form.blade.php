@if($showFormModal)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="overflow-y: scroll">
            <div class="modal-header">
                <h5 class="modal-title">{{ $editingId ? 'Modifier la mission' : 'Nouvelle réservation' }}</h5>
                <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
            </div>
            <form wire:submit="saveMission" novalidate>
                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Erreurs de validation :</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Véhicule <span class="text-danger">*</span></label>
                            <select class="form-select @error('vehicle_id') is-invalid @enderror" wire:model="vehicle_id">
                                <option value="">—</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->registration }}</option>
                                @endforeach
                            </select>
                            @error('vehicle_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Chauffeur</label>
                            <select class="form-select" wire:model="driver_id">
                                <option value="">—</option>
                                @foreach($drivers as $d)
                                    <option value="{{ $d->id }}">{{ $d->last_name }} {{ $d->first_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Techniciens du garage (rotation)</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model.live="create_technician" id="create_technician">
                                    <label class="form-check-label" for="create_technician">
                                        Technicien non répertorié
                                    </label>
                                </div>
                            </div>
                            <select class="form-select" wire:model="technician_ids" multiple>
                                @foreach($technicians as $tech)
                                    <option value="{{ $tech->id }}">{{ $tech->last_name }} {{ $tech->first_name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Maintenez Ctrl/Cmd pour selection multiple.</div>
                            @if($create_technician)
                                <div class="mt-2">
                                    <input type="text" class="form-control @error('new_technician_name') is-invalid @enderror" wire:model="new_technician_name" placeholder="Nom complet du technicien">
                                    @error('new_technician_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    <div class="form-text">Sera ajouté à la rotation et enregistré comme mécanicien.</div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label">Demandeur <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model.live="create_demandeur" id="create_demandeur">
                                    <label class="form-check-label" for="create_demandeur">
                                        Nouveau demandeur
                                    </label>
                                </div>
                            </div>
                            
                            @if(!$create_demandeur)
                                <select class="form-select @error('demandeur_id') is-invalid @enderror" wire:model="demandeur_id">
                                    <option value="">—</option>
                                    @foreach($demandeurs as $d)
                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                                @error('demandeur_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            @else
                                <div class="border rounded p-2 bg-light">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <label class="form-label small">Nom complet <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-sm" wire:model="new_demandeur_name" placeholder="ex: Alain ATANGANA">
                                            @error('new_demandeur_name') <span class="invalid-feedback small">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small">Téléphone</label>
                                            <input type="tel" class="form-control form-control-sm" wire:model="new_demandeur_phone" placeholder="ex: 6XX XXX XXX">
                                            @error('new_demandeur_phone') <span class="invalid-feedback small">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small">Email</label>
                                            <input type="email" class="form-control form-control-sm" wire:model="new_demandeur_email" placeholder="ex: email@exemple.com">
                                            @error('new_demandeur_email') <span class="invalid-feedback small">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small">Service/Direction</label>
                                            <input type="text" class="form-control form-control-sm" wire:model="new_demandeur_service" placeholder="ex: Direction Générale">
                                            @error('new_demandeur_service') <span class="invalid-feedback small">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label">Destination</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model.live="create_city" id="create_city">
                                    <label class="form-check-label" for="create_city">
                                        Nouvelle ville
                                    </label>
                                </div>
                            </div>

                            @if(!$create_city)
                                <div class="input-group">
                                    <input type="text" class="form-control" wire:model="destination" placeholder="ex: Bureau principal">
                                    <select class="form-select" style="max-width: 200px;" wire:model="city_id">
                                        <option value="">Ville...</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-text">Sélectionnez une ville pré-enregistrée ou saisissez une destination personnalisée.</div>
                            @else
                                <div class="border rounded p-2 bg-light">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <label class="form-label small">Nom de la ville <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-sm" wire:model="new_city_name" placeholder="ex: Douala">
                                            @error('new_city_name') <span class="invalid-feedback small">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small">Région <span class="text-danger">*</span></label>
                                            <select class="form-select form-select-sm" wire:model="new_region_id">
                                                <option value="">Sélectionner...</option>
                                                @foreach($regions as $region)
                                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('new_region_id') <span class="invalid-feedback small">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small">Destination spécifique (optionnel)</label>
                                            <input type="text" class="form-control form-control-sm" wire:model="destination" placeholder="ex: Bureau principal">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Début <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_start') is-invalid @enderror" wire:model="date_start">
                            @error('date_start') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fin <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_end') is-invalid @enderror" wire:model="date_end">
                            @error('date_end') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">KM départ</label>
                            <input type="number" min="0" class="form-control" wire:model.live="km_departure">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">KM retour</label>
                            <input type="number" min="0" class="form-control" wire:model.live="km_return">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Raison du déplacement</label>
                            <input type="text" class="form-control @error('raison') is-invalid @enderror" wire:model="raison" placeholder="ex: Réunion budgétaire, Festival culturel, Obsèques…" maxlength="300">
                            @error('raison') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                        </div>
                        <div class="col-12">
                            <hr class="my-1">
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
                                            <th style="width:160px">Type</th>
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
                                                @if(!empty($row['file']))
                                                    <div class="text-muted small mt-1">
                                                        <i class="bi bi-file-earmark me-1"></i>{{ $row['file']->getClientOriginalName() }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm"
                                                    wire:model="form_doc_rows.{{ $i }}.type">
                                                    <option value="ordre_mission">Ordre de mission</option>
                                                    <option value="rapport">Rapport de mission</option>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" wire:click="$set('showFormModal', false)">Annuler</button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $editingId ? 'Enregistrer' : 'Créer' }}</span>
                        <span wire:loading>
                            <i class="bi bi-hourglass-split me-1"></i> Traitement...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
