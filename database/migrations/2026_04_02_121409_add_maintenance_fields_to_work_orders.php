<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {

            // ── Véhicule ──────────────────────────────────────────────
            $table->unsignedInteger('mileage')->nullable()->after('vehicle_id')
                ->comment('Kilométrage au moment de l\'intervention');

            // ── État des systèmes ─────────────────────────────────────
            // Valeurs possibles par système : null | 'ok' | 'defaillant' | 'a_surveiller'
            $table->string('system_engine')->nullable()->after('mileage')
                ->comment('État moteur');
            $table->string('system_suspension')->nullable()
                ->comment('État suspension/transmission/freinage');
            $table->string('system_electrical')->nullable()
                ->comment('État électricité/électronique');
            $table->string('system_body')->nullable()
                ->comment('État carrosserie et peinture');
            $table->string('system_ac')->nullable()
                ->comment('État climatisation');

            // ── Cause de défaillance (une seule case cochée) ──────────
            // Enum: usure_normale | defaut_utilisateur | defaut_piece |
            //       defaut_maintenance | defaut_conception | autre
            $table->string('failure_cause')->nullable()->after('system_ac');
            $table->text('failure_cause_comment')->nullable()
                ->comment('Commentaire libre sur la cause de défaillance');

            // ── Type de défaillance (une seule case cochée) ───────────
            // Enum: electrique | hydraulique | mecanique | pneumatique |
            //       structurelle | autre
            $table->string('failure_type')->nullable()->after('failure_cause_comment');

            // ── Type de maintenance (une seule case cochée) ───────────
            // Enum: corrective | systematique | conditionnelle |
            //       ameliorative | predictive | autre
            $table->string('maintenance_type')->nullable()->after('failure_type');

            // ── Opération (une seule case cochée) ─────────────────────
            // Enum: amelioration | controle | diagnostic | nettoyage |
            //       reglage | remplacement
            $table->string('operation_type')->nullable()->after('maintenance_type');
        });

        // ── Lignes pièces : ajouter qty_requested, qty_served, cmup ──
        Schema::table('work_order_parts', function (Blueprint $table) {
            // qty_requested : quantité demandée au magasin
            $table->decimal('qty_requested', 10, 3)->nullable()->after('quantity')
                ->comment('Quantité demandée');
            // qty_served : quantité effectivement servie
            $table->decimal('qty_served', 10, 3)->nullable()->after('qty_requested')
                ->comment('Quantité servie par le magasinier');
            // cmup : coût moyen unitaire pondéré au moment de la sortie
            $table->decimal('cmup', 15, 2)->nullable()->after('qty_served')
                ->comment('CMUP au moment de la sortie stock');
            // unit : unité de mesure (pce, L, kg, m…)
            $table->string('unit', 30)->nullable()->after('cmup');
            // description supplémentaire : marque / N° série
            $table->string('part_description', 255)->nullable()->after('unit')
                ->comment('Marque / N° série de la pièce');
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn([
                'mileage',
                'system_engine', 'system_suspension', 'system_electrical',
                'system_body', 'system_ac',
                'failure_cause', 'failure_cause_comment',
                'failure_type', 'maintenance_type', 'operation_type',
            ]);
        });

        Schema::table('work_order_parts', function (Blueprint $table) {
            $table->dropColumn([
                'qty_requested', 'qty_served', 'cmup', 'unit', 'part_description',
            ]);
        });
    }
};