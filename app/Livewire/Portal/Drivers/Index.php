<?php

namespace App\Livewire\Portal\Drivers;

use App\Models\Direction;
use App\Models\Driver;
use App\Models\DrivingLicense;
use App\Models\Person;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $availability_filter = '';
    public string $direction_filter = '';
    public string $person_filter = '';
    public string $license_status_filter = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public string $matricule = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $phone = '';
    public string $email = '';
    public ?int $direction_id = null;
    public ?int $resource_person_id = null;
    public bool $is_available = true;
    public string $notes = '';

    // Permis de conduire
    public array $driving_licenses = [];
    public bool $showLicenseForm = false;

    protected $queryString = ['search' => ['except' => ''], 'availability_filter' => ['except' => ''], 'direction_filter' => ['except' => ''], 'person_filter' => ['except' => ''], 'license_status_filter' => ['except' => '']];

    protected function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'matricule' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'direction_id' => 'nullable|exists:directions,id',
            'resource_person_id' => 'nullable|exists:persons,id',
            'is_available' => 'boolean',
            'notes' => 'nullable|string',
            'driving_licenses.*.license_number' => 'required|string|max:50',
            'driving_licenses.*.license_type' => 'required|in:A,B,C,D,E,F,G,H,I',
            'driving_licenses.*.category' => 'required|string|max:10',
            'driving_licenses.*.issue_date' => 'required|date',
            'driving_licenses.*.expiry_date' => 'required|date|after:driving_licenses.*.issue_date',
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
        $d = Driver::findOrFail($id);
        $this->editingId = $d->id;
        $this->matricule = $d->matricule ?? '';
        $this->first_name = $d->first_name;
        $this->last_name = $d->last_name;
        $this->phone = $d->phone ?? '';
        $this->email = $d->email ?? '';
        $this->direction_id = $d->direction_id;
        $this->resource_person_id = $d->resource_person_id;
        $this->is_available = $d->is_available;
        $this->notes = $d->notes ?? '';
        
        // Charger les permis existants
        $this->driving_licenses = $d->drivingLicenses->map(function($license) {
            return [
                'license_number' => $license->license_number,
                'license_type' => $license->license_type,
                'category' => $license->category,
                'issue_date' => $license->issue_date->format('Y-m-d'),
                'expiry_date' => $license->expiry_date->format('Y-m-d'),
            ];
        })->toArray();
        
        $this->showFormModal = true;
    }

    public function saveDriver(): void
    {
        $this->validate();
        $data = [
            'matricule' => $this->matricule ?: null,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'direction_id' => $this->direction_id,
            'resource_person_id' => $this->resource_person_id,
            'is_available' => $this->is_available,
            'notes' => $this->notes ?: null,
        ];
        if ($this->editingId) {
            $driver = Driver::findOrFail($this->editingId);
            $driver->update($data);
            
            // Mettre à jour les permis
            $driver->drivingLicenses()->delete();
            foreach ($this->driving_licenses as $licenseData) {
                $driver->drivingLicenses()->create([
                    'license_number' => $licenseData['license_number'],
                    'license_type' => $licenseData['license_type'],
                    'category' => $licenseData['category'],
                    'issue_date' => $licenseData['issue_date'],
                    'expiry_date' => $licenseData['expiry_date'],
                    'issuing_authority' => 'À compléter',
                    'issuing_country' => 'CM',
                    'is_active' => true,
                ]);
            }
            
            $this->dispatch('notify', type: 'success', message: 'Chauffeur mis à jour.');
        } else {
            $driver = Driver::create($data);
            
            // Créer les permis
            foreach ($this->driving_licenses as $licenseData) {
                $driver->drivingLicenses()->create([
                    'license_number' => $licenseData['license_number'],
                    'license_type' => $licenseData['license_type'],
                    'category' => $licenseData['category'],
                    'issue_date' => $licenseData['issue_date'],
                    'expiry_date' => $licenseData['expiry_date'],
                    'issuing_authority' => 'À compléter',
                    'issuing_country' => 'CM',
                    'is_active' => true,
                ]);
            }
            
            $this->dispatch('notify', type: 'success', message: 'Chauffeur créé.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteDriver(): void
    {
        if ($this->editingId) {
            Driver::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Chauffeur supprimé.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->matricule = '';
        $this->first_name = '';
        $this->last_name = '';
        $this->phone = '';
        $this->email = '';
        $this->direction_id = null;
        $this->resource_person_id = null;
        $this->is_available = true;
        $this->notes = '';
        $this->driving_licenses = [];
        $this->resetValidation();
    }

    public function addLicense(): void
    {
        $this->driving_licenses[] = [
            'license_number' => '',
            'license_type' => 'B',
            'category' => 'B',
            'issue_date' => now()->format('Y-m-d'),
            'expiry_date' => '',
        ];
    }

    public function removeLicense(int $index): void
    {
        unset($this->driving_licenses[$index]);
        $this->driving_licenses = array_values($this->driving_licenses);
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    public function render(): View
    {
        $query = Driver::query()->with(['direction:id,name', 'resourcePerson:id,first_name,last_name', 'drivingLicenses']);
        
        // Filtre recherche
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('matricule', 'like', '%' . $this->search . '%');
            });
        }
        
        // Filtre direction
        if ($this->direction_filter !== '') {
            $query->where('direction_id', $this->direction_filter);
        }
        
        // Filtre personne ressource
        if ($this->person_filter !== '') {
            $query->where('resource_person_id', $this->person_filter);
        }
        
        // Filtre statut permis
        if ($this->license_status_filter !== '') {
            match($this->license_status_filter) {
                'active' => $query->whereHas('drivingLicenses', function($q) {
                    $q->where('is_active', true)->where('expiry_date', '>=', now());
                }),
                'expired' => $query->whereHas('drivingLicenses', function($q) {
                    $q->where('expiry_date', '<', now());
                }),
                'none' => $query->whereDoesntHave('drivingLicenses'),
                default => null,
            };
        }
        
        // Filtre disponibilité
        if ($this->availability_filter === '1') {
            $query->where('is_available', true);
        } elseif ($this->availability_filter === '0') {
            $query->where('is_available', false);
        }
        
        $drivers = $query->orderBy('last_name')->paginate(12);
        $directions = Direction::orderBy('name')->get(['id', 'name']);
        $persons = Person::orderBy('name')->get(['id', 'name']);
        
        // Statistiques
        $stats = [
            'total' => Driver::count(),
            'active_licenses' => Driver::whereHas('drivingLicenses', function($q) {
                $q->where('is_active', true)->where('expiry_date', '>=', now());
            })->count(),
            'expired_licenses' => Driver::whereHas('drivingLicenses', function($q) {
                $q->where('expiry_date', '<', now());
            })->count(),
            'no_licenses' => Driver::whereDoesntHave('drivingLicenses')->count(),
            'available' => Driver::where('is_available', true)->count(),
        ];

        return view('livewire.portal.drivers.index', [
            'drivers' => $drivers,
            'directions' => $directions,
            'persons' => $persons,
            'stats' => $stats,
        ]);
    }
}
