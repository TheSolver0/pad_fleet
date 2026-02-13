<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleSchedule extends Model
{
    use Auditable;

    public const STATUS_PLANNED = 'planned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'vehicle_id', 'driver_id', 'title', 'description', 'destination',
        'departure_location', 'start_datetime', 'end_datetime', 'estimated_distance',
        'purpose', 'status', 'mileage_start', 'mileage_end', 'fuel_consumed',
        'notes', 'user_id'
    ];

    protected function casts(): array
    {
        return [
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
            'estimated_distance' => 'decimal:2',
            'mileage_start' => 'integer',
            'mileage_end' => 'integer',
            'fuel_consumed' => 'decimal:2',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PLANNED => 'Planifié',
            self::STATUS_IN_PROGRESS => 'En cours',
            self::STATUS_COMPLETED => 'Terminé',
            self::STATUS_CANCELLED => 'Annulé',
            default => $this->status,
        };
    }

    public function getActualDistanceAttribute(): ?float
    {
        if ($this->mileage_start && $this->mileage_end) {
            return $this->mileage_end - $this->mileage_start;
        }
        return null;
    }

    public function getAverageConsumptionAttribute(): ?float
    {
        $distance = $this->actual_distance;
        $fuel = $this->fuel_consumed;
        
        if ($distance && $fuel && $distance > 0) {
            return ($fuel / $distance) * 100; // litres/100km
        }
        
        return null;
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
