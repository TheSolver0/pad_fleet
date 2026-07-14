@if($showFormModal)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $editingId ? 'Modifier le rôle' : 'Nouveau rôle' }}</h5>
                <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
            </div>
            <form wire:submit="saveRole">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom du rôle <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="ex: Gestionnaire de stock" @if($editingId && $name === 'Administrateur') disabled @endif>
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <hr class="my-3">
                    <label class="form-label fw-semibold">Permissions</label>
                    @error('selectedPermissions') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                    <div class="row g-3">
                        @foreach($permissionGroups as $groupLabel => $permissions)
                            <div class="col-md-6">
                                <div class="border rounded p-2 h-100">
                                    <div class="fw-semibold small text-muted text-uppercase mb-2">{{ $groupLabel }}</div>
                                    @foreach($permissions as $permission)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $permission }}" wire:model="selectedPermissions" id="perm-{{ $permission }}">
                                            <label class="form-check-label small" for="perm-{{ $permission }}">{{ $permission }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
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
