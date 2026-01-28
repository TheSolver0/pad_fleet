@if($showDocModal && $docContract)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Contrats — {{ $docContract->name }}</h5>
                <button type="button" class="btn-close" wire:click="$set('showDocModal', false)"></button>
            </div>
            <div class="modal-body">
                <form wire:submit="uploadContractDoc" class="mb-4">
                    <div class="input-group">
                        <input type="file" class="form-control" wire:model="contract_file" accept=".pdf,.jpg,.jpeg,.png">
                        <button type="submit" class="btn btn-primary">Joindre</button>
                    </div>
                    @error('contract_file') <span class="small text-danger">{{ $message }}</span> @enderror
                </form>
                <ul class="list-group list-group-flush">
                    @forelse($docContract->documents as $doc)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank">{{ $doc->original_name ?: basename($doc->file_path) }}</a>
                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteDoc({{ $doc->id }})"><i class="bi bi-trash"></i></button>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Aucun document.</li>
                    @endforelse
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="$set('showDocModal', false)">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endif
