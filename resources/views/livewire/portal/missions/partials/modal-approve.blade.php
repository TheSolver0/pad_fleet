@if($showApproveModal)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approbation</h5>
                <button type="button" class="btn-close" wire:click="$set('showApproveModal', false)"></button>
            </div>
            <div class="modal-body">
                <div class="form-check mb-2">
                    <input type="radio" class="form-check-input" id="approve_yes" wire:model="approve_reject" value="1">
                    <label class="form-check-label" for="approve_yes">Approuver la mission</label>
                </div>
                <div class="form-check">
                    <input type="radio" class="form-check-input" id="approve_no" wire:model="approve_reject" value="0">
                    <label class="form-check-label" for="approve_no">Refuser la mission</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" wire:click="$set('showApproveModal', false)">Annuler</button>
                <button type="button" class="btn btn-primary" wire:click="approveOrReject">Confirmer</button>
            </div>
        </div>
    </div>
</div>
@endif
