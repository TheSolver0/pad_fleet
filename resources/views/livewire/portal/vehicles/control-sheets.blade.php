<div>
    <p class="section-label">Fiche de contrôle véhicule — Avant et après mission</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Fiches de contrôle</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width:200px"
                        placeholder="Immatriculation..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i>Nouvelle fiche
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Véhicule</th>
                        <th>Chauffeur</th>
                        <th>N° OM</th>
                        <th>Lieu / Mission</th>
                        <th>Départ</th>
                        <th>Retour</th>
                        <th class="text-center">KM</th>
                        <th class="text-center">Photos</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sheets as $s)
                    <tr>
                        <td class="fw-semibold">{{ $s->vehicle?->registration ?? '—' }}</td>
                        <td>{{ $s->driver ? $s->driver->full_name : '—' }}</td>
                        <td><small class="text-muted">{{ $s->ordre_mission ?? '—' }}</small></td>
                        <td>{{ $s->lieu ?? ($s->mission?->destination ?? '—') }}</td>
                        <td>{{ $s->date_depart?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ $s->date_retour?->format('d/m/Y') ?? '—' }}</td>
                        <td class="text-center small">
                            {{ $s->km_depart ?? '—' }} → {{ $s->km_retour ?? '—' }}
                        </td>
                        <td class="text-center">
                            @if($s->photos_count > 0)
                                <span class="badge bg-info">{{ $s->photos_count }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary" wire:click="openView({{ $s->id }})" title="Voir la fiche">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" wire:click="openPhotoModal({{ $s->id }})" title="Photos avant/après">
                                <i class="bi bi-camera"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" wire:click="openEdit({{ $s->id }})" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $s->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">Aucune fiche de contrôle.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sheets->hasPages())
            <div class="p-3 border-top">{{ $sheets->links() }}</div>
        @endif
    </div>

    {{-- ── MODAL FORMULAIRE FICHE ── --}}
    @if($showFormModal)
    <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.45);">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background:rgba(26,84,144,.08)">
                    <h5 class="modal-title"><i class="bi bi-clipboard-check me-2"></i>
                        {{ $editingId ? 'Modifier la fiche de contrôle' : 'Nouvelle fiche de contrôle' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
                </div>
                <form wire:submit="saveSheet">
                    <div class="modal-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                        @endif

                        {{-- En-tête --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Véhicule <span class="text-danger">*</span></label>
                                <select class="form-select @error('vehicle_id') is-invalid @enderror" wire:model="vehicle_id">
                                    <option value="">—</option>
                                    @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->registration }}</option>
                                    @endforeach
                                </select>
                                @error('vehicle_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Chauffeur</label>
                                <select class="form-select" wire:model="driver_id">
                                    <option value="">—</option>
                                    @foreach($drivers as $d)
                                    <option value="{{ $d->id }}">{{ $d->last_name }} {{ $d->first_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mission liée (optionnel)</label>
                                <select class="form-select" wire:model="mission_id">
                                    <option value="">—</option>
                                    @foreach($missions as $m)
                                    <option value="{{ $m->id }}">#{{ $m->id }} — {{ Str::limit($m->destination, 30) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">N° Ordre de Mission</label>
                                <input type="text" class="form-control" wire:model="ordre_mission" placeholder="ex: 325">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Lieu de départ</label>
                                <input type="text" class="form-control" wire:model="lieu" placeholder="ex: Kribi">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date départ <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_depart') is-invalid @enderror" wire:model="date_depart">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date retour</label>
                                <input type="date" class="form-control" wire:model="date_retour">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">KM départ</label>
                                <input type="number" min="0" class="form-control" wire:model="km_depart">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">KM retour</label>
                                <input type="number" min="0" class="form-control" wire:model="km_retour">
                            </div>
                        </div>

                        @php
                            $checkSections = [
                                ['key' => 'docs_administratifs', 'title' => 'Dossier Administratif', 'icon' => 'bi-folder2-open', 'type' => 'docs'],
                                ['key' => 'controle_exterieur',  'title' => 'Contrôle Extérieur du Véhicule', 'icon' => 'bi-car-front', 'type' => 'check4'],
                                ['key' => 'compartiment_moteur','title' => 'Compartiment Moteur', 'icon' => 'bi-gear', 'type' => 'check4'],
                                ['key' => 'controle_fonctionnalites','title' => 'Contrôle des Fonctionnalités', 'icon' => 'bi-lightning', 'type' => 'check4'],
                                ['key' => 'outillages', 'title' => 'Outillages / Accessoires', 'icon' => 'bi-tools', 'type' => 'outillage'],
                            ];
                        @endphp

                        @foreach($checkSections as $section)
                        @php $sKey = $section['key']; $data = $this->$sKey; @endphp
                        <div class="card mb-3 border">
                            <div class="card-header py-2 fw-bold" style="background:rgba(26,84,144,.07)">
                                <i class="bi {{ $section['icon'] }} me-2"></i>{{ $section['title'] }}
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="text-center" style="font-size:.78rem;background:#f8f9fa">
                                        <tr>
                                            <th style="width:35%" class="text-start">Élément</th>
                                            @if($section['type'] === 'docs')
                                                <th colspan="2">Présent</th>
                                                <th colspan="2">Non présent</th>
                                            @elseif($section['type'] === 'outillage')
                                                <th colspan="2">Présent</th>
                                                <th colspan="2">Absent</th>
                                            @else
                                                <th colspan="2">Correct</th>
                                                <th colspan="2">Défaut</th>
                                            @endif
                                        </tr>
                                        <tr style="font-size:.72rem">
                                            <th></th>
                                            <th style="width:8%">D</th><th style="width:8%">R</th>
                                            <th style="width:8%">D</th><th style="width:8%">R</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data as $itemKey => $vals)
                                        <tr>
                                            <td style="font-size:.82rem">{{ $labels[$itemKey] ?? $itemKey }}</td>
                                            @if($section['type'] === 'docs')
                                                <td class="text-center"><input type="checkbox" class="form-check-input" wire:model="{{ $sKey }}.{{ $itemKey }}.depart" value="ok" @if(($vals['depart'] ?? null) === 'ok') checked @endif wire:click="$set('{{ $sKey }}.{{ $itemKey }}.depart', (${{ $sKey }}['{{ $itemKey }}']['depart'] ?? null) === 'ok' ? null : 'ok')"></td>
                                                <td class="text-center"><input type="checkbox" class="form-check-input" wire:model="{{ $sKey }}.{{ $itemKey }}.retour" value="ok" @if(($vals['retour'] ?? null) === 'ok') checked @endif wire:click="$set('{{ $sKey }}.{{ $itemKey }}.retour', (${{ $sKey }}['{{ $itemKey }}']['retour'] ?? null) === 'ok' ? null : 'ok')"></td>
                                                <td class="text-center"><input type="checkbox" class="form-check-input" @if(($vals['depart'] ?? null) === 'absent') checked @endif wire:click="$set('{{ $sKey }}.{{ $itemKey }}.depart', (${{ $sKey }}['{{ $itemKey }}']['depart'] ?? null) === 'absent' ? null : 'absent')"></td>
                                                <td class="text-center"><input type="checkbox" class="form-check-input" @if(($vals['retour'] ?? null) === 'absent') checked @endif wire:click="$set('{{ $sKey }}.{{ $itemKey }}.retour', (${{ $sKey }}['{{ $itemKey }}']['retour'] ?? null) === 'absent' ? null : 'absent')"></td>
                                            @else
                                                @php $k1 = $section['type'] === 'outillage' ? 'present_d' : 'correct_d'; $k2 = $section['type'] === 'outillage' ? 'present_r' : 'correct_r'; $k3 = $section['type'] === 'outillage' ? 'absent_d' : 'defaut_d'; $k4 = $section['type'] === 'outillage' ? 'absent_r' : 'defaut_r'; @endphp
                                                <td class="text-center"><input type="checkbox" class="form-check-input" @if($vals[$k1] ?? false) checked @endif wire:click="$set('{{ $sKey }}.{{ $itemKey }}.{{ $k1 }}', !(${{ $sKey }}['{{ $itemKey }}']['{{ $k1 }}'] ?? false))"></td>
                                                <td class="text-center"><input type="checkbox" class="form-check-input" @if($vals[$k2] ?? false) checked @endif wire:click="$set('{{ $sKey }}.{{ $itemKey }}.{{ $k2 }}', !(${{ $sKey }}['{{ $itemKey }}']['{{ $k2 }}'] ?? false))"></td>
                                                <td class="text-center"><input type="checkbox" class="form-check-input" @if($vals[$k3] ?? false) checked @endif wire:click="$set('{{ $sKey }}.{{ $itemKey }}.{{ $k3 }}', !(${{ $sKey }}['{{ $itemKey }}']['{{ $k3 }}'] ?? false))"></td>
                                                <td class="text-center"><input type="checkbox" class="form-check-input" @if($vals[$k4] ?? false) checked @endif wire:click="$set('{{ $sKey }}.{{ $itemKey }}.{{ $k4 }}', !(${{ $sKey }}['{{ $itemKey }}']['{{ $k4 }}'] ?? false))"></td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endforeach

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Observations départ</label>
                                <textarea class="form-control" rows="2" wire:model="observations_depart"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Observations retour</label>
                                <textarea class="form-control" rows="2" wire:model="observations_retour"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" wire:click="$set('showFormModal', false)">Annuler</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $editingId ? 'Enregistrer' : 'Créer la fiche' }}</span>
                            <span wire:loading><i class="bi bi-hourglass-split me-1"></i>Traitement…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- ── MODAL PHOTOS ── --}}
    @if($showPhotoModal)
    @php $sheetForPhotos = $photoSheetId ? \App\Models\VehicleControlSheet::with('photos')->find($photoSheetId) : null; @endphp
    <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.45);">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-camera me-2"></i>Photos avant / après — Fiche de contrôle</h5>
                    <button type="button" class="btn-close" wire:click="$set('showPhotoModal', false)"></button>
                </div>
                <div class="modal-body">
                    @if($sheetForPhotos && $sheetForPhotos->photos->count() > 0)
                    <div class="row g-3 mb-4">
                        @php $before = $sheetForPhotos->photos->where('type','before'); $after = $sheetForPhotos->photos->where('type','after'); @endphp
                        <div class="col-md-6">
                            <h6 class="text-muted fw-bold"><i class="bi bi-clock me-1"></i>Avant mission ({{ $before->count() }})</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($before as $p)
                                <div class="position-relative" style="width:130px">
                                    <a href="{{ $p->url }}" target="_blank">
                                        <img src="{{ $p->url }}" class="img-thumbnail" style="width:130px;height:95px;object-fit:cover" alt="">
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                        style="padding:.1rem .3rem;font-size:.65rem"
                                        wire:click="deletePhoto({{ $p->id }})"
                                        wire:confirm="Supprimer cette photo ?"><i class="bi bi-x"></i></button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted fw-bold"><i class="bi bi-check-circle me-1"></i>Après mission ({{ $after->count() }})</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($after as $p)
                                <div class="position-relative" style="width:130px">
                                    <a href="{{ $p->url }}" target="_blank">
                                        <img src="{{ $p->url }}" class="img-thumbnail" style="width:130px;height:95px;object-fit:cover" alt="">
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                        style="padding:.1rem .3rem;font-size:.65rem"
                                        wire:click="deletePhoto({{ $p->id }})"
                                        wire:confirm="Supprimer cette photo ?"><i class="bi bi-x"></i></button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <hr>
                    @endif
                    <form wire:submit="savePhotos">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"><i class="bi bi-clock me-1 text-warning"></i>Photos AVANT mission</label>
                                <input type="file" class="form-control" wire:model="before_photos" multiple accept="image/*">
                                <div class="form-text">Max 8 Mo / photo</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"><i class="bi bi-check-circle me-1 text-success"></i>Photos APRÈS mission</label>
                                <input type="file" class="form-control" wire:model="after_photos" multiple accept="image/*">
                                <div class="form-text">Max 8 Mo / photo</div>
                            </div>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-outline-secondary me-2" wire:click="$set('showPhotoModal', false)">Fermer</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="bi bi-cloud-upload me-1"></i>Enregistrer</span>
                                <span wire:loading>Envoi…</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── MODAL VUE FICHE ── --}}
    @if($showViewModal && $viewingSheet)
    <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.45);">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-clipboard-data me-2"></i>Fiche de contrôle — {{ $viewingSheet->vehicle?->registration }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('showViewModal', false)"></button>
                </div>
                <div class="modal-body">
                    @include('livewire.portal.vehicles.partials.control-sheet-view', ['sheet' => $viewingSheet, 'labels' => $labels])
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" wire:click="$set('showViewModal', false)">Fermer</button>
                    <button class="btn btn-outline-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── MODAL SUPPRESSION ── --}}
    @if($showDeleteModal)
    <div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.45);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Confirmer la suppression</h5></div>
                <div class="modal-body">Supprimer cette fiche de contrôle ?</div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" wire:click="$set('showDeleteModal', false)">Annuler</button>
                    <button class="btn btn-danger" wire:click="deleteSheet">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
