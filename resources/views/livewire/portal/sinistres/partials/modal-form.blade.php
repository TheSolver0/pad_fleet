@if($showFormModal)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $editingId ? 'Modifier le sinistre' : 'Déclarer un sinistre' }}</h5>
                <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
            </div>
            <form wire:submit="saveSinistre">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Véhicule <span class="text-danger">*</span></label>
                            <select class="form-select @error('vehicle_id') is-invalid @enderror" wire:model.live="vehicle_id">
                                <option value="">—</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->registration }}</option>
                                @endforeach
                            </select>
                            @error('vehicle_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mission (optionnel)</label>
                            <select class="form-select" wire:model="mission_id">
                                <option value="">—</option>
                                @foreach($missions as $m)
                                    <option value="{{ $m->id }}">#{{ $m->id }} {{ $m->date_start->format('d/m/Y') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date/heure déclaration <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('declared_at') is-invalid @enderror" wire:model="declared_at">
                            @error('declared_at') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Coût estimé (F)</label>
                            <input type="text" class="form-control" wire:model="estimated_cost" placeholder="ex. 1 234 567,89" inputmode="decimal">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" rows="3" wire:model="description"></textarea>
                            @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Lieu</label>
                            <input type="text" class="form-control" wire:model="location" placeholder="Lieu du sinistre">
                            @error('location') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Responsabilité</label>
                            <input type="text" class="form-control" wire:model="responsibility" placeholder="tiers, nous, partagé...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assureur</label>
                            <select class="form-select" wire:model="assureur_id">
                                <option value="">—</option>
                                @foreach($assureurs as $a)
                                    <option value="{{ $a->id }}">{{ $a->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Garage (affectation a posteriori)</label>
                            <select class="form-select" wire:model="garage_id">
                                <option value="">—</option>
                                @foreach($garages as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rapport de police (PDF / image)</label>
                            <input type="file" class="form-control" wire:model="police_report_file" accept=".pdf,image/*">
                            @error('police_report_file') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Statut</label>
                            <select class="form-select" wire:model="status">
                                <option value="declared">Déclaré</option>
                                <option value="in_repair">En réparation</option>
                                <option value="closed">Clôturé</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" wire:click="$set('showFormModal', false)">Annuler</button>
                    <button type="submit" class="btn btn-primary">{{ $editingId ? 'Enregistrer' : 'Déclarer' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
