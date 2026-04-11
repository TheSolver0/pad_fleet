<?php

namespace App\Livewire\Portal\Organisation\Directions;

use App\Models\Direction;
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
    public string $notes = '';

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'bootstrap'; 

    protected function rules(): array
    {
        return [
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
        $d = Direction::findOrFail($id);
        $this->editingId = $d->id;
        $this->name = $d->name;
        $this->code = $d->code ?? '';
        $this->notes = $d->notes ?? '';
        $this->showFormModal = true;
    }

    public function saveDirection(): void
    {
        $this->validate();
        $data = ['name' => $this->name, 'code' => $this->code ?: null, 'notes' => $this->notes ?: null];
        if ($this->editingId) {
            Direction::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Direction mise à jour.');
        } else {
            Direction::create($data);
            $this->dispatch('notify', type: 'success', message: 'Direction créée.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteDirection(): void
    {
        if ($this->editingId) {
            Direction::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Direction supprimée.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->code = '';
        $this->notes = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = Direction::query()->withCount(['departments', 'persons']);
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }
        $directions = $query->orderBy('name')->paginate(12);

        return view('livewire.portal.organisation.directions.index', ['directions' => $directions])
            ->layout('layouts.app', ['title' => 'Directions — Organisation']);
    }
}
