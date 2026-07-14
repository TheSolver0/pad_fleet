<?php

namespace App\Livewire\Portal\Roles;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    private const GUARD = 'web';
    private const PROTECTED_ROLE = 'Administrateur';

    /**
     * Regroupement des permissions par domaine, pour l'affichage du formulaire.
     * Toute permission non listée ici tombe dans le groupe "Autres".
     */
    private const PERMISSION_GROUPS = [
        'Administration' => ['parametrage-systeme', 'gestion-utilisateurs', 'gestion-roles', 'exports-massifs', 'archives'],
        'Garage'         => ['planification-maintenance', 'affectation-mecaniciens', 'validation-bons-sortie', 'ordres-travail'],
        'Mécanique'      => ['enregistrement-interventions', 'demande-pieces', 'comptes-rendus', 'photos-avant-apres'],
        'Stock'          => ['gestion-stock', 'entrees-stock', 'sorties-stock', 'reception-commandes', 'sorties-pieces', 'inventaires', 'alertes-stock'],
        'Flotte'         => ['planning-missions', 'affectations-vehicules-chauffeurs', 'suivi-assurances', 'sinistres', 'rapports-flotte'],
        'Chauffeur'      => ['consultation-missions', 'saisie-km', 'signalement-anomalies', 'carnets-bord'],
        'Direction / Services' => ['demandes-reservation', 'consultation-disponibilites', 'approbations', 'suivi-budget'],
        'Finance'        => ['rapports-financiers', 'tco', 'budgets', 'factures', 'analyses-couts', 'ecarts'],
        'Pilotage'       => ['tableaux-bord-strategiques', 'kpis', 'aide-decision', 'audits'],
    ];

    public string $search = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public string $name = '';
    /** @var array<int, string> */
    public array $selectedPermissions = [];

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'bootstrap';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->where('guard_name', self::GUARD)->ignore($this->editingId)],
            'selectedPermissions' => 'array',
            'selectedPermissions.*' => 'string|exists:permissions,name',
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
        $role = Role::with('permissions')->findOrFail($id);
        $this->editingId = $role->id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function saveRole(): void
    {
        $this->validate();

        if ($this->editingId) {
            $role = Role::findOrFail($this->editingId);
            if ($role->name === self::PROTECTED_ROLE && $this->name !== self::PROTECTED_ROLE) {
                $this->dispatch('notify', type: 'error', message: 'Le rôle "' . self::PROTECTED_ROLE . '" ne peut pas être renommé.');
                return;
            }
            $role->update(['name' => $this->name]);
            $message = 'Rôle mis à jour.';
        } else {
            $role = Role::create(['name' => $this->name, 'guard_name' => self::GUARD]);
            $message = 'Rôle créé.';
        }

        $role->syncPermissions($this->selectedPermissions);

        $this->dispatch('notify', type: 'success', message: $message);
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $role = Role::withCount('users')->findOrFail($id);

        if ($role->name === self::PROTECTED_ROLE) {
            $this->dispatch('notify', type: 'error', message: 'Le rôle "' . self::PROTECTED_ROLE . '" ne peut pas être supprimé.');
            return;
        }

        if ($role->users_count > 0) {
            $this->dispatch('notify', type: 'error', message: 'Ce rôle est encore affecté à des utilisateurs, il ne peut pas être supprimé.');
            return;
        }

        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRole(): void
    {
        if ($this->editingId) {
            $role = Role::withCount('users')->findOrFail($this->editingId);
            if ($role->name !== self::PROTECTED_ROLE && $role->users_count === 0) {
                $role->delete();
                $this->dispatch('notify', type: 'success', message: 'Rôle supprimé.');
            }
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'selectedPermissions']);
        $this->resetValidation();
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function permissionGroups(): array
    {
        $known = Permission::pluck('name')->all();
        $groups = [];

        foreach (self::PERMISSION_GROUPS as $label => $names) {
            $groups[$label] = array_values(array_intersect($names, $known));
        }

        $grouped = array_merge(...array_values(self::PERMISSION_GROUPS));
        $others = array_values(array_diff($known, $grouped));
        if (!empty($others)) {
            $groups['Autres'] = $others;
        }

        return array_filter($groups);
    }

    public function render(): View
    {
        $query = Role::query()->where('guard_name', self::GUARD)->withCount(['permissions', 'users']);
        if ($this->search !== '') {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        $roles = $query->orderBy('name')->paginate(12);

        return view('livewire.portal.roles.index', [
            'roles' => $roles,
            'permissionGroups' => $this->permissionGroups(),
        ])->layout('layouts.app', ['title' => 'Rôles & permissions']);
    }
}
