<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class DriverLeave extends Model
{
    use Auditable;

    const TYPE_CONGE_ANNUEL   = 'conge_annuel';
    const TYPE_MALADIE        = 'maladie';
    const TYPE_PERMISSION     = 'permission';
    const TYPE_MISSION        = 'mission_officielle';
    const TYPE_AUTRE          = 'autre';

    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'driver_id', 'type', 'start_date', 'end_date', 'nb_jours',
        'reason', 'status', 'approved_by', 'approved_at',
        'replacement_driver_id', 'notes', 'document_path',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'approved_at' => 'datetime',
    ];

    public static function typeOptions(): array
    {
        return [
            self::TYPE_CONGE_ANNUEL => 'Congé annuel',
            self::TYPE_MALADIE      => 'Maladie',
            self::TYPE_PERMISSION   => 'Permission',
            self::TYPE_MISSION      => 'Mission officielle',
            self::TYPE_AUTRE        => 'Autre',
        ];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function replacementDriver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'replacement_driver_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeOptions()[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING  => 'En attente',
            self::STATUS_APPROVED => 'Approuvé',
            self::STATUS_REJECTED => 'Refusé',
            default               => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING  => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            default               => 'secondary',
        };
    }

    /** Calcul automatique du nombre de jours ouvrés */
    public function computeNbJours(): void
    {
        if ($this->start_date && $this->end_date) {
            $this->nb_jours = max(1, (int) $this->start_date->diffInDays($this->end_date) + 1);
            $this->saveQuietly();
        }
    }

    /** Le congé est actif aujourd'hui */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_APPROVED
            && $this->start_date <= now()
            && $this->end_date >= now();
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
