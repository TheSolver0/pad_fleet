<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleMileageLog extends Model
{
    public const SOURCE_MISSION = 'mission';
    public const SOURCE_CONTROL_SHEET = 'control_sheet';
    public const SOURCE_REPAIR = 'repair';
    public const SOURCE_MANUAL = 'manual';

    protected $fillable = [
        'vehicle_id', 'mileage', 'source', 'source_id', 'recorded_at', 'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'mileage' => 'integer',
            'recorded_at' => 'datetime',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function recordedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            self::SOURCE_MISSION => 'Mission',
            self::SOURCE_CONTROL_SHEET => 'Fiche de contrôle',
            self::SOURCE_REPAIR => 'Réparation',
            self::SOURCE_MANUAL => 'Saisie manuelle',
            default => $this->source,
        };
    }
}
