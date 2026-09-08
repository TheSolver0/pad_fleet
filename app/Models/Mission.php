<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mission extends Model
{
    use Auditable;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved'; // legacy
    public const STATUS_PROGRAMMED = 'programmed';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_POSTPONED = 'postponed';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'En attente',
            self::STATUS_PROGRAMMED => 'Programmée',
            self::STATUS_IN_PROGRESS => 'En cours',
            self::STATUS_POSTPONED => 'Reportée',
            self::STATUS_COMPLETED => 'Terminée',
            self::STATUS_REJECTED => 'Refusée',
            self::STATUS_CANCELLED => 'Annulée',
        ];
    }

    protected $fillable = [
        'vehicle_id', 'driver_id', 'demandeur_id', 'city_id', 'date_start', 'date_end',
        'km_departure', 'km_return', 'distance_km', 'estimated_distance_km', 'destination', 'raison', 'status',
        'approved_by', 'approved_at', 'notes',
        'compte_rendu', 'compte_rendu_by', 'compte_rendu_at',
    ];

    protected function casts(): array
    {
        return [
            'date_start' => 'datetime',
            'date_end' => 'datetime',
            'approved_at' => 'datetime',
            'km_departure' => 'integer',
            'km_return' => 'integer',
            'distance_km' => 'integer',
            'estimated_distance_km' => 'integer',
            'compte_rendu_at' => 'datetime',
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

    public function demandeur(): BelongsTo
    {
        return $this->belongsTo(Demandeur::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function compteRenduByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'compte_rendu_by');
    }

    public function sinistre(): HasOne
    {
        return $this->hasOne(Sinistre::class);
    }

    public function technicians(): BelongsToMany
    {
        return $this->belongsToMany(Mechanic::class, 'mission_mechanic')->withTimestamps();
    }

    public function photos()
    {
        return $this->hasMany(MissionPhoto::class);
    }

    public function beforePhotos()
    {
        return $this->photos()->where('type', 'before');
    }

    public function afterPhotos()
    {
        return $this->photos()->where('type', 'after');
    }

    public function documents()
    {
        return $this->hasMany(MissionDocument::class);
    }

    public function controlSheets()
    {
        return $this->hasMany(VehicleControlSheet::class);
    }

    /** Calcule distance_km à partir de km_departure et km_return. */
    public function computeDistance(): void
    {
        if ($this->km_departure !== null && $this->km_return !== null && $this->km_return >= $this->km_departure) {
            $this->distance_km = $this->km_return - $this->km_departure;
            $this->saveQuietly();
        }
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->status === self::STATUS_APPROVED) {
            return self::statusOptions()[self::STATUS_PROGRAMMED];
        }

        return match ($this->status) {
            self::STATUS_PENDING => 'En attente',
            self::STATUS_PROGRAMMED => 'Programmée',
            self::STATUS_IN_PROGRESS => 'En cours',
            self::STATUS_POSTPONED => 'Reportée',
            self::STATUS_REJECTED => 'Refusée',
            self::STATUS_COMPLETED => 'Terminée',
            self::STATUS_CANCELLED => 'Annulée',
            default => $this->status,
        };
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
