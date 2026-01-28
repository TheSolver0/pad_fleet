@if($showPhotoModal && $photoSinistre)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Photos — Sinistre #{{ $photoSinistre->id }}</h5>
                <button type="button" class="btn-close" wire:click="$set('showPhotoModal', false)"></button>
            </div>
            <div class="modal-body">
                <form wire:submit="uploadPhoto" class="mb-4">
                    <div class="input-group">
                        <input type="file" class="form-control" wire:model="photo_file" accept="image/*">
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                    @error('photo_file') <span class="small text-danger">{{ $message }}</span> @enderror
                </form>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($photoSinistre->photos as $photo)
                        <div class="position-relative">
                            <img src="{{ asset('storage/' . $photo->file_path) }}" alt="" style="max-height: 120px; border-radius: 8px;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" wire:click="deletePhoto({{ $photo->id }})"><i class="bi bi-trash"></i></button>
                        </div>
                    @endforeach
                </div>
                @if($photoSinistre->photos->isEmpty())
                    <p class="text-muted mb-0">Aucune photo.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="$set('showPhotoModal', false)">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endif
