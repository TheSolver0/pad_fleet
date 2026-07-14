@if($showDocModal && $docVehicle)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Documents, photos & carte grise — {{ $docVehicle->registration }}</h5>
                <button type="button" class="btn-close" wire:click="closeDocModal"></button>
            </div>
            <div class="modal-body">
                {{-- Documents (assurance, autre) --}}
                <section class="mb-4">
                    <h6 class="fw-600 text-muted mb-2"><i class="bi bi-file-earmark-pdf me-1"></i> Documents</h6>
                    <form wire:submit="uploadDocument" class="row g-2 align-items-end mb-2">
                        <div class="col-md-3">
                            <label class="form-label small">Type</label>
                            <select class="form-select form-select-sm" wire:model="doc_type">
                                <option value="assurance">Assurance</option>
                                <option value="carte_grise">Carte grise (scan)</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Échéance</label>
                            <input type="date" class="form-control form-control-sm" wire:model="doc_expires_at">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Fichier</label>
                            <input type="file" class="form-control form-control-sm" wire:model="doc_file" accept=".pdf,.jpg,.jpeg,.png">
                            @error('doc_file') <span class="small text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100">Ajouter</button>
                        </div>
                    </form>
                    <ul class="list-group list-group-flush small">
                        @forelse($docVehicle->documents as $doc)
                            <li wire:key="moto-doc-{{ $doc->id }}" class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <span><span class="badge bg-light text-dark me-2">{{ $doc->type_label }}</span> {{ $doc->original_name ?: basename($doc->file_path) }} @if($doc->expires_at)<span class="text-muted">— Expire {{ $doc->expires_at->format('d/m/Y') }}</span>@endif</span>
                                <span>
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i></a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteDocument({{ $doc->id }})"><i class="bi bi-trash"></i></button>
                                </span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted py-2">Aucun document.</li>
                        @endforelse
                    </ul>
                </section>

                {{-- Photos --}}
                <section class="mb-4">
                    <h6 class="fw-600 text-muted mb-2"><i class="bi bi-images me-1"></i> Photos de la moto</h6>
                    <form wire:submit="uploadPhoto" class="row g-2 align-items-end mb-2">
                        <div class="col-md-3">
                            <label class="form-label small">Photo</label>
                            <input type="file" class="form-control form-control-sm" wire:model="photo_file" accept="image/*">
                            @error('photo_file') <span class="small text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Légende</label>
                            <input type="text" class="form-control form-control-sm" wire:model="photo_caption" placeholder="ex. Vue avant">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Période / Date prise</label>
                            <input type="date" class="form-control form-control-sm" wire:model="photo_taken_at">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100">Ajouter photo</button>
                        </div>
                    </form>
                    <div class="row g-2">
                        @foreach($docVehicle->photos as $photo)
                            <div class="col-6 col-md-4 col-lg-3" wire:key="moto-photo-{{ $photo->id }}">
                                <div class="position-relative border rounded overflow-hidden bg-light" style="height: 120px;">
                                    <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $photo->caption }}" class="w-100 h-100 object-fit-cover">
                                    @if($photo->caption || $photo->taken_at)
                                        <span class="position-absolute bottom-0 start-0 end-0 small bg-dark bg-opacity-75 text-white px-2 py-1 text-truncate">{{ $photo->caption }}{{ $photo->caption && $photo->taken_at ? ' — ' : '' }}{{ $photo->taken_at?->format('d/m/Y') }}</span>
                                    @endif
                                    <button type="button" class="position-absolute top-0 end-0 btn btn-sm btn-danger m-1" wire:click="deletePhoto({{ $photo->id }})" title="Supprimer"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($docVehicle->photos->isEmpty())
                        <p class="small text-muted mb-0">Aucune photo. Ajoutez des photos de la moto ci-dessus.</p>
                    @endif
                </section>

                {{-- Historique cartes grises --}}
                <section>
                    <h6 class="fw-600 text-muted mb-2"><i class="bi bi-card-checklist me-1"></i> Historique des cartes grises</h6>
                    <p class="small text-muted">Enregistrez chaque carte grise avec son ID / récépissé, dates de délivrance et d'expiration, et un scan si besoin.</p>
                    <form wire:submit="addCarteGrise" class="row g-2 align-items-end mb-3">
                        <div class="col-md-2">
                            <label class="form-label small">N° récép. / ID</label>
                            <input type="text" class="form-control form-control-sm" wire:model="cg_reference_number" placeholder="ex. 12345">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Délivrance</label>
                            <input type="date" class="form-control form-control-sm" wire:model="cg_issued_at">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Expiration</label>
                            <input type="date" class="form-control form-control-sm" wire:model="cg_expires_at">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Scan (optionnel)</label>
                            <input type="file" class="form-control form-control-sm" wire:model="cg_file" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Notes / Détails</label>
                            <input type="text" class="form-control form-control-sm" wire:model="cg_notes" placeholder="Autres détails">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100">Enregistrer</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>N° récép. / ID</th>
                                    <th>Délivrance</th>
                                    <th>Expiration</th>
                                    <th>Notes</th>
                                    <th>Fichier</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($docVehicle->carteGrises as $cg)
                                    <tr wire:key="moto-cg-{{ $cg->id }}">
                                        <td>{{ $cg->reference_number ?? '—' }}</td>
                                        <td>{{ $cg->issued_at?->format('d/m/Y') ?? '—' }}</td>
                                        <td>{{ $cg->expires_at?->format('d/m/Y') ?? '—' }}</td>
                                        <td class="small">{{ \Str::limit($cg->notes, 40) ?? '—' }}</td>
                                        <td>
                                            @if($cg->file_path)
                                                <a href="{{ asset('storage/' . $cg->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i></a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteCarteGrise({{ $cg->id }})"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-muted text-center py-3">Aucune carte grise enregistrée.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeDocModal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endif
