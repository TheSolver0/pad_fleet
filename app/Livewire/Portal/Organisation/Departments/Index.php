<?php

namespace App\Livewire\Portal\Organisation\Departments;

use App\Models\Department;
use App\Models\Direction;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter_direction = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public ?int $direction_id = null;
    public string $name = '';
    public string $code = '';
    public string $notes = '';

    protected $queryString = ['search' => ['except' => ''], 'filter_direction' => ['except' => '']];
    protected $paginationTheme = 'bootstrap'; 

    protected function rules(): array
    {
        return [
            'direction_id' => 'required|exists:directions,id',
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
        $d = Department::with('direction')->findOrFail($id);
        $this->editingId = $d->id;
        $this->direction_id = $d->direction_id;
        $this->name = $d->name;
        $this->code = $d->code ?? '';
        $this->notes = $d->notes ?? '';
        $this->showFormModal = true;
    }

    public function saveDepartment(): void
    {
        $this->validate();
        $data = [
            'direction_id' => $this->direction_id,
            'name' => $this->name,
            'code' => $this->code ?: null,
            'notes' => $this->notes ?: null,
        ];
        if ($this->editingId) {
            Department::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Département mis à jour.');
        } else {
            Department::create($data);
            $this->dispatch('notify', type: 'success', message: 'Département créé.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteDepartment(): void
    {
        if ($this->editingId) {
            Department::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Département supprimé.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    public function updatedDirectionId(): void
    {
        $this->resetValidation();
    }

    private function resetForm(): void
    {
        $this->direction_id = null;
        $this->name = '';
        $this->code = '';
        $this->notes = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = Department::query()->with('direction')->withCount(['orgServices', 'persons']);
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('direction', fn ($q2) => $q2->where('name', 'like', '%' . $this->search . '%'));
            });
        }
        if ($this->filter_direction !== '') {
            $query->where('direction_id', $this->filter_direction);
        }
        $departments = $query->orderBy('name')->paginate(12);
        $directions = Direction::orderBy('name')->get(['id', 'name']);

        return view('livewire.portal.organisation.departments.index', [
            'departments' => $departments,
            'directions' => $directions,
        ])->layout('layouts.app', ['title' => 'Départements — Organisation']);
    }
}
