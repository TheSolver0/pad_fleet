@if($showDocumentModal)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Documents de mission</h5>
                <button type="button" class="btn-close" wire:click="$set('showDocumentModal', false)"></button>
            </div>
            <form wire:submit="saveDocuments">
                <div class="modal-body">
                    @php
                        $mission = \App\Models\Mission::find($documentMissionId);
                        $existingDocuments = $mission ? $mission->documents()->orderBy('sort_order')->get() : collect();
                    @endphp

                    @if($existingDocuments->count() > 0)
                        <div class="mb-3">
                            <h6>Documents existants</h6>
                            <div class="row g-2">
                                @foreach($existingDocuments as $document)
                                    <div class="col-md-6" wire:key="mission-doc-{{ $document->id }}">
                                        <div class="card">
                                            <div class="card-body p-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold small">{{ $document->original_name }}</div>
                                                        <div class="text-muted small">
                                                            {{ $document->document_type_label }} • {{ $document->formatted_file_size }}
                                                        </div>
                                                        @if($document->caption)
                                                            <div class="text-info small"><i class="bi bi-chat-left-text me-1"></i>{{ $document->caption }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="ms-2">
                                                        @if($document->file_type === 'image')
                                                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-file-earmark-pdf"></i>
                                                            </a>
                                                        @endif
                                                        <button type="button" class="btn btn-sm btn-outline-danger ms-1" wire:click="deleteDocument({{ $document->id }})">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Type de document</label>
                        <select class="form-select" wire:model="document_type">
                            <option value="ordre_mission">Ordre de mission</option>
                            <option value="rapport">Rapport de mission</option>
                            <option value="facture">Facture</option>
                            <option value="recu">Reçu</option>
                            <option value="autre">Autre document</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Ajouter un document</label>
                        <div class="row g-2">
                            <div class="col-md-5">
                                <select class="form-select form-select-sm" wire:model="document_type">
                                    <option value="ordre_mission">Ordre de mission</option>
                                    <option value="rapport">Rapport de mission</option>
                                    <option value="facture">Facture</option>
                                    <option value="recu">Reçu</option>
                                    <option value="autre">Autre document</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control form-control-sm" wire:model="document_note"
                                    placeholder="Note de validation (optionnel)">
                            </div>
                            <div class="col-12">
                                <input type="file" class="form-control form-control-sm" wire:model="documents" multiple accept=".pdf,image/*">
                                @error('documents.*') <span class="text-danger small">{{ $message }}</span> @enderror
                                <div class="form-text">PDF, JPG, PNG — 10 Mo max par fichier.</div>
                            </div>
                        </div>
                    </div>

                    @if($documents)
                        <div class="mb-3">
                            <h6>Fichiers à uploader</h6>
                            <div class="row g-2">
                                @foreach($documents as $index => $document)
                                    <div class="col-md-6" wire:key="mission-doc-pending-{{ $index }}">
                                        <div class="card">
                                            <div class="card-body p-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-file-earmark me-2"></i>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold small">{{ $document->getClientOriginalName() }}</div>
                                                        <div class="text-muted small">{{ number_format($document->getSize() / 1024, 1) }} KB</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" wire:click="$set('showDocumentModal', false)">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les documents</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif