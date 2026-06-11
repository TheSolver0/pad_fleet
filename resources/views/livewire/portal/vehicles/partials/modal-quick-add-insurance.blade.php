@if ($showQuickAddInsurance)
    <div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.35); z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title">Configurer l'assurance du véhicule</h6>
                    <button type="button" class="btn-close btn-sm" wire:click="closeQuickAddInsurance"></button>
                </div>
                <form wire:submit="saveQuickInsurance">
                    <div class="modal-body py-3">
                        <ul class="nav nav-tabs nav-tabs-sm mb-3">
                            <li class="nav-item">
                                <button type="button" class="nav-link {{ $insurance_mode === 'existing' ? 'active' : '' }}" wire:click="$set('insurance_mode', 'existing')">
                                    Rattacher un marché existant
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link {{ $insurance_mode === 'new' ? 'active' : '' }}" wire:click="$set('insurance_mode', 'new')">
                                    Créer un nouveau marché
                                </button>
                            </li>
                        </ul>

                        @if($insurance_mode === 'existing')
                            <div class="mb-3">
                                <label class="form-label small">Marché assurance <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm @error('insurance_contract_global_id') is-invalid @enderror" wire:model="insurance_contract_global_id">
                                    <option value="">— Choisir un marché —</option>
                                    @foreach($contracts as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->start_date->format('d/m/Y') }} → {{ $c->end_date->format('d/m/Y') }})</option>
                                    @endforeach
                                </select>
                                @error('insurance_contract_global_id')
                                    <span class="small text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @else
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small">Nom du marché <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" wire:model="ins_new_name">
                                    @error('ins_new_name') <span class="small text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Assureur <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm" wire:model="ins_new_assureur_id">
                                        <option value="">— Choisir —</option>
                                        @foreach($assureurs as $assureur)
                                            <option value="{{ $assureur->id }}">{{ $assureur->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('ins_new_assureur_id') <span class="small text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Date de début <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-sm" wire:model.live="ins_new_start_date">
                                    @error('ins_new_start_date') <span class="small text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Date de fin <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-sm" wire:model.live="ins_new_end_date">
                                    @error('ins_new_end_date') <span class="small text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Statut (calculé)</label>
                                    <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ $this->insuranceStatusPreview ?: '—' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Description du lot</label>
                                    <input type="text" class="form-control form-control-sm" wire:model="ins_new_lot_description">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Notes</label>
                                    <textarea class="form-control form-control-sm" rows="2" wire:model="ins_new_notes"></textarea>
                                </div>
                            </div>
                        @endif

                        <hr class="my-3">
                        <p class="small text-muted mb-2">Attestation / certificat d'assurance du véhicule (optionnel)</p>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small">Fichier</label>
                                <input type="file" class="form-control form-control-sm" wire:model="ins_doc_file" accept=".pdf,.jpg,.jpeg,.png">
                                @error('ins_doc_file') <span class="small text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Échéance attestation</label>
                                <input type="date" class="form-control form-control-sm" wire:model="ins_doc_expires_at">
                                @error('ins_doc_expires_at') <span class="small text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="closeQuickAddInsurance">Annuler</button>
                        <button type="submit" class="btn btn-sm btn-primary">Enregistrer l'assurance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
