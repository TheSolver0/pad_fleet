@if($showPhotoModal)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Photos {{ $photo_type === 'before' ? 'avant' : 'après' }} mission
                </h5>
                <button type="button" class="btn-close" wire:click="$set('showPhotoModal', false)"></button>
            </div>
            <form wire:submit="savePhotos">
                <div class="modal-body">
                    @php
                        $mission = \App\Models\Mission::find($photoMissionId);
                        $existingPhotos = $mission ? $mission->photos()->where('type', $photo_type)->orderBy('sort_order')->get() : collect();
                    @endphp

                    @if($existingPhotos->count() > 0)
                        <div class="mb-3">
                            <h6>Photos existantes ({{ $existingPhotos->count() }})</h6>
                            <div class="row g-2">
                                @foreach($existingPhotos as $photo)
                                    <div class="col-md-3">
                                        <div class="card">
                                            <img src="{{ asset('storage/' . $photo->file_path) }}" class="card-img-top" alt="{{ $photo->original_name }}" style="height: 120px; object-fit: cover;" onerror="this.src='{{ asset('img/logo.png') }}'">
                                            <div class="card-body p-2">
                                                <p class="card-text small mb-1">{{ $photo->original_name }}</p>
                                                <small class="text-muted">Chemin: {{ $photo->file_path }}</small>
                                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deletePhoto({{ $photo->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Aucune photo {{ $photo_type === 'before' ? 'avant' : 'après' }} mission pour le moment.
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Ajouter de nouvelles photos</label>
                        @if($photo_type === 'before')
                            <input type="file" class="form-control" wire:model="before_photos" multiple accept="image/*">
                            @error('before_photos.*') <span class="text-danger small">{{ $message }}</span> @enderror
                        @else
                            <input type="file" class="form-control" wire:model="after_photos" multiple accept="image/*">
                            @error('after_photos.*') <span class="text-danger small">{{ $message }}</span> @enderror
                        @endif
                        <div class="form-text">Formats acceptés: JPG, PNG, GIF. Taille max: 5MB par photo.</div>
                    </div>

                    @if($photo_type === 'before' && $before_photos)
                        <div class="mb-3">
                            <h6>Aperçu des nouvelles photos</h6>
                            <div class="row g-2">
                                @foreach($before_photos as $index => $photo)
                                    <div class="col-md-3">
                                        <div class="card">
                                            <img src="{{ $photo->temporaryUrl() }}" class="card-img-top" alt="Nouvelle photo" style="height: 120px; object-fit: cover;">
                                            <div class="card-body p-2">
                                                <p class="card-text small">{{ $photo->getClientOriginalName() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($photo_type === 'after' && $after_photos)
                        <div class="mb-3">
                            <h6>Aperçu des nouvelles photos</h6>
                            <div class="row g-2">
                                @foreach($after_photos as $index => $photo)
                                    <div class="col-md-3">
                                        <div class="card">
                                            <img src="{{ $photo->temporaryUrl() }}" class="card-img-top" alt="Nouvelle photo" style="height: 120px; object-fit: cover;">
                                            <div class="card-body p-2">
                                                <p class="card-text small">{{ $photo->getClientOriginalName() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" wire:click="$set('showPhotoModal', false)">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les photos</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif