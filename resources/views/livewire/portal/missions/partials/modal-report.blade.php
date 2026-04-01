@if($showReportModal)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rapport des déplacements</h5>
                <button type="button" class="btn-close" wire:click="$set('showReportModal', false)"></button>
            </div>
            <form wire:submit="exportReport">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Date de début <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('report_start_date') is-invalid @enderror" wire:model="report_start_date">
                            @error('report_start_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date de fin <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('report_end_date') is-invalid @enderror" wire:model="report_end_date">
                            @error('report_end_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Statut des missions</label>
                            <select class="form-select" wire:model="report_status">
                                <option value="">Tous les statuts</option>
                                <option value="pending">En attente</option>
                                <option value="approved">Approuvée</option>
                                <option value="rejected">Refusée</option>
                                <option value="completed">Terminée</option>
                                <option value="cancelled">Annulée</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Demandeur</label>
                            <select class="form-select" wire:model="report_demandeur_id">
                                <option value="">Tous les demandeurs</option>
                                @foreach($demandeurs as $demandeur)
                                    <option value="{{ $demandeur->id }}">{{ $demandeur->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="mb-2">Informations incluses dans le rapport :</h6>
                        <ul class="mb-0 small">
                            <li>ID Mission</li>
                            <li>Statut de la mission</li>
                            <li>Demandeur et service/direction</li>
                            <li>Chauffeur assigné</li>
                            <li>Véhicule utilisé</li>
                            <li>Dates de départ et retour</li>
                            <li>Destination et ville</li>
                            <li>Kilométrage (départ, retour, distance)</li>
                            <li>Notes et date de création</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" wire:click="$set('showReportModal', false)">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-download me-1"></i> Générer le rapport Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif