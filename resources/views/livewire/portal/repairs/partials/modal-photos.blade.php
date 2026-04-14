@if($showPhotoModal)
@php $repair = $photoRepairId ? \App\Models\Repair::with('photos')->find($photoRepairId) : null; @endphp
<div class="modal show d-block" tabindex="-1" style="background:rgba(0,0,0,0.45);">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-camera me-2"></i>
                    Photos avant / après réparation
                    @if($repair) — {{ $repair->vehicle?->registration }} @endif
                </h5>
                <button type="button" class="btn-close" wire:click="$set('showPhotoModal', false)"></button>
            </div>
            <div class="modal-body">

                {{-- Photos existantes --}}
                @if($repair && $repair->photos->count() > 0)
                <div class="row g-3 mb-4">
                    @php
                        $before = $repair->photos->where('type', 'before');
                        $after  = $repair->photos->where('type', 'after');
                    @endphp
                    {{-- Avant --}}
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted mb-2"><i class="bi bi-clock me-1"></i>Avant réparation ({{ $before->count() }})</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($before as $p)
                            <div class="position-relative" style="width:120px">
                                <a href="{{ $p->url }}" target="_blank">
                                    <img src="{{ $p->url }}" alt="avant" class="img-thumbnail" style="width:120px;height:90px;object-fit:cover">
                                </a>
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                    style="padding:.1rem .3rem;font-size:.65rem"
                                    wire:click="deleteRepairPhoto({{ $p->id }})"
                                    wire:confirm="Supprimer cette photo ?">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    {{-- Après --}}
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted mb-2"><i class="bi bi-check-circle me-1"></i>Après réparation ({{ $after->count() }})</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($after as $p)
                            <div class="position-relative" style="width:120px">
                                <a href="{{ $p->url }}" target="_blank">
                                    <img src="{{ $p->url }}" alt="après" class="img-thumbnail" style="width:120px;height:90px;object-fit:cover">
                                </a>
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                    style="padding:.1rem .3rem;font-size:.65rem"
                                    wire:click="deleteRepairPhoto({{ $p->id }})"
                                    wire:confirm="Supprimer cette photo ?">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <hr>
                @endif

                {{-- Upload de nouvelles photos --}}
                <form wire:submit="saveRepairPhotos">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-clock me-1 text-warning"></i>Ajouter photos AVANT réparation
                            </label>
                            <input type="file" class="form-control" wire:model="repair_before_photos"
                                multiple accept="image/*" id="repairBeforePhotos">
                            <div class="form-text">JPEG, PNG, WEBP — max 8 Mo par photo</div>
                            @error('repair_before_photos.*') <span class="text-danger small">{{ $message }}</span> @enderror
                            <div wire:loading wire:target="repair_before_photos" class="text-muted small mt-1">
                                <i class="bi bi-hourglass-split me-1"></i> Chargement…
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-check-circle me-1 text-success"></i>Ajouter photos APRÈS réparation
                            </label>
                            <input type="file" class="form-control" wire:model="repair_after_photos"
                                multiple accept="image/*" id="repairAfterPhotos">
                            <div class="form-text">JPEG, PNG, WEBP — max 8 Mo par photo</div>
                            @error('repair_after_photos.*') <span class="text-danger small">{{ $message }}</span> @enderror
                            <div wire:loading wire:target="repair_after_photos" class="text-muted small mt-1">
                                <i class="bi bi-hourglass-split me-1"></i> Chargement…
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-outline-secondary" wire:click="$set('showPhotoModal', false)">Fermer</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="bi bi-cloud-upload me-1"></i>Enregistrer les photos</span>
                            <span wire:loading><i class="bi bi-hourglass-split me-1"></i>Envoi…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
