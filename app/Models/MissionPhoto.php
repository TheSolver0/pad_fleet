<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MissionPhoto extends Model
{
    protected $fillable = [
        'mission_id',
        'file_path',
        'original_name',
        'type',
        'caption',
        'sort_order',
        'taken_at'
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'taken_at' => 'date',
        ];
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    public static function storeUpload(Mission $mission, UploadedFile $file, string $type = 'before', ?string $caption = null, ?string $takenAt = null): self
    {
        $path = $file->store('missions/' . $mission->id . '/photos', 'public');
        $maxOrder = $mission->photos()->where('type', $type)->max('sort_order') ?? 0;

        return $mission->photos()->create([
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'type' => $type,
            'caption' => $caption,
            'sort_order' => $maxOrder + 1,
            'taken_at' => $takenAt ? \Carbon\Carbon::parse($takenAt) : null,
        ]);
    }

    public function delete(): ?bool
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            Storage::disk('public')->delete($this->file_path);
        }
        return parent::delete();
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'before' => 'Avant mission',
            'after' => 'Après mission',
            default => $this->type,
        };
    }
}