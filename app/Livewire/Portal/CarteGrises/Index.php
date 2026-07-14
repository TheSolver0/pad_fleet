<?php

namespace App\Livewire\Portal\CarteGrises;

use App\Models\Vehicle;
use App\Models\VehicleCarteGrise;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public ?int $vehicle_filter = null;
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public ?int $vehicle_id = null;
    public string $reference_number = '';
    public string $issued_at = '';
    public string $expires_at = '';
    public string $notes = '';
    public $cg_file = null;

    protected $queryString = ['search' => ['except' => ''], 'vehicle_filter' => ['except' => '']];
    protected $paginationTheme = 'bootstrap';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingVehicleFilter(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'reference_number' => 'nullable|string|max:100',
            'issued_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'cg_file' => 'nullable|file|max:10240',
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
        $cg = VehicleCarteGrise::findOrFail($id);
        $this->editingId = $cg->id;
        $this->vehicle_id = $cg->vehicle_id;
        $this->reference_number = $cg->reference_number ?? '';
        $this->issued_at = $cg->issued_at?->format('Y-m-d') ?? '';
        $this->expires_at = $cg->expires_at?->format('Y-m-d') ?? '';
        $this->notes = $cg->notes ?? '';
        $this->cg_file = null;
        $this->showFormModal = true;
    }

    public function saveCarteGrise(): void
    {
        $this->validate();
        $vehicle = Vehicle::findOrFail($this->vehicle_id);
        if ($this->editingId) {
            $cg = VehicleCarteGrise::findOrFail($this->editingId);
            $data = [
                'reference_number' => $this->reference_number ?: null,
                'issued_at' => $this->issued_at ? \Carbon\Carbon::parse($this->issued_at) : null,
                'expires_at' => $this->expires_at ? \Carbon\Carbon::parse($this->expires_at) : null,
                'notes' => $this->notes ?: null,
            ];
            if ($this->cg_file) {
                if ($cg->file_path && Storage::disk('public')->exists($cg->file_path)) {
                    Storage::disk('public')->delete($cg->file_path);
                }
                $data['file_path'] = $this->cg_file->store('vehicles/' . $vehicle->id . '/carte-grise', 'public');
                $data['original_name'] = $this->cg_file->getClientOriginalName();
            }
            $cg->update($data);
            $this->dispatch('notify', type: 'success', message: 'Carte grise mise à jour.');
        } else {
            VehicleCarteGrise::createForVehicle($vehicle, [
                'reference_number' => $this->reference_number ?: null,
                'issued_at' => $this->issued_at ?: null,
                'expires_at' => $this->expires_at ?: null,
                'notes' => $this->notes ?: null,
            ], $this->cg_file);
            $this->dispatch('notify', type: 'success', message: 'Carte grise enregistrée.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteCarteGrise(): void
    {
        if ($this->editingId) {
            VehicleCarteGrise::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Carte grise supprimée.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->vehicle_id = null;
        $this->reference_number = '';
        $this->issued_at = '';
        $this->expires_at = '';
        $this->notes = '';
        $this->cg_file = null;
        $this->editingId = null;
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = VehicleCarteGrise::query()->with('vehicle:id,registration');
        if ($this->vehicle_filter) {
            $query->where('vehicle_id', $this->vehicle_filter);
        }
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('reference_number', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%')
                    ->orWhereHas('vehicle', fn ($v) => $v->where('registration', 'like', '%' . $this->search . '%'));
            });
        }
        $cartesGrises = $query->orderByDesc('expires_at')->paginate(15);
        $vehicles = Vehicle::orderBy('registration')->get(['id', 'registration']);

        return view('livewire.portal.carte-grises.index', [
            'cartesGrises' => $cartesGrises,
            'vehicles' => $vehicles,
        ])->layout('layouts.app', ['title' => 'Cartes grises']);
    }
}
