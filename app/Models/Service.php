<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use Auditable;

    protected $fillable = ['name', 'code'];

    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }

    public function demandeurs(): HasMany
    {
        return $this->hasMany(Demandeur::class);
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
