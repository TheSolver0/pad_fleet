<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Diagnostic extends Model
{
    use Auditable;

    protected $fillable = [
        'vehicle_id', 'garage_id', 'mechanic_id', 'reference', 'diagnostic_date', 'user_name', 'user_role',
        'requester_type', 'requester_id',
        'km_arrival', 'has_admin_file', 'has_jack', 'has_wheel_key', 'has_spare_wheel', 'has_first_aid',
        'observations', 'engine_issues', 'suspension_transmission',
        'braking_system', 'electronics_electricity', 'bodywork_paint', 'air_conditioning',
        'other_issues', 'internal_works', 'external_works', 'conclusion',
        'mechanic_signature', 'maintenance_manager_signature', 'vehicle_manager_signature',
    ];

    protected function casts(): array
    {
        return [
            'diagnostic_date' => 'date',
            'km_arrival' => 'integer',
            'has_admin_file' => 'boolean',
            'has_jack' => 'boolean',
            'has_wheel_key' => 'boolean',
            'has_spare_wheel' => 'boolean',
            'has_first_aid' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function garage(): BelongsTo
    {
        return $this->belongsTo(Garage::class);
    }

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(Mechanic::class);
    }

    public function requester(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'requester_type', 'requester_id');
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    /**
     * Générer une référence unique
     */
    public static function generateReference(): string
    {
        $prefix = 'DIAG-' . date('Y');
        $lastNumber = self::where('reference', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(reference, ' . (strlen($prefix) + 1) . ') AS UNSIGNED)')
            ->value('reference');
        
        if ($lastNumber) {
            $number = intval(substr($lastNumber, strlen($prefix))) + 1;
        } else {
            $number = 1;
        }
        
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Obtenir le statut du diagnostic
     */
    public function getStatusAttribute(): string
    {
        if ($this->workOrders()->where('status', 'completed')->exists()) {
            return 'Traité';
        } elseif ($this->workOrders()->where('status', 'in_progress')->exists()) {
            return 'En cours';
        } else {
            return 'En attente';
        }
    }

    /**
     * Obtenir la couleur du statut
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'Traité' => 'success',
            'En cours' => 'warning',
            'En attente' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Vérifier si des travaux externes sont recommandés
     */
    public function hasExternalWorks(): bool
    {
        return !empty($this->external_works);
    }

    /**
     * Obtenir le nombre total de problèmes identifiés
     */
    public function getTotalIssuesCount(): int
    {
        $count = 0;
        $issues = [
            'engine_issues', 'suspension_transmission', 'braking_system',
            'electronics_electricity', 'bodywork_paint', 'air_conditioning', 'other_issues'
        ];
        
        foreach ($issues as $field) {
            if (!empty($this->$field)) {
                $count++;
            }
        }
        
        return $count;
    }
}
