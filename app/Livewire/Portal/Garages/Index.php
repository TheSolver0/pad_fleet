<?php

namespace App\Livewire\Portal\Garages;

use App\Models\Garage;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $type_filter = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $type = Garage::TYPE_EXTERNAL;
    public string $address = '';
    public string $phone = '';
    public string $email = '';
    public bool $is_active = true;
    public string $notes = '';

    protected $queryString = ['search' => ['except' => ''], 'type_filter' => ['except' => '']];
    protected $paginationTheme = 'bootstrap'; 

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:internal,external',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'is_active' => 'boolean',
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
        $g = Garage::findOrFail($id);
        $this->editingId = $g->id;
        $this->name = $g->name;
        $this->type = $g->type;
        $this->address = $g->address ?? '';
        $this->phone = $g->phone ?? '';
        $this->email = $g->email ?? '';
        $this->is_active = $g->is_active;
        $this->notes = $g->notes ?? '';
        $this->showFormModal = true;
    }

    public function saveGarage(): void
    {
        $this->validate();
        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'address' => $this->address ?: null,
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'is_active' => $this->is_active,
            'notes' => $this->notes ?: null,
        ];
        if ($this->editingId) {
            Garage::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Garage mis à jour.');
        } else {
            Garage::create($data);
            $this->dispatch('notify', type: 'success', message: 'Garage créé.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteGarage(): void
    {
        if ($this->editingId) {
            Garage::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Garage supprimé.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->type = Garage::TYPE_EXTERNAL;
        $this->address = '';
        $this->phone = '';
        $this->email = '';
        $this->is_active = true;
        $this->notes = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = Garage::query()
            ->withAvg('evaluations', 'quality_score')
            ->withAvg('evaluations', 'delivery_score')
            ->withAvg('evaluations', 'reputation_score')
            ->withCount(['evaluations', 'vehicles']);
        if ($this->search !== '') {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        if ($this->type_filter !== '') {
            $query->where('type', $this->type_filter);
        }
        $garages = $query->orderBy('name')->paginate(12);

        return view('livewire.portal.garages.index', [
            'garages' => $garages,
        ])->layout('layouts.app', ['title' => 'Garages']);
    }
}
