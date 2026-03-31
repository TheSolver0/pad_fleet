<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderTask extends Model
{
    protected $fillable = [
        'work_order_id',
        'title',
        'estimated_minutes',
        'mechanic_id',
        'is_done',
    ];

    protected function casts(): array
    {
        return [
            'estimated_minutes' => 'integer',
            'is_done' => 'boolean',
        ];
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(Mechanic::class);
    }
}
