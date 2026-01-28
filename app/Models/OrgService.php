<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrgService extends Model
{
    protected $table = 'org_services';

    protected $fillable = ['department_id', 'name', 'code', 'notes'];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function persons(): HasMany
    {
        return $this->hasMany(Person::class, 'org_service_id');
    }
}
