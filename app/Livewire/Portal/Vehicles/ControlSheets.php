<?php

namespace App\Livewire\Portal\Vehicles;

use App\Models\Driver;
use App\Models\Mission;
use App\Models\Vehicle;
use App\Models\VehicleControlSheet;
use App\Models\VehicleControlSheetPhoto;
use App\Models\VehicleSchedule;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ControlSheets extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public bool $showFormModal  = false;
    public bool $showDeleteModal = false;
    public bool $showViewModal   = false;
    public ?int $editingId = null;
    public ?int $viewingId = null;

    // Champs fiche
    public ?int    $vehicle_id          = null;
    public ?int    $mission_id          = null;
    public ?int    $vehicle_schedule_id = null;
    public ?int    $driver_id           = null;
    public string  $ordre_mission = '';
    public string  $lieu          = '';
    public string  $date_depart   = '';
    public string  $date_retour   = '';
    public string  $km_depart     = '';
    public string  $km_retour     = '';
    public array   $docs_administratifs     = [];
    public array   $controle_exterieur      = [];
    public array   $compartiment_moteur     = [];
    public array   $controle_fonctionnalites = [];
    public array   $outillages              = [];
    public string  $observations_depart = '';
    public string  $observations_retour = '';
    public ?string $signature_depart_path = null;
    public ?string $signature_retour_path = null;
    public ?string $signature_bureau_path = null;

    // Photos
    public bool  $showPhotoModal = false;
    public ?int  $photoSheetId   = null;
    public string $photo_type    = 'before';
    public $before_photos = [];
    public $after_photos  = [];

    protected $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        $this->initChecks();

        // Pré-remplissage depuis l'URL : ?mission_id=X ou ?schedule_id=X
        $missionId  = request()->query('mission_id');
        $scheduleId = request()->query('schedule_id');

        if ($missionId) {
            $mission = Mission::find($missionId);
            if ($mission) {
                $this->mission_id  = $mission->id;
                $this->vehicle_id  = $mission->vehicle_id;
                $this->driver_id   = $mission->driver_id;
                $this->date_depart = $mission->date_start->format('Y-m-d');
                $this->date_retour = $mission->date_end->format('Y-m-d');
                $this->km_depart   = $mission->km_departure !== null ? (string) $mission->km_departure : '';
                $this->km_retour   = $mission->km_return !== null ? (string) $mission->km_return : '';
                $this->lieu        = $mission->destination ?? '';
                $this->showFormModal = true;
            }
        } elseif ($scheduleId) {
            $schedule = VehicleSchedule::find($scheduleId);
            if ($schedule) {
                $this->vehicle_schedule_id = $schedule->id;
                $this->vehicle_id          = $schedule->vehicle_id;
                $this->driver_id           = $schedule->driver_id;
                $this->date_depart         = $schedule->start_datetime->format('Y-m-d');
                $this->date_retour         = $schedule->end_datetime->format('Y-m-d');
                $this->km_depart           = $schedule->mileage_start !== null ? (string) $schedule->mileage_start : '';
                $this->lieu                = $schedule->destination ?? '';
                $this->showFormModal = true;
            }
        }
    }

    private function initChecks(): void
    {
        $structure = VehicleControlSheet::defaultStructure();
        foreach ($structure as $section => $items) {
            $this->$section = $items;
        }
    }

    /**
     * Toggle a checkbox value in a nested check section.
     * For string-valued fields (docs): toggles between $onValue and null.
     * For boolean fields (check4/outillage): toggles between true and false.
     */
    public function toggleCheck(string $section, string $item, string $key, mixed $onValue = true): void
    {
        $arr = $this->$section;
        $current = $arr[$item][$key] ?? null;
        $offValue = is_string($onValue) ? null : false;
        $arr[$item][$key] = ($current === $onValue) ? $offValue : $onValue;
        $this->$section = $arr;
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'vehicle_id', 'mission_id', 'vehicle_schedule_id', 'driver_id',
            'ordre_mission', 'lieu', 'date_depart', 'date_retour',
            'km_depart', 'km_retour', 'observations_depart', 'observations_retour',
            'signature_depart_path', 'signature_retour_path', 'signature_bureau_path']);
        $this->initChecks();
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $sheet = VehicleControlSheet::findOrFail($id);
        $this->editingId   = $id;
        $this->vehicle_id  = $sheet->vehicle_id;
        $this->mission_id  = $sheet->mission_id;
        $this->driver_id   = $sheet->driver_id;
        $this->ordre_mission = $sheet->ordre_mission ?? '';
        $this->lieu          = $sheet->lieu ?? '';
        $this->date_depart   = $sheet->date_depart?->format('Y-m-d') ?? '';
        $this->date_retour   = $sheet->date_retour?->format('Y-m-d') ?? '';
        $this->km_depart     = $sheet->km_depart !== null ? (string)$sheet->km_depart : '';
        $this->km_retour     = $sheet->km_retour !== null ? (string)$sheet->km_retour : '';
        $this->observations_depart = $sheet->observations_depart ?? '';
        $this->observations_retour = $sheet->observations_retour ?? '';
        $this->signature_depart_path = $sheet->signature_depart_path;
        $this->signature_retour_path = $sheet->signature_retour_path;
        $this->signature_bureau_path = $sheet->signature_bureau_path;

        // Merge saved data with default structure (to handle new keys)
        $defaults = VehicleControlSheet::defaultStructure();
        foreach (['docs_administratifs','controle_exterieur','compartiment_moteur','controle_fonctionnalites','outillages'] as $section) {
            $saved = $sheet->$section ?? [];
            $this->$section = array_merge($defaults[$section], $saved);
        }

        $this->showFormModal = true;
    }

    public function openView(int $id): void
    {
        $this->viewingId = $id;
        $this->showViewModal = true;
    }

    public function saveSheet(string $sigDepart = '', string $sigRetour = '', string $sigBureau = ''): void
    {
        $this->validate([
            'vehicle_id'  => 'required|exists:vehicles,id',
            'date_depart' => 'required|date',
            'km_depart'   => 'nullable|integer|min:0',
            'km_retour'   => 'nullable|integer|min:0',
        ]);

        $data = [
            'vehicle_id'               => $this->vehicle_id,
            'mission_id'               => $this->mission_id ?: null,
            'vehicle_schedule_id'      => $this->vehicle_schedule_id ?: null,
            'driver_id'                => $this->driver_id ?: null,
            'created_by'               => auth()->id(),
            'ordre_mission'            => $this->ordre_mission ?: null,
            'lieu'                     => $this->lieu ?: null,
            'date_depart'              => $this->date_depart,
            'date_retour'              => $this->date_retour ?: null,
            'km_depart'                => $this->km_depart !== '' ? (int)$this->km_depart : null,
            'km_retour'                => $this->km_retour !== '' ? (int)$this->km_retour : null,
            'docs_administratifs'      => $this->docs_administratifs,
            'controle_exterieur'       => $this->controle_exterieur,
            'compartiment_moteur'      => $this->compartiment_moteur,
            'controle_fonctionnalites' => $this->controle_fonctionnalites,
            'outillages'               => $this->outillages,
            'observations_depart'      => $this->observations_depart ?: null,
            'observations_retour'      => $this->observations_retour ?: null,
            'signature_depart_path'    => $this->signature_depart_path,
            'signature_retour_path'    => $this->signature_retour_path,
            'signature_bureau_path'    => $this->signature_bureau_path,
        ];

        if ($sigDepart) {
            $data['signature_depart_path'] = $this->storeBase64Signature($sigDepart);
        }
        if ($sigRetour) {
            $data['signature_retour_path'] = $this->storeBase64Signature($sigRetour);
        }
        if ($sigBureau) {
            $data['signature_bureau_path'] = $this->storeBase64Signature($sigBureau);
        }

        if ($this->editingId) {
            VehicleControlSheet::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Fiche mise à jour.');
        } else {
            VehicleControlSheet::create($data);
            $this->dispatch('notify', type: 'success', message: 'Fiche de contrôle créée.');
        }

        $this->showFormModal = false;
        $this->resetPage();
    }

    private function storeBase64Signature(string $base64): string
    {
        $raw = preg_replace('#^data:image/\w+;base64,#i', '', $base64);
        $decoded = base64_decode($raw, true);
        $path = 'control-sheets/signatures/' . uniqid('sig_') . '.png';
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $decoded);
        return $path;
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteSheet(): void
    {
        if ($this->editingId) {
            VehicleControlSheet::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Fiche supprimée.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    public function openPhotoModal(int $sheetId, string $type = 'before'): void
    {
        $this->photoSheetId  = $sheetId;
        $this->photo_type    = $type;
        $this->before_photos = [];
        $this->after_photos  = [];
        $this->showPhotoModal = true;
    }

    public function savePhotos(): void
    {
        $this->validate([
            'before_photos.*' => 'image|max:8192',
            'after_photos.*'  => 'image|max:8192',
        ]);

        $sheet = VehicleControlSheet::findOrFail($this->photoSheetId);

        foreach ($this->before_photos as $photo) {
            VehicleControlSheetPhoto::storeUpload($sheet, $photo, 'before');
        }
        foreach ($this->after_photos as $photo) {
            VehicleControlSheetPhoto::storeUpload($sheet, $photo, 'after');
        }

        $this->dispatch('notify', type: 'success', message: 'Photos enregistrées.');
        $this->showPhotoModal = false;
        $this->before_photos = [];
        $this->after_photos  = [];
    }

    public function deletePhoto(int $photoId): void
    {
        $photo = VehicleControlSheetPhoto::findOrFail($photoId);
        if (file_exists(storage_path('app/public/' . $photo->file_path))) {
            unlink(storage_path('app/public/' . $photo->file_path));
        }
        $photo->delete();
        $this->dispatch('notify', type: 'success', message: 'Photo supprimée.');
    }

    public function render(): View
    {
        $query = VehicleControlSheet::query()
            ->with(['vehicle:id,registration', 'mission:id,destination', 'driver:id,first_name,last_name'])
            ->withCount('photos');

        if ($this->search !== '') {
            $query->whereHas('vehicle', fn($q) => $q->where('registration', 'like', '%' . $this->search . '%'));
        }

        $sheets   = $query->orderByDesc('date_depart')->paginate(15);
        $vehicles = Vehicle::orderBy('registration')->get(['id', 'registration']);
        $drivers  = Driver::orderBy('last_name')->get(['id', 'first_name', 'last_name']);
        $missions = Mission::whereIn('status', ['approved', 'completed'])
            ->orderByDesc('date_start')->limit(100)->get(['id', 'destination', 'date_start']);

        $viewingSheet = $this->viewingId
            ? VehicleControlSheet::with(['vehicle', 'driver', 'mission', 'photos'])->find($this->viewingId)
            : null;

        return view('livewire.portal.vehicles.control-sheets', [
            'sheets'       => $sheets,
            'vehicles'     => $vehicles,
            'drivers'      => $drivers,
            'missions'     => $missions,
            'viewingSheet' => $viewingSheet,
            'labels'       => VehicleControlSheet::labels(),
        ])->layout('layouts.app', ['title' => 'Fiches de contrôle véhicules']);
    }
}
