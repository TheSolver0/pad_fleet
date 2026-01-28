@if($showAssignmentModal)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $editingVehicleId ? 'Modifier l\'affectation' : 'Nouvelle affectation' }}</h5>
                <button type="button" class="btn-close" wire:click="closeAssignmentModal"></button>
            </div>
            <form wire:submit="saveAssignment">
                <div class="modal-body">
                    <div class="row g-3">
                        @if($editingVehicleId && $editingVehicle)
                            <div class="col-12">
                                <label class="form-label small text-muted">Véhicule</label>
                                <p class="mb-0 fw-medium">{{ $editingVehicle->registration }} — {{ $editingVehicle->vehicleModel?->full_name ?? '—' }}</p>
                            </div>
                        @else
                            <div class="col-12">
                                <label class="form-label">Véhicule <span class="text-danger">*</span></label>
                                <select class="form-select @error('assignment_vehicle_id') is-invalid @enderror" wire:model.live="assignment_vehicle_id">
                                    <option value="">— Choisir un véhicule —</option>
                                    @foreach($vehicles as $v)
                                        <option value="{{ $v->id }}">{{ $v->registration }} — {{ $v->vehicleModel?->full_name ?? '—' }}</option>
                                    @endforeach
                                </select>
                                @error('assignment_vehicle_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        @endif
                        <div class="col-12">
                            <label class="form-label">Assigné à</label>
                            <div class="input-group">
                                <select class="form-select @error('assignment_person_id') is-invalid @enderror" wire:model.live="assignment_person_id">
                                    <option value="">— Aucune affectation —</option>
                                    @foreach($persons as $person)
                                        <option value="{{ $person->id }}">{{ $person->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary" wire:click="openQuickAddPerson" title="Ajouter une personne">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                            @error('assignment_person_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Type d'affectation</label>
                            <select class="form-select" wire:model.live="assignment_type">
                                <option value="">— Choisir —</option>
                                @foreach(\App\Models\Vehicle::assignmentTypeOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Période</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="affPeriodIndefinite" wire:model.live="assignment_period_indefinite">
                                <label class="form-check-label" for="affPeriodIndefinite">Indéfinie (ex. dotation)</label>
                            </div>
                        </div>
                        @if(!$assignment_period_indefinite)
                        <div class="col-md-6">
                            <label class="form-label">Début</label>
                            <input type="date" class="form-control" wire:model="assignment_start_at">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fin</label>
                            <input type="date" class="form-control" wire:model="assignment_end_at">
                        </div>
                        @else
                        <div class="col-md-6">
                            <label class="form-label">À partir du (optionnel)</label>
                            <input type="date" class="form-control" wire:model="assignment_start_at">
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" wire:click="closeAssignmentModal">Annuler</button>
                    <button type="submit" class="btn btn-primary">{{ $editingVehicleId ? 'Enregistrer' : 'Créer' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
