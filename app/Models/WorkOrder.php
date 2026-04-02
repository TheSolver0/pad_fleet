<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasMaintenanceEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    use Auditable;
    use HasMaintenanceEnums;

    public const STATUS_PENDING = 'pending';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_VALIDATED = 'validated';

    protected $fillable = [
        'vehicle_id', 'diagnostic_id', 'mechanic_id', 'reference', 'transfer_reference', 'transfer_date', 'work_date',
        'start_time', 'end_time', 'work_description', 'parts_used', 'parts_removed',
        'equipment_used', 'tools_used', 'technical_notes', 'problems_found',
        'solutions_applied', 'quality_control', 'final_checks', 'labor_cost',
        'parts_cost', 'total_cost', 'status', 'completion_percent', 'stock_applied_at', 'completion_notes',
        'mechanic_signature', 'supervisor_signature', 'client_signature', 'validation_date',
        // Véhicule
        'mileage',

        // État systèmes
        'system_engine',
        'system_suspension',
        'system_electrical',
        'system_body',
        'system_ac',

        // Classification de l'incident
        'failure_cause',
        'failure_cause_comment',
        'failure_type',
        'maintenance_type',
        'operation_type',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'transfer_date' => 'date',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'labor_cost' => 'decimal:2',
            'parts_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'completion_percent' => 'integer',
            'stock_applied_at' => 'datetime',
            'validation_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function diagnostic(): BelongsTo
    {
        return $this->belongsTo(Diagnostic::class);
    }

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(Mechanic::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(WorkOrderTask::class);
    }

    public function parts(): HasMany
    {
        return $this->hasMany(WorkOrderPart::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(WorkOrderPhoto::class)->orderBy('sort_order');
    }

    public function getPartsTotalCostAttribute(): float
    {
        return (float) $this->parts()->sum('total_cost');
    }

    /**
     * Générer une référence unique
     */
    public static function generateReference(): string
    {
        $prefix = 'BT-'.date('Y');
        $lastNumber = self::where('reference', 'like', $prefix.'%')
            ->orderByRaw('CAST(SUBSTRING(reference, '.(strlen($prefix) + 1).') AS UNSIGNED)')
            ->value('reference');

        if ($lastNumber) {
            $number = intval(substr($lastNumber, strlen($prefix))) + 1;
        } else {
            $number = 1;
        }

        return $prefix.str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Obtenir le libellé du statut
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'En attente',
            self::STATUS_IN_PROGRESS => 'En cours',
            self::STATUS_COMPLETED => 'Terminé',
            self::STATUS_VALIDATED => 'Validé',
            default => $this->status,
        };
    }

    /**
     * Obtenir la couleur du statut
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'info',
            self::STATUS_IN_PROGRESS => 'warning',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_VALIDATED => 'primary',
            default => 'secondary',
        };
    }

    /**
     * Calculer la durée de travail
     */
    public function getWorkDurationAttribute(): ?string
    {
        if (! $this->start_time || ! $this->end_time) {
            return null;
        }

        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        $duration = $start->diff($end);

        if ($duration->h > 0) {
            return $duration->h.'h '.$duration->i.'min';
        } else {
            return $duration->i.' min';
        }
    }

    /**
     * Vérifier si le bon de travail est signé
     */
    public function isSigned(): bool
    {
        return ! empty($this->mechanic_signature) &&
               ! empty($this->supervisor_signature);
    }

    /**
     * Vérifier si le bon de travail est validé
     */
    public function isValidated(): bool
    {
        return $this->status === self::STATUS_VALIDATED &&
               ! empty($this->client_signature) &&
               ! empty($this->validation_date);
    }
}
