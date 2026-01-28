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
                            <label class="form-label">Demandeur <span class="text-danger">*</span></label>
                            <select class="form-select @error('demandeur_id') is-invalid @enderror" wire:model="demandeur_id">
                                <option value="">—</option>
                                @foreach($demandeurs as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                            @error('demandeur_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Destination</label>
                            <input type="text" class="form-control" wire:model="destination">
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
