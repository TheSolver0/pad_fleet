@if($showFormModal)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $editingId ? 'Modifier le véhicule' : 'Nouveau véhicule' }}</h5>
                <button type="button" class="btn-close" wire:click="closeFormModal"></button>
            </div>
            <form wire:submit="saveVehicle">
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Ligne 1 : Identité --}}
                        <div class="col-md-4">
                            <label class="form-label">Immatriculation <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('registration') is-invalid @enderror" wire:model="registration">
                            @error('registration') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Marque</label>
                            <select class="form-select" wire:model.live="brand_id">
                                <option value="">— Choisir une marque —</option>
                                @foreach($brands as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Modèle <span class="text-danger">*</span></label>
                            <select class="form-select @error('vehicle_model_id') is-invalid @enderror" wire:model="vehicle_model_id">
                                <option value="">— Choisir un modèle —</option>
                                @foreach($vehicleModelsForBrand as $vm)
                                    <option value="{{ $vm->id }}">{{ $vm->name }}</option>
                                @endforeach
                            </select>
                            @error('vehicle_model_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        {{-- Ligne 2 : Catégorie, achat --}}
                        <div class="col-md-4">
                            <label class="form-label">Catégorie</label>
                            <select class="form-select" wire:model="category">
                                <option value="">— Choisir —</option>
                                @foreach(\App\Models\Vehicle::categoryOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date d'achat</label>
                            <input type="date" class="form-control" wire:model.live="purchase_date">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Prix d'achat (F)</label>
                            <input type="text" class="form-control" wire:model.live="purchase_price" placeholder="ex. 1 234 567,89" inputmode="decimal">
                        </div>
                        {{-- Ligne 3 : Valeur vénale, KM, Puissance --}}
                        <div class="col-md-4">
                            <label class="form-label">Valeur vénale (calcul auto)</label>
                            <input type="text" class="form-control bg-light" readonly value="{{ $this->computedVenalValue !== null && $this->computedVenalValue !== '' ? $this->computedVenalValue . ' F' : '—' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kilométrage</label>
                            <input type="number" min="0" class="form-control @error('mileage') is-invalid @enderror" wire:model="mileage">
                            @error('mileage') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Puissance (CV)</label>
                            <input type="number" min="0" class="form-control @error('power') is-invalid @enderror" wire:model="power" placeholder="ex. 90">
                            @error('power') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        {{-- Ligne 4 : Statut, Garage, Assurance --}}
                        <div class="col-md-4">
                            <label class="form-label">Statut</label>
                            <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                <option value="available">Disponible</option>
                                <option value="in_use">En mission</option>
                                <option value="repair">En réparation</option>
                                <option value="out_of_service">Hors service</option>
                            </select>
                            @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Garage (si en réparation)</label>
                            <select class="form-select" wire:model="garage_id">
                                <option value="">—</option>
                                @foreach($garages as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Marché assurance</label>
                            <select class="form-select" wire:model="insurance_contract_global_id">
                                <option value="">—</option>
                                @foreach($contracts as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Ligne 5 : Affectation (personne + bouton +, type) --}}
                        <div class="col-md-4">
                            <label class="form-label">Assigné à</label>
                            <div class="input-group">
                                <select class="form-select @error('assigned_person_id') is-invalid @enderror" wire:model.live="assigned_person_id">
                                    <option value="">— Aucune affectation —</option>
                                    @foreach($persons as $person)
                                        <option value="{{ $person->id }}">{{ $person->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary" wire:click="openQuickAddPerson" title="Ajouter une personne">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                            @error('assigned_person_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type d'affectation</label>
                            <select class="form-select" wire:model.live="assignment_type">
                                <option value="">— Choisir —</option>
                                @foreach(\App\Models\Vehicle::assignmentTypeOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Période</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="periodIndefinite" wire:model.live="assignment_period_indefinite">
                                <label class="form-check-label small" for="periodIndefinite">Indéfinie</label>
                            </div>
                        </div>
                        @if(!$assignment_period_indefinite)
                        <div class="col-md-4">
                            <label class="form-label">Début affectation</label>
                            <input type="date" class="form-control" wire:model="assignment_start_at">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fin affectation</label>
                            <input type="date" class="form-control" wire:model="assignment_end_at">
                        </div>
                        <div class="col-md-4"></div>
                        @else
                        <div class="col-md-4">
                            <label class="form-label">À partir du (optionnel)</label>
                            <input type="date" class="form-control" wire:model="assignment_start_at">
                        </div>
                        <div class="col-md-4"></div>
                        @endif
                        {{-- Ligne 6 : Notes --}}
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" wire:click="closeFormModal">Annuler</button>
                    <button type="submit" class="btn btn-primary">{{ $editingId ? 'Enregistrer' : 'Créer' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
