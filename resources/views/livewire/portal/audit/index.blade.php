<div>
    <p class="section-label">Journal d'audit — Traçabilité des actions</p>

    <div class="activity-card mb-4">
        <div class="activity-card-header">Filtres</div>
        <div class="p-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label small fw-600">Action</label>
                <select class="form-select form-select-sm" wire:model.live="action_filter">
                    <option value="">Toutes</option>
                    @foreach($actions as $a)
                        <option value="{{ $a }}">{{ \App\Models\AuditLog::actionLabel($a) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-600">Utilisateur</label>
                <select class="form-select form-select-sm" wire:model.live="user_filter">
                    <option value="">Tous</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->matricule ?? '—' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-600">Du</label>
                <input type="date" class="form-control form-control-sm" wire:model.live="date_from">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-600">Au</label>
                <input type="date" class="form-control form-control-sm" wire:model.live="date_to">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-600">Recherche</label>
                <input type="text" class="form-control form-control-sm" wire:model.live.debounce.300ms="search" placeholder="Description, URL...">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-secondary btn-sm w-100" wire:click="clearFilters">
                    <i class="bi bi-x-circle me-1"></i> Réinitialiser
                </button>
            </div>
        </div>
        </div>
    </div>

    <div class="activity-card">
        <div class="activity-card-header">Traces récentes</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 140px;">Date / Heure</th>
                        <th style="width: 100px;">Action</th>
                        <th>Acteur</th>
                        <th>Description</th>
                        <th style="width: 100px;">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="text-nowrap small text-muted">
                                {{ $log->created_at->format('d/m/Y') }}<br>
                                <span class="text-body">{{ $log->created_at->format('H:i:s') }}</span>
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($log->action) {
                                        'created' => 'bg-success',
                                        'updated' => 'bg-primary',
                                        'deleted' => 'bg-danger',
                                        'login' => 'bg-info',
                                        'logout' => 'bg-secondary',
                                        'login_failed' => 'bg-warning text-dark',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $log->action_label }}</span>
                            </td>
                            <td>
                                <span class="fw-medium">{{ $log->actor_name }}</span>
                                @if($log->user?->matricule)
                                    <br><small class="text-muted">Mat. {{ $log->user->matricule }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="small">{{ $log->description }}</div>
                                @if($log->auditable_type)
                                    <small class="text-muted">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</small>
                                @endif
                                @if($log->old_values || $log->new_values)
                                    <button type="button" class="btn btn-link btn-sm p-0 ms-1" data-bs-toggle="collapse" data-bs-target="#audit-detail-{{ $log->id }}" aria-expanded="false">
                                        Détails
                                    </button>
                                    <div class="collapse mt-1 small" id="audit-detail-{{ $log->id }}">
                                        @if($log->old_values)
                                            <div><span class="text-muted">Ancien :</span> <code>{{ json_encode($log->old_values, JSON_UNESCAPED_UNICODE) }}</code></div>
                                        @endif
                                        @if($log->new_values)
                                            <div><span class="text-muted">Nouveau :</span> <code>{{ json_encode($log->new_values, JSON_UNESCAPED_UNICODE) }}</code></div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $log->ip_address ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Aucune trace pour le moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="p-3 border-top">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
