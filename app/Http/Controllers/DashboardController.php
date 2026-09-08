<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Mission;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function tripStats(Request $request): JsonResponse
    {
        $period = $request->query('period', 'month');

        $query = Mission::query()
            ->whereNotIn('status', [Mission::STATUS_REJECTED, Mission::STATUS_CANCELLED]);

        $query = match($period) {
            'month'   => (clone $query)->whereMonth('date_start', now()->month)->whereYear('date_start', now()->year),
            'quarter' => (clone $query)->whereBetween('date_start', [now()->firstOfQuarter(), now()->lastOfQuarter()]),
            'year'    => (clone $query)->whereYear('date_start', now()->year),
            default   => $query,
        };

        $drivers = (clone $query)
            ->whereNotNull('driver_id')
            ->selectRaw('driver_id, count(*) as missions_count, coalesce(sum(distance_km),0) as total_km, coalesce(avg(distance_km),0) as avg_km, avg(TIMESTAMPDIFF(MINUTE, date_start, date_end)) as avg_duration_minutes, max(date_start) as last_mission')
            ->groupBy('driver_id')
            ->orderByDesc('missions_count')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $driver = Driver::find($row->driver_id);
                $dur = $row->avg_duration_minutes ? (int) $row->avg_duration_minutes : null;
                return [
                    'name'             => $driver ? $driver->full_name : '—',
                    'missions_count'   => (int) $row->missions_count,
                    'total_km'         => (int) $row->total_km,
                    'avg_km'           => round((float) $row->avg_km, 1),
                    'avg_duration_min' => $dur,
                    'dur_label'        => $dur ? floor($dur/60).'h '.($dur%60).'min' : '—',
                    'last_mission'     => $row->last_mission
                        ? \Carbon\Carbon::parse($row->last_mission)->translatedFormat('d M Y')
                        : '—',
                ];
            });

        $vehicles = (clone $query)
            ->whereNotNull('vehicle_id')
            ->selectRaw('vehicle_id, count(*) as missions_count, coalesce(sum(distance_km),0) as total_km, coalesce(avg(distance_km),0) as avg_km, avg(TIMESTAMPDIFF(MINUTE, date_start, date_end)) as avg_duration_minutes, max(date_start) as last_mission')
            ->groupBy('vehicle_id')
            ->orderByDesc('missions_count')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $vehicle = Vehicle::find($row->vehicle_id);
                $dur = $row->avg_duration_minutes ? (int) $row->avg_duration_minutes : null;
                return [
                    'name'             => $vehicle ? $vehicle->registration : '—',
                    'missions_count'   => (int) $row->missions_count,
                    'total_km'         => (int) $row->total_km,
                    'avg_km'           => round((float) $row->avg_km, 1),
                    'avg_duration_min' => $dur,
                    'dur_label'        => $dur ? floor($dur/60).'h '.($dur%60).'min' : '—',
                    'last_mission'     => $row->last_mission
                        ? \Carbon\Carbon::parse($row->last_mission)->translatedFormat('d M Y')
                        : '—',
                ];
            });

        return response()->json([
            'drivers'  => $drivers,
            'vehicles' => $vehicles,
        ]);
    }
}