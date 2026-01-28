<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SinistrePhoto extends Model
{
    protected $table = 'sinistre_photos';

    protected $fillable = ['sinistre_id', 'file_path', 'original_name'];

    public function sinistre(): BelongsTo
    {
        return $this->belongsTo(Sinistre::class);
    }

    public static function storeUpload(Sinistre $sinistre, UploadedFile $file): self
    {
        $path = $file->store('sinistres/' . $sinistre->id, 'public');
        return $sinistre->photos()->create([
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function delete(): ?bool
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            Storage::disk('public')->delete($this->file_path);
        }
        return parent::delete();
    }
}
