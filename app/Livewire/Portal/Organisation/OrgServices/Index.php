<?php

namespace App\Livewire\Portal\Organisation\OrgServices;

use App\Models\Department;
use App\Models\Direction;
use App\Models\OrgService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter_department = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public ?int $department_id = null;
    public string $name = '';
    public string $code = '';
    public string $notes = '';

    protected $queryString = ['search' => ['except' => ''], 'filter_department' => ['except' => '']];
    protected $paginationTheme = 'bootstrap'; 

    protected function rules(): array
    {
        return [
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $s = OrgService::with('department.direction')->findOrFail($id);
        $this->editingId = $s->id;
        $this->department_id = $s->department_id;
        $this->name = $s->name;
        $this->code = $s->code ?? '';
        $this->notes = $s->notes ?? '';
        $this->showFormModal = true;
    }

    public function saveOrgService(): void
    {
        $this->validate();
        $data = [
            'department_id' => $this->department_id,
            'name' => $this->name,
            'code' => $this->code ?: null,
            'notes' => $this->notes ?: null,
        ];
        if ($this->editingId) {
            OrgService::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Service mis à jour.');
        } else {
            OrgService::create($data);
            $this->dispatch('notify', type: 'success', message: 'Service créé.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteOrgService(): void
    {
        if ($this->editingId) {
            OrgService::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Service supprimé.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->department_id = null;
        $this->name = '';
        $this->code = '';
        $this->notes = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = OrgService::query()->with('department.direction')->withCount('persons');
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('department', fn ($q2) => $q2->where('name', 'like', '%' . $this->search . '%')->orWhereHas('direction', fn ($q3) => $q3->where('name', 'like', '%' . $this->search . '%')));
            });
        }
        if ($this->filter_department !== '') {
            $query->where('department_id', $this->filter_department);
        }
        $orgServices = $query->orderBy('name')->paginate(12);
        $departments = Department::with('direction')->orderBy('name')->get(['id', 'direction_id', 'name']);
        $directions = Direction::orderBy('name')->get(['id', 'name']);

        return view('livewire.portal.organisation.org-services.index', [
            'orgServices' => $orgServices,
            'departments' => $departments,
            'directions' => $directions,
        ])->layout('layouts.app', ['title' => 'Services — Organisation']);
    }
}
