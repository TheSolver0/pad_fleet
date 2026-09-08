@if($showCompteRenduModal)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Compte-rendu de mission</h5>
                <button type="button" class="btn-close" wire:click="closeCompteRenduModal"></button>
            </div>
            <form wire:submit="saveCompteRendu">
                <div class="modal-body">
                    <label class="form-label">Compte-rendu</label>
                    <textarea class="form-control @error('compte_rendu') is-invalid @enderror" rows="6" wire:model="compte_rendu" placeholder="Déroulement de la mission, observations, incidents..."></textarea>
                    @error('compte_rendu') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" wire:click="closeCompteRenduModal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
