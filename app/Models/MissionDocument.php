<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MissionDocument extends Model
{
    protected $fillable = [
        'mission_id',
        'file_path',
        'original_name',
        'file_type',
        'mime_type',
        'document_type',
        'caption',
        'file_size',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    public static function storeUpload(Mission $mission, UploadedFile $file, ?string $documentType = null, ?string $caption = null): self
    {
        $mimeType = $file->getMimeType();
        $fileType = str_starts_with($mimeType, 'image/') ? 'image' : 'pdf';

        $path = $file->store('missions/' . $mission->id . '/documents', 'public');
        $maxOrder = $mission->documents()->max('sort_order') ?? 0;

        return $mission->documents()->create([
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'file_type' => $fileType,
            'mime_type' => $mimeType,
            'document_type' => $documentType,
            'caption' => $caption,
            'file_size' => $file->getSize(),
            'sort_order' => $maxOrder + 1,
        ]);
    }

    public function delete(): ?bool
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            Storage::disk('public')->delete($this->file_path);
        }
        return parent::delete();
    }

    public function getFileTypeLabelAttribute(): string
    {
        return match ($this->file_type) {
            'image' => 'Image',
            'pdf' => 'PDF',
            default => $this->file_type,
        };
    }

    public function getDocumentTypeLabelAttribute(): string
    {
        return match ($this->document_type) {
            'ordre_mission' => 'Ordre de mission',
            'rapport' => 'Rapport de mission',
            'facture' => 'Facture',
            'recu' => 'Reçu',
            'autre' => 'Autre document',
            default => $this->document_type ?? 'Document',
        };
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}