<?php

namespace App\Livewire\Portal\Drivers;

use App\Models\Driver;
use App\Models\DrivingLicense;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DrivingLicenses extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public string $status_filter = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $driver_id = null;

    public string $license_number = '';
    public string $license_type = 'B';
    public string $category = 'B';
    public string $issue_date = '';
    public string $expiry_date = '';
    public string $issuing_authority = '';
    public string $issuing_country = 'CM';
    public bool $is_active = true;
    public string $notes = '';
    public $license_file = null;
    /** Chemin du fichier déjà enregistré (affichage aperçu/téléchargement en édition). */
    public ?string $existing_file_path = null;

    protected $queryString = ['search' => ['except' => ''], 'status_filter' => ['except' => '']];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'driver_id' => 'required|exists:drivers,id',
            'license_number' => 'required|string|max:50|unique:driving_licenses,license_number,' . ($this->editingId ?? 'NULL'),
            'license_type' => 'required|in:A,B,C,D,E,F,G,H,I',
            'category' => 'required|string|max:10',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'issuing_authority' => 'required|string|max:200',
            'issuing_country' => 'required|string|max:100',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
            'license_file' => 'nullable|file|max:2048|mimes:pdf,jpg,jpeg,png',
        ];
    }

    public function openCreate(?int $driverId = null): void
    {
        $this->resetForm();
        $this->driver_id = $driverId;
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $license = DrivingLicense::findOrFail($id);
        $this->editingId = $license->id;
        $this->driver_id = $license->driver_id;
        $this->license_number = $license->license_number;
        $this->license_type = $license->license_type;
        $this->category = $license->category;
        $this->issue_date = $license->issue_date->format('Y-m-d');
        $this->expiry_date = $license->expiry_date->format('Y-m-d');
        $this->issuing_authority = $license->issuing_authority;
        $this->issuing_country = $license->issuing_country;
        $this->is_active = $license->is_active;
        $this->notes = $license->notes ?? '';
        $this->existing_file_path = $license->file_path;
        $this->license_file = null;
        $this->showFormModal = true;
    }

    public function saveLicense(): void
    {
        $this->validate();
        
        $data = [
            'driver_id' => $this->driver_id,
            'license_number' => $this->license_number,
            'license_type' => $this->license_type,
            'category' => $this->category,
            'issue_date' => $this->issue_date,
            'expiry_date' => $this->expiry_date,
            'issuing_authority' => $this->issuing_authority,
            'issuing_country' => $this->issuing_country,
            'is_active' => $this->is_active,
            'notes' => $this->notes ?: null,
        ];

        if ($this->license_file) {
            $data['file_path'] = $this->license_file->store('driving-licenses', 'public');
        }

        if ($this->editingId) {
            DrivingLicense::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Permis de conduire mis à jour.');
        } else {
            DrivingLicense::create($data);
            $this->dispatch('notify', type: 'success', message: 'Permis de conduire créé.');
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteLicense(): void
    {
        if ($this->editingId) {
            $license = DrivingLicense::findOrFail($this->editingId);
            
            // Supprimer le fichier
            if ($license->file_path && file_exists(storage_path('app/public/' . $license->file_path))) {
                unlink(storage_path('app/public/' . $license->file_path));
            }
            
            $license->delete();
            $this->dispatch('notify', type: 'success', message: 'Permis de conduire supprimé.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    public function toggleActive(int $id): void
    {
        $license = DrivingLicense::findOrFail($id);
        $license->update(['is_active' => !$license->is_active]);
        $status = $license->is_active ? 'activé' : 'désactivé';
        $this->dispatch('notify', type: 'success', message: "Permis de conduire {$status}.");
    }

    public function getLicenseTypeLabel(string $type): string
    {
        return match($type) {
            'A' => 'Moto',
            'B' => 'Voiture',
            'C' => 'Poids lourd',
            'D' => 'Autobus',
            'E' => 'Remorque',
            'F' => 'Agricole',
            'G' => 'Engin spécial',
            'H' => 'Transport en commun',
            'I' => 'Transport de marchandises',
            default => $type,
        };
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->driver_id = null;
        $this->license_number = '';
        $this->license_type = 'B';
        $this->category = 'B';
        $this->issue_date = '';
        $this->expiry_date = '';
        $this->issuing_authority = '';
        $this->issuing_country = 'CM';
        $this->is_active = true;
        $this->notes = '';
        $this->license_file = null;
        $this->existing_file_path = null;
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = DrivingLicense::with(['driver']);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('license_number', 'like', '%' . $this->search . '%')
                    ->orWhere('category', 'like', '%' . $this->search . '%')
                    ->orWhereHas('driver', function ($q2) {
                        $q2->where('first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->status_filter !== '') {
            match($this->status_filter) {
                'active' => $query->where('is_active', true)->where('expiry_date', '>=', now()),
                'expired' => $query->where('expiry_date', '<', now()),
                'expiring' => $query->where('expiry_date', '>', now())->where('expiry_date', '<=', now()->addDays(30)),
                'inactive' => $query->where('is_active', false),
                default => null,
            };
        }

        $licenses = $query->orderBy('expiry_date', 'desc')->paginate(20);
        
        $drivers = Driver::orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'last_name']);

        return view('livewire.portal.drivers.driving-licenses', [
            'licenses' => $licenses,
            'drivers' => $drivers,
        ]);
    }
}
