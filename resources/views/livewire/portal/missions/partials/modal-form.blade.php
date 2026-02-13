@if($showFormModal)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $editingId ? 'Modifier la mission' : 'Nouvelle réservation' }}</h5>
                <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
            </div>
            <form wire:submit="saveMission">
                <div class="modal-body">
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
                                            <input type="text" class="form-control form-control-sm" wire:model="new_demandeur_name" placeholder="ex: Jean Dupont">
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
                            <label class="form-label">Destination</label>
                            <div class="input-group">
                                <input type="text" class="form-control" wire:model="destination" placeholder="ex: Bureau principal">
                                <select class="form-select" style="max-width: 200px;" wire:model="city_id">
                                    <option value="">Ville...</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
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
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" wire:click="$set('showFormModal', false)">Annuler</button>
                    <button type="submit" class="btn btn-primary">{{ $editingId ? 'Enregistrer' : 'Créer' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
