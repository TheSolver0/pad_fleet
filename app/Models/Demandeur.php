<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Demandeur extends Model
{
    use Auditable;

    protected $fillable = [
        'matricule', 'name', 'service_id', 'contact_phone', 'contact_email', 'notes',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function missions(): HasMany
    {
        return $this->hasMany(Mission::class, 'demandeur_id');
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
