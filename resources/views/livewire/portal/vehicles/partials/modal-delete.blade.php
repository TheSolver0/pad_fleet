@if($showDeleteModal)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce véhicule ? Les documents et l'historique associés seront également supprimés.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" wire:click="$set('showDeleteModal', false)">Annuler</button>
                <button type="button" class="btn btn-danger" wire:click="deleteVehicle">Supprimer</button>
            </div>
        </div>
    </div>
</div>
@endif
