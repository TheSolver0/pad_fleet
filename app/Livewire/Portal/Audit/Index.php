<?php

namespace App\Livewire\Portal\Audit;

use App\Models\AuditLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $action_filter = '';
    public string $user_filter = '';
    public string $date_from = '';
    public string $date_to = '';
    public string $search = '';

    protected $queryString = [
        'action_filter' => ['except' => ''],
        'user_filter' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->authorize('audits');
    }

    public function updatedActionFilter(): void
    {
        $this->resetPage();
    }

    public function updatedUserFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->action_filter = '';
        $this->user_filter = '';
        $this->date_from = '';
        $this->date_to = '';
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = AuditLog::query()
            ->with('user:id,name,matricule')
            ->orderByDesc('created_at');

        if ($this->action_filter !== '') {
            $query->where('action', $this->action_filter);
        }
        if ($this->user_filter !== '') {
            $query->where('user_id', $this->user_filter);
        }
        if ($this->date_from !== '') {
            $query->whereDate('created_at', '>=', $this->date_from);
        }
        if ($this->date_to !== '') {
            $query->whereDate('created_at', '<=', $this->date_to);
        }
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                    ->orWhere('auditable_type', 'like', '%' . $this->search . '%')
                    ->orWhere('url', 'like', '%' . $this->search . '%');
            });
        }

        $logs = $query->paginate(20);

        $users = User::orderBy('name')->get(['id', 'name', 'matricule']);
        $actions = AuditLog::query()->distinct()->pluck('action')->sort()->values();

        return view('livewire.portal.audit.index', [
            'logs' => $logs,
            'users' => $users,
            'actions' => $actions,
        ])->layout('layouts.app', ['title' => 'Journal d\'audit']);
    }
}
