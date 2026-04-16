<?php

namespace App\Livewire\Portal\Affectations;

use App\Models\Person;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Department;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter_type = '';

    public bool $showAssignmentModal = false;
    public bool $showQuickAddPerson = false;
    public ?int $editingVehicleId = null;

    public ?int $assignment_vehicle_id = null;
    public ?int $assignment_person_id = null;
    public string $assignment_type = '';
    public bool $assignment_period_indefinite = true;
    public string $assignment_start_at = '';
    public string $assignment_end_at = '';

    public string $quick_name = '';
    public string $quick_email = '';
    public string $quick_phone = '';
    public string $quick_department = '';
    public ?int $quick_department_id = null;

    protected $queryString = ['search' => ['except' => ''], 'filter_type' => ['except' => '']];
    protected $paginationTheme = 'bootstrap'; 

    public function openCreate(): void
    {
        $this->editingVehicleId = null;
        $this->assignment_vehicle_id = null;
        $this->assignment_person_id = null;
        $this->assignment_type = '';
        $this->assignment_period_indefinite = true;
        $this->assignment_start_at = '';
        $this->assignment_end_at = '';
        $this->showAssignmentModal = true;
    }

    public function openEdit(int $vehicleId): void
    {
        $v = Vehicle::findOrFail($vehicleId);
        $this->editingVehicleId = $v->id;
        $this->assignment_vehicle_id = $v->id;
        $this->assignment_person_id = $v->assigned_person_id;
        $this->assignment_type = $v->assignment_type ?? '';
        $this->assignment_period_indefinite = $v->assignment_end_at === null;
        $this->assignment_start_at = $v->assignment_start_at?->format('Y-m-d') ?? '';
        $this->assignment_end_at = $v->assignment_end_at?->format('Y-m-d') ?? '';
        $this->showAssignmentModal = true;
    }

    public function saveAssignment(): void
    {
        $vehicleId = $this->editingVehicleId ?? $this->assignment_vehicle_id;
        $this->validate([
            'assignment_vehicle_id' => 'required_if:editingVehicleId,null|nullable|exists:vehicles,id',
            'assignment_person_id' => 'nullable|exists:persons,id',
            'assignment_type' => 'nullable|string|in:dotation,affectation,liaison,lucatelli,sec_surete,travaux,missions,transport_vip',
            'assignment_start_at' => 'nullable|date',
            'assignment_end_at' => 'nullable|date|after_or_equal:assignment_start_at',
        ]);
        $id = $this->editingVehicleId ?? $this->assignment_vehicle_id;
        if (!$id) {
            return;
        }
        Vehicle::where('id', $id)->update([
            'assigned_person_id' => $this->assignment_person_id ?: null,
            'assignment_type' => $this->assignment_type ?: null,
            'assignment_start_at' => $this->assignment_start_at ?: null,
            'assignment_end_at' => $this->assignment_period_indefinite ? null : ($this->assignment_end_at ?: null),
        ]);
        $this->dispatch('notify', type: 'success', message: $this->editingVehicleId ? 'Affectation mise à jour.' : 'Affectation enregistrée.');
        $this->showAssignmentModal = false;
        $this->editingVehicleId = null;
        $this->assignment_vehicle_id = null;
        $this->assignment_person_id = null;
        $this->assignment_type = '';
        $this->assignment_period_indefinite = true;
        $this->assignment_start_at = '';
        $this->assignment_end_at = '';
    }

    public function removeAssignment(int $vehicleId): void
    {
        Vehicle::where('id', $vehicleId)->update([
            'assigned_person_id' => null, 'assignment_type' => null,
            'assignment_start_at' => null, 'assignment_end_at' => null,
        ]);
        $this->dispatch('notify', type: 'success', message: 'Affectation retirée.');
    }

    public function closeAssignmentModal(): void
    {
        $this->showAssignmentModal = false;
        $this->editingVehicleId = null;
        $this->assignment_vehicle_id = null;
        $this->assignment_person_id = null;
        $this->assignment_type = '';
        $this->assignment_period_indefinite = true;
        $this->assignment_start_at = '';
        $this->assignment_end_at = '';
        $this->resetValidation();
    }

    public function updatedAssignmentType(): void
    {
        if (in_array($this->assignment_type, [Vehicle::ASSIGNMENT_DOTATION], true)) {
            $this->assignment_period_indefinite = true;
        }
    }

    public function openQuickAddPerson(): void
    {
        $this->quick_name = '';
        $this->quick_email = '';
        $this->quick_phone = '';
        $this->quick_department = '';
        $this->showQuickAddPerson = true;
    }

    public function closeQuickAddPerson(): void
    {
        $this->showQuickAddPerson = false;
        $this->resetValidation(['quick_name', 'quick_email']);
    }

    public function saveQuickPerson(): void
    {
        $this->validate([
            'quick_name' => 'required|string|max:150',
            'quick_email' => 'nullable|email|max:150',
            'quick_phone' => 'nullable|string|max:30',
            'quick_department_id' => 'nullable|exists:departments,id',
        ]);
        $person = Person::create([
            'name' => $this->quick_name,
            'email' => $this->quick_email ?: null,
            'phone' => $this->quick_phone ?: null,
            'department_id' => $this->quick_department_id ?: null,
        ]);
        $this->assignment_person_id = $person->id;
        $this->showQuickAddPerson = false;
        $this->quick_name = '';
        $this->quick_email = '';
        $this->quick_phone = '';
        $this->quick_department_id = null;
        $this->dispatch('notify', type: 'success', message: 'Personne ajoutée et sélectionnée.');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }


    public function render(): View
    {
        $query = Vehicle::query()
            ->with(['vehicleModel.brand:id,name', 'assignedPerson:id,name'])
            ->whereNotNull('assigned_person_id');
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('registration', 'like', '%' . $this->search . '%')
                    ->orWhereHas('vehicleModel', function ($q2) {
                        $q2->where('name', 'like', '%' . $this->search . '%')
                            ->orWhereHas('brand', fn ($q3) => $q3->where('name', 'like', '%' . $this->search . '%'));
                    })
                    ->orWhereHas('assignedPerson', fn ($q2) => $q2->where('name', 'like', '%' . $this->search . '%'));
            });
        }
        if ($this->filter_type !== '') {
            $query->where('assignment_type', $this->filter_type);
        }
        $assignments = $query->orderBy('registration')->paginate(15);
        $vehicles = Vehicle::query()->with('vehicleModel.brand')->orderBy('registration')->get(['id', 'registration', 'vehicle_model_id']);
        $persons = Person::orderBy('name')->get(['id', 'name']);
        $departments = Department::orderBy('name')->get();

        return view('livewire.portal.affectations.index', [
            'assignments' => $assignments,
            'vehicles' => $vehicles,
            'persons' => $persons,
            'departments' => $departments,
            'editingVehicle' => $this->editingVehicleId ? Vehicle::with('vehicleModel.brand')->find($this->editingVehicleId) : null,
        ])->layout('layouts.app', ['title' => 'Affectations véhicules']);
    }
}
