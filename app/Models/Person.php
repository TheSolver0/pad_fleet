<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    protected $table = 'persons';

    protected $fillable = ['name', 'email', 'phone', 'direction_id', 'department_id', 'org_service_id', 'notes'];

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

    public function assignedVehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'assigned_person_id');
    }

    /** Libellé Direction / Département / Service pour affichage */
    public function getOrganisationLabelAttribute(): string
    {
        $parts = array_filter([
            $this->direction?->name,
            $this->department?->name,
            $this->orgService?->name,
        ]);
        return implode(' — ', $parts) ?: '—';
    }
}
