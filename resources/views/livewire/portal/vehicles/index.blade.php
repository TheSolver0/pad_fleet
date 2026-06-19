<div>
    <p class="section-label">Gestion des véhicules</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">
            <div class="module-toolbar">
                <span class="module-toolbar-title">Véhicules</span>
                <div class="module-toolbar-filters">
                    <input type="text" class="form-control form-control-sm" style="width: 220px;" placeholder="Immat., marque, modèle..." wire:model.live.debounce.300ms="search">
                    <select class="form-select form-select-sm" style="width: 140px;" wire:model.live="status_filter">
                        <option value="">Tous statuts</option>
                        <option value="available">Disponible</option>
                        <option value="in_use">En mission</option>
                        <option value="repair">En réparation</option>
                        <option value="out_of_service">Hors service</option>
                    </select>
                </div>
                <div class="module-toolbar-actions d-flex align-items-center gap-2">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary {{ $view_mode === 'table' ? 'active' : '' }}" wire:click="setViewMode('table')" title="Vue tableau">
                            <i class="bi bi-table"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary {{ $view_mode === 'card' ? 'active' : '' }}" wire:click="setViewMode('card')" title="Vue cartes">
                            <i class="bi bi-grid-3x3-gap"></i>
                        </button>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nouveau véhicule
                    </button>
                </div>
            </div>
        </div>

        @if($view_mode === 'table')
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Immatriculation</th>
                        <th>Marque / Modèle</th>
                        <th>Catégorie</th>
                        <th>Puissance</th>
                        <th>Valeur vénale</th>
                        <th>KM</th>
                        <th>Affectation</th>
                        <th>Période</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $v)
                        <tr>
                            <td><span class="fw-medium">{{ $v->registration }}</span></td>
                            <td>{{ $v->vehicleModel?->full_name ?? '—' }}</td>
                            <td>{{ $v->category_label ?? '—' }}</td>
                            <td>{{ $v->power !== null ? $v->power . ' CV' : '—' }}</td>
                            <td>{{ $v->venal_value ? format_money($v->venal_value, 0) . ' F' : '—' }}</td>
                            <td>{{ format_number($v->mileage, 0) }}</td>
                            <td class="small">
                                @if($v->assignedPerson)
                                    <span class="fw-medium">{{ $v->assignedPerson->name }}</span>
                                    @if($v->assignment_type_label)
                                        <br><span class="text-muted">{{ $v->assignment_type_label }}</span>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td class="small">{{ $v->assignment_period_label ?? '—' }}</td>
                            <td>
                                @php
                                    $badge = match($v->status) {
                                        'available' => 'bg-success',
                                        'in_use' => 'bg-primary',
                                        'repair' => 'bg-warning text-dark',
                                        'out_of_service' => 'bg-secondary',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ $v->status_label }}</span>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $v->id }})" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="openDocModal({{ $v->id }})" title="Documents, photos & carte grise">
                                    <i class="bi bi-folder2-open"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $v->id }})" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Aucun véhicule. Cliquez sur « Nouveau véhicule » pour ajouter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <div class="row g-3">
            @forelse($vehicles as $v)
                <div class="col-sm-6 col-lg-4">
                    <div class="card h-100 border shadow-sm vehicle-card">
                        <div class="vehicle-card-img-wrap bg-light position-relative" style="height: 160px; overflow: hidden;">
                            @if($v->photos->isNotEmpty())
                                <img src="{{ asset('storage/' . $v->photos->first()->file_path) }}" alt="" class="w-100 h-100 object-fit-cover">
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                    <i class="bi bi-car-front" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                            <span class="badge position-absolute top-0 end-0 m-2 {{ $v->status === 'available' ? 'bg-success' : ($v->status === 'repair' ? 'bg-warning text-dark' : 'bg-secondary') }}">{{ $v->status_label }}</span>
                        </div>
                        <div class="card-body py-3">
                            <h6 class="card-title mb-1 fw-bold">{{ $v->registration }}</h6>
                            <p class="card-text small text-muted mb-2">{{ $v->vehicleModel?->full_name ?? '—' }}</p>
                            <ul class="list-unstyled small mb-0">
                                <li><span class="text-muted">Catégorie:</span> {{ $v->category_label ?? '—' }}</li>
                                <li><span class="text-muted">Puissance:</span> {{ $v->power !== null ? $v->power . ' CV' : '—' }}</li>
                                <li><span class="text-muted">Valeur vénale:</span> {{ $v->venal_value ? format_money($v->venal_value, 0) . ' F' : '—' }}</li>
                                <li><span class="text-muted">KM:</span> {{ format_number($v->mileage, 0) }}</li>
                                <li><span class="text-muted">Affectation:</span> @if($v->assignedPerson) {{ $v->assignedPerson->name }}@if($v->assignment_type_label) ({{ $v->assignment_type_label }})@endif · {{ $v->assignment_period_label ?? '—' }} @else — @endif</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0 d-flex flex-wrap gap-1">
                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEdit({{ $v->id }})" title="Modifier"><i class="bi bi-pencil"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="openDocModal({{ $v->id }})" title="Documents, photos & carte grise"><i class="bi bi-folder2-open"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $v->id }})" title="Supprimer"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">Aucun véhicule. Cliquez sur « Nouveau véhicule » pour ajouter.</div>
            @endforelse
        </div>
        @endif
        @if($vehicles->hasPages())
            <div class="p-3 border-top">{{ $vehicles->links() }}</div>
        @endif
    </div>

    @include('livewire.portal.vehicles.partials.modal-form')
    @include('livewire.portal.vehicles.partials.modal-quick-add-person')
    @include('livewire.portal.vehicles.partials.modal-quick-add-direction')
@include('livewire.portal.vehicles.partials.modal-documents')
    @include('livewire.portal.vehicles.partials.modal-delete')
</div>
