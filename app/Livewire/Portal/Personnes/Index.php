<?php

namespace App\Livewire\Portal\Personnes;

use App\Models\Department;
use App\Models\Direction;
use App\Models\OrgService;
use App\Models\Person;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public ?int $direction_id = null;
    public ?int $department_id = null;
    public ?int $org_service_id = null;
    public string $notes = '';

    protected $queryString = ['search' => ['except' => '']];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:30',
            'direction_id' => 'nullable|exists:directions,id',
            'department_id' => 'nullable|exists:departments,id',
            'org_service_id' => 'nullable|exists:org_services,id',
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
        $p = Person::with('direction', 'department', 'orgService')->findOrFail($id);
        $this->editingId = $p->id;
        $this->name = $p->name;
        $this->email = $p->email ?? '';
        $this->phone = $p->phone ?? '';
        $this->direction_id = $p->direction_id;
        $this->department_id = $p->department_id;
        $this->org_service_id = $p->org_service_id;
        $this->notes = $p->notes ?? '';
        $this->showFormModal = true;
    }

    public function savePerson(): void
    {
        $this->validate();
        $data = [
            'name' => $this->name,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'direction_id' => $this->direction_id,
            'department_id' => $this->department_id,
            'org_service_id' => $this->org_service_id,
            'notes' => $this->notes ?: null,
        ];
        if ($this->editingId) {
            Person::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Personne mise à jour.');
        } else {
            Person::create($data);
            $this->dispatch('notify', type: 'success', message: 'Personne créée.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deletePerson(): void
    {
        if ($this->editingId) {
            Vehicle::where('assigned_person_id', $this->editingId)->update([
                'assigned_person_id' => null, 'assignment_type' => null,
                'assignment_start_at' => null, 'assignment_end_at' => null,
            ]);
            Person::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Personne supprimée.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    public function updatedDirectionId(): void
    {
        $this->department_id = null;
        $this->org_service_id = null;
    }

    public function updatedDepartmentId(): void
    {
        $this->org_service_id = null;
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->direction_id = null;
        $this->department_id = null;
        $this->org_service_id = null;
        $this->notes = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = Person::query()->with(['direction:id,name', 'department:id,name', 'orgService:id,name'])->withCount('assignedVehicles');
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhereHas('direction', fn ($q2) => $q2->where('name', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('department', fn ($q2) => $q2->where('name', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('orgService', fn ($q2) => $q2->where('name', 'like', '%' . $this->search . '%'));
            });
        }
        $persons = $query->orderBy('name')->paginate(12);
        $directions = Direction::orderBy('name')->get(['id', 'name']);
        $departments = Department::when($this->direction_id, fn ($q) => $q->where('direction_id', $this->direction_id))->with('direction:id,name')->orderBy('name')->get(['id', 'direction_id', 'name']);
        $orgServices = OrgService::when($this->department_id, fn ($q) => $q->where('department_id', $this->department_id))->with('department:id,name')->orderBy('name')->get(['id', 'department_id', 'name']);

        return view('livewire.portal.personnes.index', [
            'persons' => $persons,
            'directions' => $directions,
            'departments' => $departments,
            'orgServices' => $orgServices,
        ])->layout('layouts.app', ['title' => 'Personnes (affectation véhicules)']);
    }
}
