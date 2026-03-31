<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;

class WorkOrderPhoto extends Model
{
    protected $fillable = [
        'work_order_id',
        'phase',
        'file_path',
        'original_name',
        'caption',
        'taken_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'taken_at' => 'date',
            'sort_order' => 'integer',
        ];
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public static function storeUpload(WorkOrder $workOrder, UploadedFile $file, string $phase, ?string $caption = null, ?string $takenAt = null): self
    {
        $path = $file->store('work-orders/' . $workOrder->id . '/photos', 'public');
        $maxOrder = (int) $workOrder->photos()->max('sort_order');

        return $workOrder->photos()->create([
            'phase' => $phase,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'caption' => $caption ?: null,
            'taken_at' => $takenAt ? \Carbon\Carbon::parse($takenAt) : null,
            'sort_order' => $maxOrder + 1,
        ]);
    }
}
