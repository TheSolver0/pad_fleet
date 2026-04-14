<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mission extends Model
{
    use Auditable;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'vehicle_id', 'driver_id', 'demandeur_id', 'city_id', 'date_start', 'date_end',
        'km_departure', 'km_return', 'distance_km', 'destination', 'raison', 'status',
        'approved_by', 'approved_at', 'notes',
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

    public function sinistre(): HasOne
    {
        return $this->hasOne(Sinistre::class);
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
        return match ($this->status) {
            self::STATUS_PENDING => 'En attente',
            self::STATUS_APPROVED => 'Approuvée',
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
