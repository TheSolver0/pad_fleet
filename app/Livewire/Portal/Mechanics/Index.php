<?php

namespace App\Livewire\Portal\Mechanics;

use App\Models\Mechanic;
use App\Models\User;
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

    public string $first_name = '';
    public string $last_name = '';
    public ?int $user_id = null;
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $specialization = '';
    public string $hire_date = '';
    public string $certificate = '';
    public string $hourly_rate = '';
    public bool $is_active = true;
    public string $notes = '';

    protected $queryString = ['search' => ['except' => '']];

    protected function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'user_id' => 'nullable|exists:users,id',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'specialization' => 'nullable|string|max:100',
            'hire_date' => 'nullable|date',
            'certificate' => 'nullable|string|max:200',
            'hourly_rate' => 'nullable|numeric|min:0',
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
        $mechanic = Mechanic::findOrFail($id);
        $this->editingId = $mechanic->id;
        $this->first_name = $mechanic->first_name;
        $this->last_name = $mechanic->last_name;
        $this->user_id = $mechanic->user_id;
        $this->phone = $mechanic->phone ?? '';
        $this->email = $mechanic->email ?? '';
        $this->address = $mechanic->address ?? '';
        $this->specialization = $mechanic->specialization ?? '';
        $this->hire_date = $mechanic->hire_date?->format('Y-m-d') ?? '';
        $this->certificate = $mechanic->certificate ?? '';
        $this->hourly_rate = $mechanic->hourly_rate ? number_format($mechanic->hourly_rate, 2, ',', ' ') : '';
        $this->is_active = $mechanic->is_active;
        $this->notes = $mechanic->notes ?? '';
        $this->showFormModal = true;
    }

    public function saveMechanic(): void
    {
        $this->hourly_rate = str_replace([' ', ','], ['', '.'], $this->hourly_rate);
        
        $this->validate();
        
        $data = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'user_id' => $this->user_id,
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'address' => $this->address ?: null,
            'specialization' => $this->specialization ?: null,
            'hire_date' => $this->hire_date ?: null,
            'certificate' => $this->certificate ?: null,
            'hourly_rate' => $this->hourly_rate ?: null,
            'is_active' => $this->is_active,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editingId) {
            Mechanic::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Mécanicien mis à jour.');
        } else {
            Mechanic::create($data);
            $this->dispatch('notify', type: 'success', message: 'Mécanicien créé.');
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteMechanic(): void
    {
        if ($this->editingId) {
            Mechanic::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Mécanicien supprimé.');
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
        $this->first_name = '';
        $this->last_name = '';
        $this->user_id = null;
        $this->phone = '';
        $this->email = '';
        $this->address = '';
        $this->specialization = '';
        $this->hire_date = '';
        $this->certificate = '';
        $this->hourly_rate = '';
        $this->is_active = true;
        $this->notes = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = Mechanic::query();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $mechanics = $query->orderBy('last_name')->orderBy('first_name')->paginate(20);
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('livewire.portal.mechanics.index', [
            'mechanics' => $mechanics,
            'users' => $users,
        ]);
    }
}
