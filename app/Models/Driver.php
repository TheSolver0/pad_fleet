<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\DriverAssignment;


class Driver extends Model
{
    use Auditable;
    

    protected $guarded = [
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
            'is_garage_driver' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function orgService(): BelongsTo
    {
        return $this->belongsTo(OrgService::class, 'org_service_id');
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

    public function getIdDocumentRectoUrlAttribute(): ?string
    {
        return $this->id_document_recto_path ? asset('storage/' . $this->id_document_recto_path) : null;
    }

    public function getIdDocumentVersoUrlAttribute(): ?string
    {
        return $this->id_document_verso_path ? asset('storage/' . $this->id_document_verso_path) : null;
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }

public function assignments(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(DriverAssignment::class)->orderByDesc('started_at');
}

public function activeAssignment(): \Illuminate\Database\Eloquent\Relations\HasOne
{
    return $this->hasOne(DriverAssignment::class)
        ->where('status', DriverAssignment::STATUS_ACTIVE)
        ->where('type', DriverAssignment::TYPE_VEHICLE)
        ->latest('started_at');
}

public function activeDispatch(): \Illuminate\Database\Eloquent\Relations\HasOne
{
    return $this->hasOne(DriverAssignment::class)
        ->where('status', DriverAssignment::STATUS_ACTIVE)
        ->whereIn('type', [DriverAssignment::TYPE_DIRECTION, DriverAssignment::TYPE_PERSON])
        ->latest('started_at');
}

public function leaves(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(DriverLeave::class)->orderByDesc('start_date');
}

public function activeLeave(): \Illuminate\Database\Eloquent\Relations\HasOne
{
    return $this->hasOne(DriverLeave::class)
        ->where('status', DriverLeave::STATUS_APPROVED)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now());
}

public function getIsOnLeaveAttribute(): bool
{
    return $this->activeLeave()->exists();
}

/** Le chauffeur est-il rattaché à la Direction des Affaires Générales (DAG) ? */
public function isAtDag(): bool
{
    return $this->direction?->code === 'DAG';
}

/** Libellé rapide de l'affectation organisationnelle / garage du chauffeur. */
public function getAssignmentLabelAttribute(): string
{
    if ($this->is_garage_driver) {
        return 'Garage';
    }
    $parts = array_filter([
        $this->direction?->name,
        $this->department?->name,
        $this->orgService?->name,
    ]);

    return $parts ? implode(' / ', $parts) : 'Non affecté';
}
}
