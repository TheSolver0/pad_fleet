<?php

namespace App\Livewire\Portal\Reports;

use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class VehicleReform extends Component
{
    public string $filter = 'all'; // all, reformed, near_reform, ok
    public string $vehicle_category = '';
    public string $sort_by = 'reform_urgency'; // reform_urgency, maintenance_cost, cost_ratio

    private function computeAllVehiclesData(): array
    {
        $query = Vehicle::with([
            'vehicleModel.brand',
            'repairs' => fn ($q) => $q->with(['repairParts', 'expenses']),
        ])->whereNotNull('purchase_date');

        if ($this->vehicle_category !== '') {
            $query->where('category', $this->vehicle_category);
        }

        $now = Carbon::now();
        $data = [];

        foreach ($query->get() as $vehicle) {
            $reformDate = $vehicle->purchase_date->copy()->addYears(12);
            $daysToReform = (int) $now->diffInDays($reformDate, false);
            $ageYears = $vehicle->purchase_date->diffInYears($now);

            $totalMaintenance = 0.0;
            foreach ($vehicle->repairs as $repair) {
                $totalMaintenance += (float) ($repair->cost ?? 0);
                $totalMaintenance += $repair->repairParts->sum('total_price');
                $totalMaintenance += $repair->expenses->sum('amount');
            }

            if ($vehicle->shouldBeReformed()) {
                $status = 'reformed';
            } elseif ($vehicle->isNearReform()) {
                $status = 'near_reform';
            } else {
                $status = 'ok';
            }

            $venalValue = (float) ($vehicle->venal_value ?? $vehicle->computeVenalValue() ?? 0);
            $purchasePrice = (float) ($vehicle->purchase_price ?? 0);
            $costRatio = $purchasePrice > 0 ? round(($totalMaintenance / $purchasePrice) * 100, 1) : 0.0;
            $economicAlert = $venalValue > 0 && $totalMaintenance > $venalValue;

            $data[] = [
                'vehicle'           => $vehicle,
                'reform_date'       => $reformDate,
                'days_to_reform'    => $daysToReform,
                'age_years'         => $ageYears,
                'status'            => $status,
                'total_maintenance' => $totalMaintenance,
                'venal_value'       => $venalValue,
                'purchase_price'    => $purchasePrice,
                'cost_ratio'        => $costRatio,
                'economic_alert'    => $economicAlert,
            ];
        }

        return $data;
    }

    public function getAllVehiclesDataProperty(): array
    {
        return $this->computeAllVehiclesData();
    }

    public function getStatsProperty(): array
    {
        $all = $this->allVehiclesData;

        return [
            'total'            => count($all),
            'already_reformed' => count(array_filter($all, fn ($d) => $d['status'] === 'reformed')),
            'near_reform'      => count(array_filter($all, fn ($d) => $d['status'] === 'near_reform')),
            'ok'               => count(array_filter($all, fn ($d) => $d['status'] === 'ok')),
            'economic_alerts'  => count(array_filter($all, fn ($d) => $d['economic_alert'])),
        ];
    }

    public function getVehiclesDataProperty(): array
    {
        $data = $this->allVehiclesData;

        if ($this->filter !== 'all') {
            $data = array_values(array_filter($data, fn ($d) => $d['status'] === $this->filter));
        }

        usort($data, function ($a, $b) {
            return match ($this->sort_by) {
                'maintenance_cost' => $b['total_maintenance'] <=> $a['total_maintenance'],
                'cost_ratio'       => $b['cost_ratio'] <=> $a['cost_ratio'],
                default            => $a['days_to_reform'] <=> $b['days_to_reform'],
            };
        });

        return $data;
    }

    public function render(): View
    {
        return view('livewire.portal.reports.vehicle-reform', [
            'vehiclesData' => $this->vehiclesData,
            'stats'        => $this->stats,
        ]);
    }
}
