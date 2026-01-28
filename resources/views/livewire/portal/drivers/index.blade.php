<div>
    <p class="section-label">Chauffeurs — Profils, permis, affectation service, disponibilité</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Chauffeurs</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Nom, matricule..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="availability_filter">
                        <option value="">Tous</option>
                        <option value="1">Disponibles</option>
                        <option value="0">Indisponibles</option>
                    </select>
                </div>
                <div class="module-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-person-plus me-1"></i> Nouveau chauffeur
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Matricule</th>
                        <th>Nom complet</th>
                        <th>Service</th>
                        <th>Permis / Échéance</th>
                        <th>Statut</th>
                        <th>Missions</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drivers as $d)
                        <tr>
                            <td>{{ $d->matricule ?? '—' }}</td>
                            <td><span class="fw-medium">{{ $d->full_name }}</span></td>
                            <td>{{ $d->service?->name ?? '—' }}</td>
                            <td>
                                @if($d->license_number)
                                    {{ $d->license_category ?? '—' }} — {{ $d->license_expiry ? $d->license_expiry->format('d/m/Y') : '—' }}
                                    @if($d->isLicenseExpired()) <span class="badge bg-danger">Expiré</span> @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($d->is_available)
                                    <span class="badge bg-success">Disponible</span>
                                @else
                                    <span class="badge bg-secondary">Indisponible</span>
                                @endif
                            </td>
                            <td>{{ $d->missions_count }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $d->id }})"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $d->id }})"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucun chauffeur.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($drivers->hasPages())
            <div class="p-3 border-top">{{ $drivers->links() }}</div>
        @endif
    </div>

    @include('livewire.portal.drivers.partials.modal-form')
    @include('livewire.portal.drivers.partials.modal-delete')
</div>
