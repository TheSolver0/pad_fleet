<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleInspection extends Model
{
    protected $fillable = [
        'vehicle_id', 'inspected_at', 'expires_at',
        'result', 'control_center', 'cost',
        'certificate_number', 'notes', 'document_path',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'inspected_at' => 'date',
        'expires_at'   => 'date',
        'cost'         => 'decimal:2',
    ];

    const RESULT_ADMITTED  = 'admitted';
    const RESULT_ADJOURNED = 'adjourned';
    const RESULT_REFUSED   = 'refused';

    public function vehicle(): BelongsTo   { return $this->belongsTo(Vehicle::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updatedBy(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }

    public function resultLabel(): string
    {
        return match($this->result) {
            self::RESULT_ADMITTED  => 'Admis',
            self::RESULT_ADJOURNED => 'Ajourné',
            self::RESULT_REFUSED   => 'Refusé',
            default                => $this->result,
        };
    }

    public function resultColor(): string
    {
        return match($this->result) {
            self::RESULT_ADMITTED  => 'success',
            self::RESULT_ADJOURNED => 'warning',
            self::RESULT_REFUSED   => 'danger',
            default                => 'secondary',
        };
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
{
    return !$this->isExpired()
        && now()->diffInDays($this->expires_at) <= $days;
}
    public function statusLabel(): string
    {
        if ($this->isExpired())       return 'Expirée';
        if ($this->isExpiringSoon())  return 'Bientôt expirée';
        return 'Valide';
    }

    public function statusColor(): string
    {
        if ($this->isExpired())       return 'danger';
        if ($this->isExpiringSoon())  return 'warning';
        return 'success';
    }

    // Scopes utiles
    public function scopeExpired($q)      { return $q->where('expires_at', '<', now()); }
    public function scopeExpiringSoon($q, int $days = 30)
    {
        return $q->where('expires_at', '>=', now())
                 ->where('expires_at', '<=', now()->addDays($days));
    }
    public function scopePending($q)
    {
        return $q->whereIn('result', [self::RESULT_ADJOURNED, self::RESULT_REFUSED]);
    }
}