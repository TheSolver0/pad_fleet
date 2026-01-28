<?php

namespace App\Models\Concerns;

use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            AuditLogger::logCreate($model);
        });

        static::updated(function (Model $model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);
            if (! empty($changes)) {
                $original = array_intersect_key($model->getOriginal(), $changes);
                AuditLogger::logUpdate($model, $original, $changes);
            }
        });

        static::deleted(function (Model $model) {
            AuditLogger::logDelete($model);
        });
    }
}
