@if($showFormModal)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $editingId ? 'Modifier la réparation' : 'Nouvelle réparation' }}</h5>
                <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
            </div>
            <form wire:submit="saveRepair">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Véhicule <span class="text-danger">*</span></label>
                            <select class="form-select @error('vehicle_id') is-invalid @enderror" wire:model="vehicle_id">
                                <option value="">—</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->registration }}</option>
                                @endforeach
                            </select>
                            @error('vehicle_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Garage <span class="text-danger">*</span></label>
                            <select class="form-select @error('garage_id') is-invalid @enderror" wire:model="garage_id">
                                <option value="">—</option>
                                @foreach($garages as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                            @error('garage_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select class="form-select" wire:model.live="type">
                                <option value="internal">Interne</option>
                                <option value="external">Externe (fiche transfert obligatoire)</option>
                            </select>
                        </div>
                        @if($type === 'external')
                            <div class="col-md-6">
                                <label class="form-label">Fiche de transfert</label>
                                <input type="file" class="form-control" wire:model="transfer_sheet" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        @endif
                        <div class="col-12">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" rows="3" wire:model="description"></textarea>
                            @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Nouvelle rubrique pour les réparations -->
                        <div class="col-md-4">
                            <label class="form-label">Type de réparation <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('repair_type') is-invalid @enderror" wire:model="repair_type" placeholder="ex: Freinage, Moteur, Carrosserie">
                            @error('repair_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Priorité</label>
                            <select class="form-select @error('priority') is-invalid @enderror" wire:model="priority">
                                <option value="low">Basse</option>
                                <option value="medium">Moyenne</option>
                                <option value="high">Haute</option>
                                <option value="urgent">Urgente</option>
                            </select>
                            @error('priority') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Durée estimée</label>
                            <input type="text" class="form-control @error('estimated_duration') is-invalid @enderror" wire:model="estimated_duration" placeholder="ex: 2 jours, 4 heures">
                            @error('estimated_duration') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mécanicien assigné</label>
                            <select class="form-select @error('mechanic_id') is-invalid @enderror" wire:model="mechanic_id">
                                <option value="">Non assigné</option>
                                @foreach($mechanics as $m)
                                    <option value="{{ $m->id }}">{{ $m->last_name }} {{ $m->first_name }} ({{ $m->specialization }})</option>
                                @endforeach
                            </select>
                            @error('mechanic_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Coût (F)</label>
                            <input type="text" class="form-control" wire:model="cost" placeholder="ex. 1 234 567,89" inputmode="decimal">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Début</label>
                            <input type="date" class="form-control" wire:model="started_at">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fin (réelle)</label>
                            <input type="date" class="form-control" wire:model="completed_at">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date limite donnée au prestataire</label>
                            <input type="date" class="form-control" wire:model="expected_completed_at" placeholder="Délai qu'on lui a donné">
                            <small class="text-muted">Délai qu'on lui a donné pour rendre le véhicule</small>
                        </div>
                        {{-- Évaluation prestation : qualité, délai réalisé --}}
                        <div class="col-12 border-top pt-3">
                            <h6 class="text-muted mb-2"><i class="bi bi-star me-1"></i> Évaluation de la prestation</h6>
                            <p class="small text-muted">Qualité du travail et respect du délai (véhicule confié au prestataire).</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Qualité du travail (1 à 5)</label>
                            <select class="form-select" wire:model="quality_rating">
                                <option value="">— Non évalué</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} — {{ $i <= 2 ? 'Insuffisant' : ($i == 3 ? 'Correct' : ($i == 4 ? 'Bien' : 'Très bien')) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Respect du délai (1 à 5)</label>
                            <select class="form-select" wire:model="delay_rating">
                                <option value="">— Non évalué</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Commentaire évaluation</label>
                            <textarea class="form-control" rows="2" wire:model="evaluation_comment" placeholder="Remarques sur la prestation..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                        </div>

                        @if($type === 'external')
                            <div class="col-12 border-top pt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="text-muted mb-0"><i class="bi bi-receipt me-1"></i> Dépenses externes + pièces jointes</h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addExpenseLine">
                                        <i class="bi bi-plus-lg me-1"></i>Ajouter
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Libellé</th>
                                                <th style="width:130px">Montant (F)</th>
                                                <th>Note</th>
                                                <th style="width:220px">Pièce jointe</th>
                                                <th style="width:60px"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($expense_lines as $i => $line)
                                                <tr>
                                                    <td><input type="text" class="form-control form-control-sm" wire:model="expense_lines.{{ $i }}.label"></td>
                                                    <td><input type="text" class="form-control form-control-sm" wire:model="expense_lines.{{ $i }}.amount"></td>
                                                    <td><input type="text" class="form-control form-control-sm" wire:model="expense_lines.{{ $i }}.notes"></td>
                                                    <td>
                                                        <input type="file" class="form-control form-control-sm" wire:model="expense_files.{{ $i }}" accept=".pdf,.jpg,.jpeg,.png">
                                                        @if(!empty($line['existing_attachment_path']))
                                                            <a class="small" href="{{ asset('storage/'.$line['existing_attachment_path']) }}" target="_blank">{{ $line['existing_attachment_name'] ?? 'Pièce existante' }}</a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(count($expense_lines) > 1)
                                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeExpenseLine({{ $i }})"><i class="bi bi-trash"></i></button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
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
