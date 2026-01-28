<?php

namespace App\Livewire\Portal\Brands;

use App\Models\Brand;
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
    public string $code = '';

    protected $queryString = ['search' => ['except' => '']];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20',
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
        $b = Brand::findOrFail($id);
        $this->editingId = $b->id;
        $this->name = $b->name;
        $this->code = $b->code ?? '';
        $this->showFormModal = true;
    }

    public function saveBrand(): void
    {
        $this->validate();
        $data = [
            'name' => $this->name,
            'code' => $this->code ?: null,
        ];
        if ($this->editingId) {
            Brand::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Marque mise à jour.');
        } else {
            Brand::create($data);
            $this->dispatch('notify', type: 'success', message: 'Marque créée.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteBrand(): void
    {
        if ($this->editingId) {
            Brand::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Marque supprimée.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->code = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = Brand::query()->withCount('vehicleModels');
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }
        $brands = $query->orderBy('name')->paginate(12);

        return view('livewire.portal.brands.index', [
            'brands' => $brands,
        ])->layout('layouts.app', ['title' => 'Marques de véhicules']);
    }
}
