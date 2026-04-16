@if ($showQuickAddPerson)
    <div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.35); z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title">Ajouter une personne</h6>
                    <button type="button" class="btn-close btn-sm" wire:click="closeQuickAddPerson"></button>
                </div>
                <form wire:submit="saveQuickPerson">
                    <div class="modal-body py-3">
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label small">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" wire:model="quick_name"
                                    placeholder="Nom complet">
                                @error('quick_name')
                                    <span class="small text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label small">Email</label>
                                <input type="email" class="form-control form-control-sm" wire:model="quick_email"
                                    placeholder="email@exemple.com">
                                @error('quick_email')
                                    <span class="small text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Téléphone</label>
                                <input type="text" class="form-control form-control-sm" wire:model="quick_phone"
                                    placeholder="+237...">
                            </div>
                                <div class="col-6">
                                    <label class="form-label small">Département</label>
                                    <select class="form-control form-control-sm"  wire:model="quick_department_id">
                                        <option value="">— Aucun département —</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            wire:click="closeQuickAddPerson">Annuler</button>
                        <button type="submit" class="btn btn-sm btn-primary">Ajouter et sélectionner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
