<?php

namespace App\Livewire\Portal\Assureurs;

use App\Models\Assureur;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status_filter = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $code = '';
    public string $contact_person = '';
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $city = '';
    public string $country = 'CM';
    public string $website = '';
    public string $notes = '';
    public bool $is_active = true;

    protected $queryString = ['search' => ['except' => ''], 'status_filter' => ['except' => '']];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:200|unique:assureurs,name,' . $this->editingId,
            'code' => 'nullable|string|max:50|unique:assureurs,code,' . $this->editingId,
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:200',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
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
        $assureur = Assureur::findOrFail($id);
        $this->editingId = $assureur->id;
        $this->name = $assureur->name;
        $this->code = $assureur->code ?? '';
        $this->contact_person = $assureur->contact_person ?? '';
        $this->phone = $assureur->phone ?? '';
        $this->email = $assureur->email ?? '';
        $this->address = $assureur->address ?? '';
        $this->city = $assureur->city ?? '';
        $this->country = $assureur->country ?? 'CM';
        $this->website = $assureur->website ?? '';
        $this->notes = $assureur->notes ?? '';
        $this->is_active = $assureur->is_active;
        $this->showFormModal = true;
    }

    public function saveAssureur(): void
    {
        $this->validate();
        $data = [
            'name' => $this->name,
            'code' => $this->code ?: null,
            'contact_person' => $this->contact_person ?: null,
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'address' => $this->address ?: null,
            'city' => $this->city ?: null,
            'country' => $this->country ?: 'CM',
            'website' => $this->website ?: null,
            'notes' => $this->notes ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            Assureur::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Assureur mis à jour.');
        } else {
            Assureur::create($data);
            $this->dispatch('notify', type: 'success', message: 'Assureur créé.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteAssureur(): void
    {
        $assureur = Assureur::findOrFail($this->editingId);
        
        // Vérifier si des assurances sont liées
        if ($assureur->insurances()->count() > 0) {
            $this->dispatch('notify', type: 'error', message: 'Impossible de supprimer cet assureur car il a des assurances liées.');
            $this->showDeleteModal = false;
            return;
        }
        
        $assureur->delete();
        $this->dispatch('notify', type: 'success', message: 'Assureur supprimé.');
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->code = '';
        $this->contact_person = '';
        $this->phone = '';
        $this->email = '';
        $this->address = '';
        $this->city = '';
        $this->country = 'CM';
        $this->website = '';
        $this->notes = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    public function render(): View
    {
        $query = Assureur::query();
        
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhere('contact_person', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }
        
        if ($this->status_filter === '1') {
            $query->where('is_active', true);
        } elseif ($this->status_filter === '0') {
            $query->where('is_active', false);
        }
        
        $assureurs = $query->orderBy('name')->paginate(12);
        
        // Statistiques
        $stats = [
            'total' => Assureur::count(),
            'active' => Assureur::where('is_active', true)->count(),
            'inactive' => Assureur::where('is_active', false)->count(),
        ];

        return view('livewire.portal.assureurs.index', [
            'assureurs' => $assureurs,
            'stats' => $stats,
        ]);
    }
}
