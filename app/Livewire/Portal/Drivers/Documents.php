<?php

namespace App\Livewire\Portal\Drivers;

use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Documents extends Component
{
    public function render(): View
    {
        $user = Auth::user();
        $driver = Driver::where('user_id', $user?->id)->first();

        $vehicles = collect();
        if ($driver) {
            $vehicleIds = $driver->missions()
                ->whereNotNull('vehicle_id')
                ->pluck('vehicle_id')
                ->unique()
                ->values();

            $vehicles = Vehicle::with([
                'documents' => fn ($q) => $q->whereIn('type', ['assurance', 'carte_grise'])->orderByDesc('created_at'),
                'carteGrises' => fn ($q) => $q->orderByDesc('issued_at'),
            ])->whereIn('id', $vehicleIds)->orderBy('registration')->get();
        }

        return view('livewire.portal.drivers.documents', [
            'driver' => $driver,
            'vehicles' => $vehicles,
        ])->layout('layouts.app', ['title' => 'Documents chauffeur']);
    }
}
