<?php

namespace App\Livewire\Portal\Drivers;

use App\Models\Direction;
use App\Models\Driver;
use App\Models\DriverAssignment;
use App\Models\Person;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Mise à disposition d'un chauffeur auprès d'une direction ou d'un agent/directeur.
 */
class Dispatches extends Component
{
    use WithPagination;

    public string $search        = '';
    public string $status_filter = DriverAssignment::STATUS_ACTIVE;
    public bool   $showFormModal   = false;
    public bool   $showDeleteModal = false;
    public ?int   $editingId       = null;

    public ?int    $driver_id    = null;
    public string  $type         = DriverAssignment::TYPE_DIRECTION;
    public ?int    $direction_id = null;
    public ?int    $person_id    = null;
    public string  $started_at   = '';
    public string  $ended_at     = '';
    public string  $notes        = '';

    protected $paginationTheme = 'bootstrap';

    protected function rules(): array
    {
        return [
            'driver_id'    => 'required|exists:drivers,id',
            'type'         => 'required|in:direction,person',
            'direction_id' => [
                'nullable',
                $this->type === 'direction' ? 'required' : 'sometimes',
                'exists:directions,id',
            ],
            'person_id'    => [
                'nullable',
                $this->type === 'person' ? 'required' : 'sometimes',
                'exists:persons,id',
            ],
            'started_at'   => 'required|date',
            'ended_at'     => 'nullable|date|after_or_equal:started_at',
            'notes'        => 'nullable|string|max:1000',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'driver_id', 'direction_id', 'person_id', 'started_at', 'ended_at', 'notes']);
        $this->type      = DriverAssignment::TYPE_DIRECTION;
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $a = DriverAssignment::findOrFail($id);
        $this->editingId    = $id;
        $this->driver_id    = $a->driver_id;
        $this->type         = $a->type;
        $this->direction_id = $a->direction_id;
        $this->person_id    = $a->person_id;
        $this->started_at   = $a->started_at?->format('Y-m-d') ?? '';
        $this->ended_at     = $a->ended_at?->format('Y-m-d') ?? '';
        $this->notes        = $a->notes ?? '';
        $this->showFormModal = true;
    }

    public function saveDispatch(): void
    {
        $this->validate();

        $data = [
            'driver_id'    => $this->driver_id,
            'type'         => $this->type,
            'direction_id' => $this->type === DriverAssignment::TYPE_DIRECTION ? $this->direction_id : null,
            'person_id'    => $this->type === DriverAssignment::TYPE_PERSON    ? $this->person_id    : null,
            'vehicle_id'   => null,
            'status'       => DriverAssignment::STATUS_ACTIVE,
            'started_at'   => $this->started_at,
            'ended_at'     => $this->ended_at ?: null,
            'notes'        => $this->notes ?: null,
            'created_by'   => Auth::id(),
        ];

        if ($this->editingId) {
            DriverAssignment::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Mise à disposition mise à jour.');
        } else {
            DriverAssignment::create($data);
            $this->dispatch('notify', type: 'success', message: 'Chauffeur mis à disposition.');
        }

        $this->showFormModal = false;
        $this->resetPage();
    }

    public function endDispatch(int $id): void
    {
        DriverAssignment::findOrFail($id)->update([
            'status'    => DriverAssignment::STATUS_ENDED,
            'ended_at'  => now()->format('Y-m-d'),
            'updated_by'=> Auth::id(),
        ]);
        $this->dispatch('notify', type: 'success', message: 'Mise à disposition clôturée.');
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId     = $id;
        $this->showDeleteModal = true;
    }

    public function deleteDispatch(): void
    {
        if ($this->editingId) {
            DriverAssignment::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Mise à disposition supprimée.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    public function render(): View
    {
        $query = DriverAssignment::query()
            ->with(['driver', 'direction:id,name', 'person:id,first_name,last_name'])
            ->whereIn('type', [DriverAssignment::TYPE_DIRECTION, DriverAssignment::TYPE_PERSON]);

        if ($this->search !== '') {
            $query->whereHas('driver', fn ($q) =>
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name',  'like', '%' . $this->search . '%')
            );
        }
        if ($this->status_filter !== '') {
            $query->where('status', $this->status_filter);
        }

        $dispatches  = $query->orderByDesc('started_at')->paginate(15);
        $drivers     = Driver::orderBy('last_name')->get(['id', 'first_name', 'last_name']);
        $directions  = Direction::orderBy('name')->get(['id', 'name']);
        $persons     = Person::orderBy('last_name')->get(['id', 'first_name', 'last_name']);

        return view('livewire.portal.drivers.dispatches', [
            'dispatches' => $dispatches,
            'drivers'    => $drivers,
            'directions' => $directions,
            'persons'    => $persons,
        ])->layout('layouts.app', ['title' => 'Mise à disposition des chauffeurs']);
    }
}
