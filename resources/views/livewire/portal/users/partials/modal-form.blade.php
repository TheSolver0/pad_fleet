@if($showFormModal)
<div class="modal show d-block" tabindex="-1" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $editingId ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' }}</h5>
                <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
            </div>
            <form wire:submit="saveUser">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nom complet <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Matricule</label>
                            <input type="text" class="form-control @error('matricule') is-invalid @enderror" wire:model="matricule">
                            @error('matricule') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email">
                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Téléphone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" wire:model="phone">
                            @error('phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Genre</label>
                            <select class="form-select @error('gender') is-invalid @enderror" wire:model="gender">
                                <option value="">—</option>
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                            @error('gender') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fonction / Poste</label>
                            <input type="text" class="form-control @error('occupation') is-invalid @enderror" wire:model="occupation">
                            @error('occupation') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rôle</label>
                            <select class="form-select @error('roleName') is-invalid @enderror" wire:model="roleName">
                                <option value="">Aucun rôle</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}">{{ $role }}</option>
                                @endforeach
                            </select>
                            @error('roleName') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <label class="form-label">Mot de passe {{ $editingId ? '' : '*' }}</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" wire:model="password" placeholder="{{ $editingId ? 'Laisser vide pour ne pas changer' : '' }}">
                            @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmation mot de passe</label>
                            <input type="password" class="form-control" wire:model="password_confirmation">
                        </div>
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
