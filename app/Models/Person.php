<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    protected $table = 'persons';

    protected $guarded = []; 
    
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
        return $this->hasMany(Vehicle::class);
    }

    public function getFullNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return trim($this->first_name . ' ' . $this->last_name);
        }
        return $this->name ?? '';
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
