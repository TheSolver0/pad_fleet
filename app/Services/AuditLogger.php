<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    private const HIDDEN_KEYS = ['password', 'remember_token'];

    private static function filterSensitive(array $data): array
    {
        return array_diff_key($data, array_flip(self::HIDDEN_KEYS));
    }

    public static function log(
        string $action,
        ?string $description = null,
        ?Model $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): AuditLog {
        $request = Request::instance();
        $oldValues = $oldValues !== null ? self::filterSensitive($oldValues) : null;
        $newValues = $newValues !== null ? self::filterSensitive($newValues) : null;

        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $auditable?->getMorphClass(),
            'auditable_id' => $auditable?->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description,
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
        ]);
    }

    public static function logCreate(Model $model, ?string $description = null): AuditLog
    {
        return self::log(
            'created',
            $description ?? 'Création de ' . class_basename($model),
            $model,
            null,
            self::filterSensitive($model->getAttributes()),
        );
    }

    public static function logUpdate(Model $model, array $oldValues, array $newValues, ?string $description = null): AuditLog
    {
        return self::log(
            'updated',
            $description ?? 'Modification de ' . class_basename($model),
            $model,
            $oldValues,
            $newValues,
        );
    }

    public static function logDelete(Model $model, ?string $description = null): AuditLog
    {
        return self::log(
            'deleted',
            $description ?? 'Suppression de ' . class_basename($model),
            $model,
            self::filterSensitive($model->getAttributes()),
            null,
        );
    }

    public static function logLogin($user, ?string $matricule = null): AuditLog
    {
        return self::log('login', 'Connexion réussie', null, null, [
            'user_id' => $user->id,
            'matricule' => $user->matricule ?? $matricule,
        ]);
    }

    public static function logLogout($user): AuditLog
    {
        return self::log('logout', 'Déconnexion', null, null, [
            'user_id' => $user->id,
            'matricule' => $user->matricule ?? null,
        ]);
    }

    public static function logLoginFailed(?string $matricule, string $reason = 'Identifiants incorrects'): AuditLog
    {
        return self::log('login_failed', $reason, null, null, ['matricule' => $matricule]);
    }
}
