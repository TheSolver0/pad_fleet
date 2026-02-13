<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use Auditable;

    protected $guarded = [
    ];

    protected function casts(): array
    {
        return [
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

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function resourcePerson(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'resource_person_id');
    }

    public function missions(): HasMany
    {
        return $this->hasMany(Mission::class);
    }

    public function drivingLicenses(): HasMany
    {
        return $this->hasMany(DrivingLicense::class);
    }

    public function activeDrivingLicense(): HasMany
    {
        return $this->drivingLicenses()->where('is_active', true);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getMissionsCountAttribute(): int
    {
        return $this->missions()->count();
    }

    public function getActiveLicensesCountAttribute(): int
    {
        return $this->activeDrivingLicense()->count();
    }

    public function getExpiredLicensesCountAttribute(): int
    {
        return $this->drivingLicenses()->where('expiry_date', '<', now())->count();
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
