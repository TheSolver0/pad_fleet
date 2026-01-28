<div>
    <p class="section-label">Personnes (affectation véhicules)</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Personnes</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Nom, email, service..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouvelle personne
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
                        <th>Téléphone</th>
                        <th>Direction / Département / Service</th>
                        <th>Véhicules affectés</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($persons as $p)
                        <tr>
                            <td><span class="fw-medium">{{ $p->name }}</span></td>
                            <td>{{ $p->email ?? '—' }}</td>
                            <td>{{ $p->phone ?? '—' }}</td>
                            <td class="small">{{ $p->organisation_label }}</td>
                            <td>{{ $p->assigned_vehicles_count }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $p->id }})" title="Modifier"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $p->id }})" title="Supprimer"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Aucune personne. Créez des personnes pour pouvoir affecter des véhicules.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($persons->hasPages())
            <div class="p-3 border-top">{{ $persons->links() }}</div>
        @endif
    </div>

    @include('livewire.portal.personnes.partials.modal-form')
    @include('livewire.portal.personnes.partials.modal-delete')
</div>
