@if($showFormModal)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $editingId ? 'Modifier la réparation' : 'Nouvelle réparation' }}</h5>
                <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
            </div>
            <form wire:submit="saveRepair">
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
                            <label class="form-label">Garage <span class="text-danger">*</span></label>
                            <select class="form-select @error('garage_id') is-invalid @enderror" wire:model="garage_id">
                                <option value="">—</option>
                                @foreach($garages as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                            @error('garage_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select class="form-select" wire:model.live="type">
                                <option value="internal">Interne</option>
                                <option value="external">Externe (fiche transfert obligatoire)</option>
                            </select>
                        </div>
                        @if($type === 'external')
                            <div class="col-md-6">
                                <label class="form-label">Fiche de transfert</label>
                                <input type="file" class="form-control" wire:model="transfer_sheet" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        @endif
                        <div class="col-12">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" rows="3" wire:model="description"></textarea>
                            @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Coût (F)</label>
                            <input type="text" class="form-control" wire:model="cost" placeholder="ex. 1 234 567,89" inputmode="decimal">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Début</label>
                            <input type="date" class="form-control" wire:model="started_at">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fin</label>
                            <input type="date" class="form-control" wire:model="completed_at">
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
