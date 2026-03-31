<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairExpense extends Model
{
    protected $fillable = [
        'repair_id',
        'label',
        'amount',
        'attachment_path',
        'attachment_name',
        'notes',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function repair(): BelongsTo
    {
        return $this->belongsTo(Repair::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
