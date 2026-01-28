<?php

namespace App\Livewire\Portal\VehicleModels;

use App\Models\Brand;
use App\Models\VehicleModel;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $brand_filter = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public ?int $brand_id = null;
    public string $name = '';
    public string $code = '';

    protected $queryString = ['search' => ['except' => ''], 'brand_filter' => ['except' => '']];

    protected function rules(): array
    {
        return [
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
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
        $m = VehicleModel::findOrFail($id);
        $this->editingId = $m->id;
        $this->brand_id = $m->brand_id;
        $this->name = $m->name;
        $this->code = $m->code ?? '';
        $this->showFormModal = true;
    }

    public function saveModel(): void
    {
        $this->validate();
        $data = [
            'brand_id' => $this->brand_id,
            'name' => $this->name,
            'code' => $this->code ?: null,
        ];
        if ($this->editingId) {
            VehicleModel::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Modèle mis à jour.');
        } else {
            VehicleModel::create($data);
            $this->dispatch('notify', type: 'success', message: 'Modèle créé.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteModel(): void
    {
        if ($this->editingId) {
            VehicleModel::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Modèle supprimé.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->brand_id = null;
        $this->name = '';
        $this->code = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = VehicleModel::query()->with('brand:id,name');
        if ($this->search !== '') {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        if ($this->brand_filter !== '') {
            $query->where('brand_id', $this->brand_filter);
        }
        $models = $query->orderBy('name')->paginate(12);
        $brands = Brand::orderBy('name')->get(['id', 'name']);

        return view('livewire.portal.vehicle-models.index', [
            'models' => $models,
            'brands' => $brands,
        ])->layout('layouts.app', ['title' => 'Modèles de véhicules']);
    }
}
