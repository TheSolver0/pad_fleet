<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use Auditable;

    protected $fillable = [
        'user_id', 'matricule', 'first_name', 'last_name', 'phone', 'email',
        'license_number', 'license_category', 'license_expiry', 'service_id',
        'is_available', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'license_expiry' => 'date',
            'is_available' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function missions(): HasMany
    {
        return $this->hasMany(Mission::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getMissionsCountAttribute(): int
    {
        return $this->missions()->count();
    }

    public function isLicenseExpired(): bool
    {
        return $this->license_expiry && $this->license_expiry->isPast();
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
