<?php

namespace App\Livewire\Portal\Repairs;

use App\Models\Garage;
use App\Models\Repair;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public string $type_filter = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public ?int $vehicle_id = null;
    public ?int $garage_id = null;
    public string $type = Repair::TYPE_INTERNAL;
    public $transfer_sheet = null;
    public string $description = '';
    public string $cost = '';
    public string $started_at = '';
    public string $completed_at = '';
    public string $notes = '';

    protected $queryString = ['search' => ['except' => ''], 'type_filter' => ['except' => '']];

    protected function rules(): array
    {
        $rules = [
            'vehicle_id' => 'required|exists:vehicles,id',
            'garage_id' => 'required|exists:garages,id',
            'type' => 'required|in:internal,external',
            'description' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
            'started_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ];
        return $rules;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $r = Repair::findOrFail($id);
        $this->editingId = $r->id;
        $this->vehicle_id = $r->vehicle_id;
        $this->garage_id = $r->garage_id;
        $this->type = $r->type;
        $this->description = $r->description;
        $this->cost = $r->cost ? format_money($r->cost, 2) : '';
        $this->started_at = $r->started_at?->format('Y-m-d') ?? '';
        $this->completed_at = $r->completed_at?->format('Y-m-d') ?? '';
        $this->notes = $r->notes ?? '';
        $this->showFormModal = true;
    }

    public function saveRepair(): void
    {
        $this->cost = parse_french_number($this->cost) ?? $this->cost;
        $this->validate();
        $data = [
            'vehicle_id' => $this->vehicle_id,
            'garage_id' => $this->garage_id,
            'type' => $this->type,
            'description' => $this->description,
            'cost' => $this->cost ?: null,
            'started_at' => $this->started_at ?: null,
            'completed_at' => $this->completed_at ?: null,
            'notes' => $this->notes ?: null,
        ];
        if ($this->type === Repair::TYPE_EXTERNAL && $this->transfer_sheet) {
            $vehicle = Vehicle::findOrFail($this->vehicle_id);
            $path = $this->transfer_sheet->store('repairs/transfer-sheets', 'public');
            $data['transfer_sheet_path'] = $path;
        }
        if ($this->editingId) {
            Repair::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Réparation mise à jour.');
        } else {
            Repair::create($data);
            $this->dispatch('notify', type: 'success', message: 'Réparation enregistrée.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRepair(): void
    {
        if ($this->editingId) {
            Repair::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Réparation supprimée.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->vehicle_id = null;
        $this->garage_id = null;
        $this->type = Repair::TYPE_INTERNAL;
        $this->transfer_sheet = null;
        $this->description = '';
        $this->cost = '';
        $this->started_at = '';
        $this->completed_at = '';
        $this->notes = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = Repair::query()->with(['vehicle:id,registration', 'garage:id,name']);
        if ($this->search !== '') {
            $query->whereHas('vehicle', fn ($q) => $q->where('registration', 'like', '%' . $this->search . '%'));
        }
        if ($this->type_filter !== '') {
            $query->where('type', $this->type_filter);
        }
        $repairs = $query->orderByDesc('created_at')->paginate(12);
        $vehicles = Vehicle::orderBy('registration')->get(['id', 'registration']);
        $garages = Garage::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('livewire.portal.repairs.index', [
            'repairs' => $repairs,
            'vehicles' => $vehicles,
            'garages' => $garages,
        ])->layout('layouts.app', ['title' => 'Réparations']);
    }
}
