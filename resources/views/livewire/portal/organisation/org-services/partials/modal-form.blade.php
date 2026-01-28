@if($showFormModal)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{ $editingId ? 'Modifier le service' : 'Nouveau service' }}</h5>
            <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
        </div>
        <form wire:submit="saveOrgService">
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Département <span class="text-danger">*</span></label>
                    <select class="form-select @error('department_id') is-invalid @enderror" wire:model.live="department_id">
                        <option value="">— Choisir —</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->direction?->name ?? '' }} — {{ $dept->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Code</label>
                    <input type="text" class="form-control" wire:model="code" placeholder="ex: SIV">
                </div>
                <div class="mb-0">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" wire:click="$set('showFormModal', false)">Annuler</button>
                <button type="submit" class="btn btn-primary">{{ $editingId ? 'Enregistrer' : 'Créer' }}</button>
            </div>
        </form>
    </div></div>
</div>
@endif
