<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleControlSheet extends Model
{
    use Auditable;

    protected $fillable = [
        'vehicle_id', 'mission_id', 'vehicle_schedule_id', 'driver_id', 'created_by',
        'ordre_mission', 'lieu', 'date_depart', 'date_retour',
        'km_depart', 'km_retour',
        'docs_administratifs', 'controle_exterieur',
        'compartiment_moteur', 'controle_fonctionnalites', 'outillages',
        'observations_depart', 'observations_retour',
    ];

    protected $casts = [
        'date_depart'            => 'date',
        'date_retour'            => 'date',
        'docs_administratifs'    => 'array',
        'controle_exterieur'     => 'array',
        'compartiment_moteur'    => 'array',
        'controle_fonctionnalites' => 'array',
        'outillages'             => 'array',
    ];

    // ── Structure par défaut de la fiche ─────────────────────────

    public static function defaultStructure(): array
    {
        return [
            'docs_administratifs' => [
                'carte_grise'      => ['depart' => null, 'retour' => null],
                'assurance'        => ['depart' => null, 'retour' => null],
                'visite_technique' => ['depart' => null, 'retour' => null],
                'stationnement'    => ['depart' => null, 'retour' => null],
            ],
            'controle_exterieur' => [
                'feux_avant'          => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'feux_arriere'        => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'clignotants'         => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'feux_stop'           => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'feux_recul'          => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'feux_detresse'       => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'pare_brise'          => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'pneus_gonflage'      => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'ecrous_roues'        => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'roue_secours'        => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'fuites_dessous'      => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'treuil'              => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'levier_4x4'          => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
            ],
            'compartiment_moteur' => [
                'huile_moteur'        => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'liquide_refroid'     => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'liquide_frein'       => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'liquide_embrayage'   => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'liquide_lave_glace'  => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'huile_direction'     => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'tension_courroies'   => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'cosses_batteries'    => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
            ],
            'controle_fonctionnalites' => [
                'freinage'            => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'ceinture_securite'   => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'essuie_glace'        => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'avertisseur_sonore'  => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'alarme_marche_arriere' => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'retroviseurs'        => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'radio'               => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
                'climatisation'       => ['correct_d' => null, 'defaut_d' => null, 'correct_r' => null, 'defaut_r' => null],
            ],
            'outillages' => [
                'kit_premier_secours'  => ['present_d' => null, 'absent_d' => null, 'present_r' => null, 'absent_r' => null],
                'triangles_signalisation' => ['present_d' => null, 'absent_d' => null, 'present_r' => null, 'absent_r' => null],
                'niveau_carburant'     => ['present_d' => null, 'absent_d' => null, 'present_r' => null, 'absent_r' => null],
                'cric_barre_rallonge'  => ['present_d' => null, 'absent_d' => null, 'present_r' => null, 'absent_r' => null],
                'cle_demonte_pneus'    => ['present_d' => null, 'absent_d' => null, 'present_r' => null, 'absent_r' => null],
                'extincteur'           => ['present_d' => null, 'absent_d' => null, 'present_r' => null, 'absent_r' => null],
                'sangle_remorquage'    => ['present_d' => null, 'absent_d' => null, 'present_r' => null, 'absent_r' => null],
                'cales'                => ['present_d' => null, 'absent_d' => null, 'present_r' => null, 'absent_r' => null],
                'manivelles_cric'      => ['present_d' => null, 'absent_d' => null, 'present_r' => null, 'absent_r' => null],
            ],
        ];
    }

    // Labels lisibles pour chaque item
    public static function labels(): array
    {
        return [
            // docs administratifs
            'carte_grise'      => 'Carte grise',
            'assurance'        => 'Assurance',
            'visite_technique' => 'Visite Technique',
            'stationnement'    => 'Stationnement',
            // extérieur
            'feux_avant'       => 'Feux Avant',
            'feux_arriere'     => 'Feux arrière',
            'clignotants'      => 'Clignotants',
            'feux_stop'        => 'Feux Stop',
            'feux_recul'       => 'Feux de recul',
            'feux_detresse'    => 'Feux de détresse',
            'pare_brise'       => 'Pare-brise',
            'pneus_gonflage'   => 'État des pneus et gonflage',
            'ecrous_roues'     => 'Écrous de roues serré',
            'roue_secours'     => 'Roues de secours gonflé',
            'fuites_dessous'   => 'Fuites sous le véhicule',
            'treuil'           => 'Treuil (si véhicule équilibré)',
            'levier_4x4'       => 'Levier 4x4 fonctionne',
            // moteur
            'huile_moteur'     => 'Huile moteur',
            'liquide_refroid'  => 'Liquide de refroidissement',
            'liquide_frein'    => 'Liquide de frein',
            'liquide_embrayage'=> 'Liquide d\'embrayage',
            'liquide_lave_glace'=> 'Liquide Lave-glace',
            'huile_direction'  => 'Huile de direction',
            'tension_courroies'=> 'Tension des courroies',
            'cosses_batteries' => 'Cosses de batteries',
            // fonctionnalités
            'freinage'         => 'Freinage',
            'ceinture_securite'=> 'Ceinture de sécurité',
            'essuie_glace'     => 'Essuie-glace',
            'avertisseur_sonore'=> 'Avertisseur sonore (Klaxon)',
            'alarme_marche_arriere' => 'Alarme de marche arrière',
            'retroviseurs'     => 'Rétroviseurs',
            'radio'            => 'Radio',
            'climatisation'    => 'Climatisation',
            // outillages
            'kit_premier_secours'  => 'Kit de premier secours',
            'triangles_signalisation' => '2X Triangles de signalisation',
            'niveau_carburant'     => 'Niveau carburant',
            'cric_barre_rallonge'  => 'Cric avec sa barre-rallonge',
            'cle_demonte_pneus'    => 'Clé démonte-pneus',
            'extincteur'           => 'Extincteur',
            'sangle_remorquage'    => 'Sangle de remorquage',
            'cales'                => '2X Cales',
            'manivelles_cric'      => 'Manivelles de cric',
        ];
    }

    public function vehicle(): BelongsTo         { return $this->belongsTo(Vehicle::class); }
    public function mission(): BelongsTo         { return $this->belongsTo(Mission::class); }
    public function vehicleSchedule(): BelongsTo { return $this->belongsTo(VehicleSchedule::class); }
    public function driver(): BelongsTo          { return $this->belongsTo(Driver::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function photos(): HasMany
    {
        return $this->hasMany(VehicleControlSheetPhoto::class, 'control_sheet_id');
    }

    public function beforePhotos(): HasMany
    {
        return $this->photos()->where('type', 'before');
    }

    public function afterPhotos(): HasMany
    {
        return $this->photos()->where('type', 'after');
    }

    protected static function booted(): void
    {
        static::bootAuditable();
    }
}
