<?php

namespace App\Livewire\Portal\Schedules;

use App\Models\Driver;
use App\Models\City;
use App\Models\Region;
use App\Models\Vehicle;
use App\Models\VehicleSchedule;
use App\Models\VehicleScheduleDocument;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    private const KNOWN_PURPOSES = ['transport_personnel', 'livraison', 'mission', 'maintenance'];

    public string $search = '';
    public string $status_filter = '';
    public string $date_filter = '';
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;

    public ?int $vehicle_id = null;
    public ?int $driver_id = null;
    public string $title = '';
    public string $description = '';
    public string $destination = '';
    public ?int $city_id = null;
    public bool $create_city = false;
    public string $new_city_name = '';
    public ?int $new_region_id = null;
    public string $departure_location = '';
    public string $start_datetime = '';
    public string $end_datetime = '';
    public string $estimated_distance = '';
    public string $purpose = '';
    public string $purpose_other = '';
    public string $status = VehicleSchedule::STATUS_PLANNED;
    public string $mileage_start = '';
    public string $mileage_end = '';
    public string $fuel_consumed = '';
    public string $notes = '';

    // Lignes documents de validation dans le formulaire (type + note individuelle)
    public array $form_doc_rows = [
        ['file' => null, 'type' => 'ordre_mission', 'note' => '']
    ];

    // Modal de gestion des documents existants
    public $documents = [];
    public bool $showDocumentModal = false;
    public ?int $documentScheduleId = null;
    public string $document_type = 'autre';
    public string $document_note = '';

    protected $queryString = ['search' => ['except' => ''], 'status_filter' => ['except' => ''], 'date_filter' => ['except' => '']];
    protected $paginationTheme = 'bootstrap'; 

    protected function rules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'destination' => 'nullable|string|max:200',
            'city_id' => 'nullable|exists:cities,id',
            'new_city_name' => 'nullable|required_if:create_city,true|string|max:100',
            'new_region_id' => 'nullable|required_if:create_city,true|exists:regions,id',
            'departure_location' => 'nullable|string|max:200',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'estimated_distance' => 'nullable|numeric|min:0',
            'purpose' => 'nullable|string|max:100',
            'purpose_other' => 'nullable|required_if:purpose,autre|string|max:150',
            'status' => 'required|in:planned,in_progress,completed,cancelled',
            'mileage_start' => 'nullable|integer|min:0',
            'mileage_end' => 'nullable|integer|min:0',
            'fuel_consumed' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'form_doc_rows.*.file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
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
        $schedule = VehicleSchedule::findOrFail($id);
        $this->editingId = $schedule->id;
        $this->vehicle_id = $schedule->vehicle_id;
        $this->driver_id = $schedule->driver_id;
        $this->title = $schedule->title;
        $this->description = $schedule->description ?? '';
        $this->destination = $schedule->destination;
        $this->departure_location = $schedule->departure_location ?? '';
        $this->start_datetime = $schedule->start_datetime->format('Y-m-d\TH:i');
        $this->end_datetime = $schedule->end_datetime->format('Y-m-d\TH:i');
        $this->estimated_distance = $schedule->estimated_distance ? number_format($schedule->estimated_distance, 2, ',', ' ') : '';
        if ($schedule->purpose && !in_array($schedule->purpose, self::KNOWN_PURPOSES, true)) {
            $this->purpose = 'autre';
            $this->purpose_other = $schedule->purpose;
        } else {
            $this->purpose = $schedule->purpose ?? '';
            $this->purpose_other = '';
        }
        $this->status = $schedule->status;
        $this->mileage_start = $schedule->mileage_start ? (string) $schedule->mileage_start : '';
        $this->mileage_end = $schedule->mileage_end ? (string) $schedule->mileage_end : '';
        $this->fuel_consumed = $schedule->fuel_consumed ? number_format($schedule->fuel_consumed, 2, ',', ' ') : '';
        $this->notes = $schedule->notes ?? '';
        $this->showFormModal = true;
    }

    public function saveSchedule(): void
    {
        $this->estimated_distance = str_replace([' ', ','], ['', '.'], $this->estimated_distance);
        $this->fuel_consumed = str_replace([' ', ','], ['', '.'], $this->fuel_consumed);
        
        $this->validate();

        $cityName = null;
        if ($this->create_city) {
            $city = City::create([
                'name' => trim($this->new_city_name),
                'region_id' => $this->new_region_id,
                'region' => Region::whereKey($this->new_region_id)->value('name'),
                'is_active' => true,
            ]);
            $cityName = $city->name;
            $this->city_id = $city->id;
        } elseif ($this->city_id) {
            $cityName = City::whereKey($this->city_id)->value('name');
        }

        $finalDestination = trim($this->destination);
        if ($cityName) {
            $finalDestination = $finalDestination !== '' ? ($finalDestination . ' - ' . $cityName) : $cityName;
        }
        if ($finalDestination === '') {
            $this->addError('destination', 'Veuillez choisir une ville ou saisir une destination.');
            return;
        }
        
        $data = [
            'vehicle_id' => $this->vehicle_id,
            'driver_id' => $this->driver_id ?: null,
            'title' => $this->title,
            'description' => $this->description ?: null,
            'destination' => $finalDestination,
            'departure_location' => $this->departure_location ?: null,
            'start_datetime' => $this->start_datetime,
            'end_datetime' => $this->end_datetime,
            'estimated_distance' => $this->estimated_distance ?: null,
            'purpose' => $this->purpose === 'autre' ? trim($this->purpose_other) : ($this->purpose ?: null),
            'status' => $this->status,
            'mileage_start' => $this->mileage_start ?: null,
            'mileage_end' => $this->mileage_end ?: null,
            'fuel_consumed' => $this->fuel_consumed ?: null,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editingId) {
            $schedule = VehicleSchedule::findOrFail($this->editingId);
            $schedule->update($data);
            $this->dispatch('notify', type: 'success', message: 'Planning mis à jour.');
        } else {
            $schedule = VehicleSchedule::create($data);
            $this->dispatch('notify', type: 'success', message: 'Planning créé.');
        }

        // Enregistrer les documents de validation joints au formulaire
        foreach ($this->form_doc_rows as $row) {
            if (!empty($row['file'])) {
                VehicleScheduleDocument::storeUpload($schedule, $row['file'], $row['type'], $row['note'] ?: null);
            }
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteSchedule(): void
    {
        if ($this->editingId) {
            VehicleSchedule::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Planning supprimé.');
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
        $this->vehicle_id = null;
        $this->driver_id = null;
        $this->title = '';
        $this->description = '';
        $this->destination = '';
        $this->city_id = null;
        $this->create_city = false;
        $this->new_city_name = '';
        $this->new_region_id = null;
        $this->departure_location = '';
        $this->start_datetime = '';
        $this->end_datetime = '';
        $this->estimated_distance = '';
        $this->purpose = '';
        $this->purpose_other = '';
        $this->status = VehicleSchedule::STATUS_PLANNED;
        $this->mileage_start = '';
        $this->mileage_end = '';
        $this->fuel_consumed = '';
        $this->notes = '';
        $this->form_doc_rows = [['file' => null, 'type' => 'ordre_mission', 'note' => '']];
        $this->resetValidation();
    }

    public function addFormDocRow(): void
    {
        $this->form_doc_rows[] = ['file' => null, 'type' => 'ordre_mission', 'note' => ''];
    }

    public function removeFormDocRow(int $index): void
    {
        if (count($this->form_doc_rows) > 1) {
            array_splice($this->form_doc_rows, $index, 1);
        }
    }

    public function openDocumentModal(int $scheduleId): void
    {
        $this->documentScheduleId = $scheduleId;
        $this->documents = [];
        $this->document_type = 'autre';
        $this->showDocumentModal = true;
    }

    public function saveDocuments(): void
    {
        $this->validate([
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $schedule = VehicleSchedule::findOrFail($this->documentScheduleId);

        foreach ($this->documents as $file) {
            VehicleScheduleDocument::storeUpload($schedule, $file, $this->document_type, $this->document_note ?: null);
        }

        $this->dispatch('notify', type: 'success', message: 'Documents enregistrés.');
        $this->showDocumentModal = false;
        $this->documents = [];
        $this->document_note = '';
    }

    public function deleteDocument(int $documentId): void
    {
        VehicleScheduleDocument::findOrFail($documentId)->delete();
        $this->dispatch('notify', type: 'success', message: 'Document supprimé.');
    }

    public function render(): View
    {
        $query = VehicleSchedule::with(['vehicle', 'driver'])->withCount(['documents', 'controlSheets']);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('destination', 'like', '%' . $this->search . '%')
                    ->orWhereHas('vehicle', function ($q2) {
                        $q2->where('registration', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('driver', function ($q3) {
                        $q3->where('first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->status_filter !== '') {
            $query->where('status', $this->status_filter);
        }

        if ($this->date_filter !== '') {
            $date = Carbon::parse($this->date_filter);
            $query->whereDate('start_datetime', $date);
        }

        $schedules = $query->orderBy('start_datetime', 'desc')->paginate(20);
        $vehicles = Vehicle::where('status', '!=', 'out_of_service')->orderBy('registration')->get(['id', 'registration']);
        $drivers = Driver::orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        $cities = City::active()->orderBy('name')->get(['id', 'name', 'region']);
        $regions = Region::orderBy('name')->get(['id', 'name']);

        return view('livewire.portal.schedules.index', [
            'schedules' => $schedules,
            'vehicles' => $vehicles,
            'drivers' => $drivers,
            'cities' => $cities,
            'regions' => $regions,
        ]);
    }
}
