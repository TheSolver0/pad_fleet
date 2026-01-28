<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Direction extends Model
{
    protected $fillable = ['name', 'code', 'notes'];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function persons(): HasMany
    {
        return $this->hasMany(Person::class, 'direction_id');
    }
}
