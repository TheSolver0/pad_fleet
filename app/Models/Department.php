<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = ['direction_id', 'name', 'code', 'notes'];

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function orgServices(): HasMany
    {
        return $this->hasMany(OrgService::class, 'department_id');
    }

    public function persons(): HasMany
    {
        return $this->hasMany(Person::class, 'department_id');
    }
}
