<?php

namespace App\Livewire\Portal\Users;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $email = '';
    public string $matricule = '';
    public string $gender = '';
    public string $phone = '';
    public string $occupation = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $roleName = '';

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'bootstrap';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($this->editingId)],
            'matricule' => ['nullable', 'string', 'max:50', Rule::unique('users', 'matricule')->ignore($this->editingId)],
            'gender' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:30',
            'occupation' => 'nullable|string|max:150',
            'password' => $this->editingId ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed',
            'roleName' => 'nullable|string|exists:roles,name',
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
        $user = User::with('roles')->findOrFail($id);
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->matricule = $user->matricule ?? '';
        $this->gender = $user->gender ?? '';
        $this->phone = $user->phone ?? '';
        $this->occupation = $user->occupation ?? '';
        $this->roleName = $user->roles->first()->name ?? '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function saveUser(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'matricule' => $this->matricule ?: null,
            'gender' => $this->gender ?: null,
            'phone' => $this->phone ?: null,
            'occupation' => $this->occupation ?: null,
        ];

        if ($this->password !== '') {
            $data['password'] = $this->password;
        }

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);
            $user->update($data);
            $message = 'Utilisateur mis à jour.';
        } else {
            $user = User::create($data);
            $message = 'Utilisateur créé.';
        }

        $user->syncRoles($this->roleName !== '' ? [$this->roleName] : []);

        $this->dispatch('notify', type: 'success', message: $message);
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        if ($id === auth()->id()) {
            $this->dispatch('notify', type: 'error', message: 'Vous ne pouvez pas désactiver votre propre compte.');
            return;
        }

        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
        $this->dispatch('notify', type: 'success', message: $user->is_active ? 'Utilisateur activé.' : 'Utilisateur désactivé.');
    }

    public function confirmDelete(int $id): void
    {
        if ($id === auth()->id()) {
            $this->dispatch('notify', type: 'error', message: 'Vous ne pouvez pas supprimer votre propre compte.');
            return;
        }

        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteUser(): void
    {
        if ($this->editingId && $this->editingId !== auth()->id()) {
            User::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Utilisateur supprimé.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId', 'name', 'email', 'matricule', 'gender',
            'phone', 'occupation', 'password', 'password_confirmation', 'roleName',
        ]);
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = User::query()->with('roles');
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('matricule', 'like', '%' . $this->search . '%');
            });
        }
        $users = $query->orderBy('name')->paginate(12);
        $roles = Role::orderBy('name')->pluck('name');

        return view('livewire.portal.users.index', [
            'users' => $users,
            'roles' => $roles,
        ])->layout('layouts.app', ['title' => 'Utilisateurs']);
    }
}
