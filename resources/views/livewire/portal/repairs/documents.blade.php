<div>
    <p class="section-label">Documents de réparation — Diagnostics, devis, factures, rapports techniques</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Documents réparations</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width:220px"
                        placeholder="Titre, véhicule, description..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width:160px" wire:model.live="status_filter">
                        <option value="">Tous statuts</option>
                        <option value="pending">En attente</option>
                        <option value="physical">Validé (physique)</option>
                        <option value="digital">Validé (numérique)</option>
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate()">
                        <i class="bi bi-plus-lg me-1"></i>Ajouter un document
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Véhicule</th>
                        <th>Réparation</th>
                        <th>Type</th>
                        <th>Titre</th>
                        <th>Fichier</th>
                        <th>Validation</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                    <tr>
                        <td class="fw-semibold">{{ $doc->repair?->vehicle?->registration ?? '—' }}</td>
                        <td><small class="text-muted">#{{ $doc->repair_id }}</small></td>
                        <td>
                            @php
                                $typeColors = [
                                    'diagnostic'       => 'primary',
                                    'estimate'         => 'warning',
                                    'invoice'          => 'success',
                                    'technical_report' => 'info',
                                    'other'            => 'secondary',
                                ];
                                $typeLabels = [
                                    'diagnostic'       => 'Diagnostic',
                                    'estimate'         => 'Devis',
                                    'invoice'          => 'Facture',
                                    'technical_report' => 'Rapport tech.',
                                    'other'            => 'Autre',
                                ];
                            @endphp
                            <span class="badge bg-{{ $typeColors[$doc->document_type] ?? 'secondary' }}">
                                {{ $typeLabels[$doc->document_type] ?? $doc->document_type }}
                            </span>
                        </td>
                        <td>
                            <div style="font-size:.87rem">{{ $doc->title }}</div>
                            @if($doc->description)
                                <small class="text-muted">{{ Str::limit($doc->description, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ $doc->original_filename ?? '—' }}
                                @if($doc->file_size)
                                    <span class="ms-1 text-muted">({{ $doc->formatted_file_size }})</span>
                                @endif
                            </small>
                        </td>
                        <td>
                            @if($doc->digitally_validated)
                                <span class="badge bg-success">Validé numérique</span>
                                @if($doc->digitallyValidatedBy)
                                    <br><small class="text-muted">{{ $doc->digitallyValidatedBy->name }}</small>
                                @endif
                            @elseif($doc->physically_validated)
                                <span class="badge bg-warning text-dark">Validé physique</span>
                                @if($doc->physicallyValidatedBy)
                                    <br><small class="text-muted">{{ $doc->physicallyValidatedBy->name }}</small>
                                @endif
                            @else
                                <span class="badge bg-secondary">En attente</span>
                            @endif
                        </td>
                        <td><small>{{ $doc->created_at->format('d/m/Y') }}</small></td>
                        <td class="text-end">
                            <a href="{{ $doc->file_url }}" target="_blank"
                                class="btn btn-sm btn-outline-primary" title="Télécharger / Voir">
                                <i class="bi bi-download"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-info"
                                wire:click="openValidation({{ $doc->id }})" title="Valider">
                                <i class="bi bi-shield-check"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                wire:click="deleteDocument({{ $doc->id }})"
                                wire:confirm="Supprimer ce document définitivement ?">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Aucun document.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($documents->hasPages())
            <div class="p-3 border-top">{{ $documents->links() }}</div>
        @endif
    </div>

    {{-- MODAL AJOUT DOCUMENT --}}
    @if($showFormModal)
    <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.45);">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-file-earmark-plus me-2"></i>Ajouter un document de réparation</h5>
                    <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
                </div>
                <form wire:submit="saveDocument" novalidate>
                    <div class="modal-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Réparation <span class="text-danger">*</span></label>
                                <select class="form-select @error('repair_id') is-invalid @enderror" wire:model="repair_id">
                                    <option value="">— Sélectionner une réparation —</option>
                                    @foreach($repairs as $r)
                                        <option value="{{ $r->id }}">
                                            #{{ $r->id }} — {{ $r->vehicle?->registration ?? 'Véh. inconnu' }}
                                            ({{ $r->created_at->format('d/m/Y') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('repair_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Type de document <span class="text-danger">*</span></label>
                                <select class="form-select @error('document_type') is-invalid @enderror" wire:model="document_type">
                                    <option value="diagnostic">Diagnostic</option>
                                    <option value="estimate">Devis</option>
                                    <option value="invoice">Facture</option>
                                    <option value="technical_report">Rapport technique</option>
                                    <option value="other">Autre</option>
                                </select>
                                @error('document_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Titre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    wire:model="title" placeholder="ex: Devis réparation moteur véhicule XYZ">
                                @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" rows="2" wire:model="description"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Fichier <span class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('document_file') is-invalid @enderror"
                                    wire:model="document_file"
                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                <div class="form-text">PDF, images, Word, Excel — max 10 Mo</div>
                                @error('document_file') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                <div wire:loading wire:target="document_file" class="text-muted small mt-1">
                                    <i class="bi bi-hourglass-split me-1"></i>Chargement…
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" wire:click="$set('showFormModal', false)">Annuler</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="bi bi-cloud-upload me-1"></i>Enregistrer</span>
                            <span wire:loading><i class="bi bi-hourglass-split me-1"></i>Envoi…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL VALIDATION --}}
    @if($showValidationModal)
    <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.45);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-shield-check me-2"></i>Valider le document</h5>
                    <button type="button" class="btn-close" wire:click="$set('showValidationModal', false)"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" wire:model="physically_validated" id="physVal">
                            <label class="form-check-label" for="physVal">Validation physique (document papier reçu)</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="digitally_validated" id="digVal">
                            <label class="form-check-label" for="digVal">Validation numérique (fichier vérifié)</label>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Notes de validation</label>
                        <textarea class="form-control" rows="2" wire:model="validation_notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" wire:click="$set('showValidationModal', false)">Annuler</button>
                    <button type="button" class="btn btn-success" wire:click="saveValidation">
                        <i class="bi bi-check-lg me-1"></i>Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
