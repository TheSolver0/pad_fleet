<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sinistre extends Model
{
    use Auditable;

    public const STATUS_DECLARED = 'declared';
    public const STATUS_IN_REPAIR = 'in_repair';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'vehicle_id', 'mission_id', 'declared_at', 'description', 'location',
        'estimated_cost', 'responsibility', 'garage_id', 'assureur_id', 'status', 'notes', 'police_report_path','driver_id',
    ];

    protected function casts(): array
    {
        return [
            'declared_at' => 'datetime',
            'estimated_cost' => 'decimal:2',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    public function garage(): BelongsTo
    {
        return $this->belongsTo(Garage::class);
    }

    public function assureur(): BelongsTo
    {
        return $this->belongsTo(Assureur::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(SinistrePhoto::class, 'sinistre_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DECLARED => 'Déclaré',
            self::STATUS_IN_REPAIR => 'En réparation',
            self::STATUS_CLOSED => 'Clôturé',
            default => $this->status,
        };
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
    public function driver(): BelongsTo
{
    return $this->belongsTo(Driver::class);
}
}
