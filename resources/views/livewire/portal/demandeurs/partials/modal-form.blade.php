@if($showFormModal)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $editingId ? 'Modifier le demandeur' : 'Nouveau demandeur' }}</h5>
                <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
            </div>
            <form wire:submit="saveDemandeur">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Matricule</label>
                            <input type="text" class="form-control" wire:model="matricule">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type de demandeur <span class="text-danger">*</span></label>
                            <select class="form-select @error('demandeur_type') is-invalid @enderror" wire:model.live="demandeur_type">
                                <option value="person">Agent PAD</option>
                                <option value="direction">Direction</option>
                                <option value="particulier">Particulier (personne externe)</option>
                            </select>
                            @error('demandeur_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        @if($demandeur_type === 'person')
                            <div class="col-md-6">
                                <label class="form-label">Personne liée <span class="text-danger">*</span></label>
                                <select class="form-select @error('person_id') is-invalid @enderror" wire:model="person_id">
                                    <option value="">—</option>
                                    @foreach($persons as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                @error('person_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        @elseif($demandeur_type === 'direction')
                            <div class="col-md-6">
                                <label class="form-label">Direction liée <span class="text-danger">*</span></label>
                                <select class="form-select @error('direction_id') is-invalid @enderror" wire:model="direction_id">
                                    <option value="">—</option>
                                    @foreach($directions as $dir)
                                        <option value="{{ $dir->id }}">{{ $dir->name }}</option>
                                    @endforeach
                                </select>
                                @error('direction_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        @else
                            <div class="col-md-6">
                                <p class="small text-muted mb-0 mt-4">Personne externe au PAD — renseignez simplement son nom et ses coordonnées ci-dessous.</p>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label">Service</label>
                            <select class="form-select" wire:model="service_id">
                                <option value="">—</option>
                                @foreach($services as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="text" class="form-control" wire:model="contact_phone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" wire:model="contact_email">
                            @error('contact_email') <span class="invalid-feedback">{{ $message }}</span> @enderror
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
