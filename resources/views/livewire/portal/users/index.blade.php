<div>
    <p class="section-label">Utilisateurs</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Utilisateurs</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 240px;" placeholder="Nom, email, matricule..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouvel utilisateur
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Matricule</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td><span class="fw-medium">{{ $u->name }}</span></td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->matricule ?? '—' }}</td>
                            <td>
                                @forelse($u->roles as $role)
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                            <td>
                                @if($u->is_active)
                                    <span class="badge bg-success-subtle text-success-emphasis">Actif</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger-emphasis">Inactif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="toggleActive({{ $u->id }})" title="{{ $u->is_active ? 'Rendre inactif' : 'Rendre actif' }}" @if($u->id === auth()->id()) disabled @endif>
                                    <i class="bi {{ $u->is_active ? 'bi-slash-circle' : 'bi-check-circle' }}"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $u->id }})"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $u->id }})" @if($u->id === auth()->id()) disabled title="Impossible de supprimer votre propre compte" @endif><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Aucun utilisateur.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="p-3 border-top">{{ $users->links() }}</div>
        @endif
    </div>

    @include('livewire.portal.users.partials.modal-form')
    @include('livewire.portal.users.partials.modal-delete')
</div>
