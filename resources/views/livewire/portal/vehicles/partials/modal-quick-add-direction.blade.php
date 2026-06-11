@if ($showQuickAddDirection)
    <div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.35); z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title">Ajouter une direction</h6>
                    <button type="button" class="btn-close btn-sm" wire:click="closeQuickAddDirection"></button>
                </div>
                <form wire:submit="saveQuickDirection">
                    <div class="modal-body py-3">
                        <div class="mb-2">
                            <label class="form-label small">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" wire:model="quick_direction_name" placeholder="ex. Direction générale">
                            @error('quick_direction_name')
                                <span class="small text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label small">Code</label>
                            <input type="text" class="form-control form-control-sm" wire:model="quick_direction_code" placeholder="ex. DG">
                            @error('quick_direction_code')
                                <span class="small text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="closeQuickAddDirection">Annuler</button>
                        <button type="submit" class="btn btn-sm btn-primary">Ajouter et sélectionner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
