<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsedMaterial extends Model
{
    use Auditable;

    protected $fillable = [
        'repair_id', 'article_id', 'quantity', 'condition', 'disposition'
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function repair(): BelongsTo
    {
        return $this->belongsTo(Repair::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
