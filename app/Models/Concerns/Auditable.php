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

trait HasMaintenanceEnums
{
    // ── Cause de défaillance ─────────────────────────────────────────
    public static function failureCauses(): array
    {
        return [
            'usure_normale'      => 'Usure normale',
            'defaut_utilisateur' => 'Défaut utilisateur',
            'defaut_piece'       => 'Défaut pièce',
            'defaut_maintenance' => 'Défaut maintenance',
            'defaut_conception'  => 'Défaut conception',
            'autre'              => 'Autre',
        ];
    }
 
    // ── Type de défaillance ──────────────────────────────────────────
    public static function failureTypes(): array
    {
        return [
            'electrique'   => 'Électrique',
            'hydraulique'  => 'Hydraulique',
            'mecanique'    => 'Mécanique',
            'pneumatique'  => 'Pneumatique',
            'structurelle' => 'Structurelle',
            'autre'        => 'Autre',
        ];
    }
 
    // ── Type de maintenance ──────────────────────────────────────────
    public static function maintenanceTypes(): array
    {
        return [
            'corrective'    => 'Corrective',
            'systematique'  => 'Systématique',
            'conditionnelle'=> 'Conditionnelle',
            'ameliorative'  => 'Améliorative',
            'predictive'    => 'Prédictive',
            'autre'         => 'Autre',
        ];
    }
 
    // ── Opération ────────────────────────────────────────────────────
    public static function operationTypes(): array
    {
        return [
            'amelioration' => 'Amélioration',
            'controle'     => 'Contrôle',
            'diagnostic'   => 'Diagnostic',
            'nettoyage'    => 'Nettoyage',
            'reglage'      => 'Réglage',
            'remplacement' => 'Remplacement',
        ];
    }
 
    // ── État des systèmes ────────────────────────────────────────────
    public static function systemStates(): array
    {
        return [
            ''             => '—',
            'ok'           => 'OK',
            'defaillant'   => 'Défaillant',
            'a_surveiller' => 'À surveiller',
        ];
    }
 
    public static function vehicleSystems(): array
    {
        return [
            'system_engine'     => 'Moteur',
            'system_suspension' => 'Suspension / Transmission / Freinage',
            'system_electrical' => 'Électricité / Électronique',
            'system_body'       => 'Carrosserie et peinture',
            'system_ac'         => 'Climatisation',
        ];
    }
 
    // ── Helpers d'affichage ──────────────────────────────────────────
    public function getFailureCauseLabelAttribute(): string
    {
        return static::failureCauses()[$this->failure_cause] ?? '—';
    }
 
    public function getFailureTypeLabelAttribute(): string
    {
        return static::failureTypes()[$this->failure_type] ?? '—';
    }
 
    public function getMaintenanceTypeLabelAttribute(): string
    {
        return static::maintenanceTypes()[$this->maintenance_type] ?? '—';
    }
 
    public function getOperationTypeLabelAttribute(): string
    {
        return static::operationTypes()[$this->operation_type] ?? '—';
    }
}
