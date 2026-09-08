<?php

namespace App\Livewire\Portal\CityDistances;

use App\Models\City;
use App\Models\CityDistance;
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

    public ?int $from_city_id = null;
    public ?int $to_city_id = null;
    public string $distance_km = '';

    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'bootstrap';

    protected function rules(): array
    {
        return [
            'from_city_id' => 'required|exists:cities,id|different:to_city_id',
            'to_city_id' => 'required|exists:cities,id',
            'distance_km' => 'required|integer|min:1',
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
        $cd = CityDistance::findOrFail($id);
        $this->editingId = $cd->id;
        $this->from_city_id = $cd->from_city_id;
        $this->to_city_id = $cd->to_city_id;
        $this->distance_km = (string) $cd->distance_km;
        $this->showFormModal = true;
    }

    public function saveDistance(): void
    {
        $this->validate();
        $data = [
            'from_city_id' => $this->from_city_id,
            'to_city_id' => $this->to_city_id,
            'distance_km' => (int) $this->distance_km,
        ];
        if ($this->editingId) {
            CityDistance::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Distance mise à jour.');
        } else {
            CityDistance::updateOrCreate(
                ['from_city_id' => $data['from_city_id'], 'to_city_id' => $data['to_city_id']],
                $data
            );
            $this->dispatch('notify', type: 'success', message: 'Distance enregistrée.');
        }
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteDistance(): void
    {
        if ($this->editingId) {
            CityDistance::findOrFail($this->editingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Distance supprimée.');
        }
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    private function resetForm(): void
    {
        $this->from_city_id = null;
        $this->to_city_id = null;
        $this->distance_km = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = CityDistance::query()->with(['fromCity:id,name', 'toCity:id,name']);
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->whereHas('fromCity', fn ($c) => $c->where('name', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('toCity', fn ($c) => $c->where('name', 'like', '%' . $this->search . '%'));
            });
        }
        $distances = $query->orderBy('id', 'desc')->paginate(15);
        $cities = City::orderBy('name')->get(['id', 'name']);

        return view('livewire.portal.city-distances.index', [
            'distances' => $distances,
            'cities' => $cities,
        ])->layout('layouts.app', ['title' => 'Distances entre villes']);
    }
}
