<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverAssignment extends Model
{
    protected $fillable = [
        'driver_id', 'vehicle_id', 'mission_id', 'direction_id', 'person_id',
        'type', 'status',
        'started_at', 'ended_at', 'end_reason', 'notes',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at'   => 'date',
    ];

    const TYPE_VEHICLE    = 'vehicle';
    const TYPE_MISSION    = 'mission';
    const TYPE_DIRECTION  = 'direction';  // Mis à disposition d'une direction
    const TYPE_PERSON     = 'person';     // Mis à disposition d'un agent/directeur

    const STATUS_ACTIVE    = 'active';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_ENDED     = 'ended';

    public function driver(): BelongsTo    { return $this->belongsTo(Driver::class); }
    public function vehicle(): BelongsTo   { return $this->belongsTo(Vehicle::class); }
    public function mission(): BelongsTo   { return $this->belongsTo(Mission::class); }
    public function direction(): BelongsTo { return $this->belongsTo(Direction::class); }
    public function person(): BelongsTo    { return $this->belongsTo(Person::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updatedBy(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_VEHICLE   => 'Véhicule',
            self::TYPE_MISSION   => 'Mission',
            self::TYPE_DIRECTION => 'Direction',
            self::TYPE_PERSON    => 'Agent / Directeur',
            default              => $this->type,
        };
    }

    public function getAssignedToLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_VEHICLE   => $this->vehicle?->registration ?? '—',
            self::TYPE_DIRECTION => $this->direction?->name ?? '—',
            self::TYPE_PERSON    => $this->person?->full_name ?? '—',
            default              => '—',
        };
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE    => 'Actif',
            self::STATUS_SUSPENDED => 'Suspendu',
            self::STATUS_ENDED     => 'Terminé',
            default                => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE    => 'success',
            self::STATUS_SUSPENDED => 'warning',
            self::STATUS_ENDED     => 'secondary',
            default                => 'secondary',
        };
    }
}