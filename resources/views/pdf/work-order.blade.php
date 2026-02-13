<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bon de Travail {{ $workOrder->reference }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .section { margin-bottom: 25px; }
        .section-title { font-weight: bold; color: #0066cc; margin-bottom: 10px; }
        .field { margin-bottom: 8px; }
        .field-label { font-weight: bold; }
        .field-value { background: #f5f5f5; padding: 5px; display: inline-block; min-width: 200px; }
        .textarea { background: #f5f5f5; padding: 10px; min-height: 80px; white-space: pre-wrap; }
        .cost-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
        .cost-total { background: #e6f3ff; font-weight: bold; }
        .footer { margin-top: 50px; text-align: center; }
        @media print { body { margin: 10px; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>BON DE TRAVAIL</h1>
        <h2>PORT AUTONOME DE DOUALA - DIRECTION DES AFFAIRES GÉNÉRALES</h2>
        <h3>DÉPARTEMENT DE GESTION DU PATRIMOINE</h3>
    </div>

    <div class="info-grid">
        <div>
            <div class="field">
                <span class="field-label">Référence:</span>
                <span class="field-value">{{ $workOrder->reference }}</span>
            </div>
            <div class="field">
                <span class="field-label">Date:</span>
                <span class="field-value">{{ $workOrder->work_date->format('d/m/Y') }}</span>
            </div>
            <div class="field">
                <span class="field-label">Véhicule:</span>
                <span class="field-value">{{ $workOrder->vehicle->registration }}</span>
            </div>
            @if($workOrder->start_time && $workOrder->end_time)
            <div class="field">
                <span class="field-label">Heures:</span>
                <span class="field-value">{{ $workOrder->start_time->format('H:i') }} - {{ $workOrder->end_time->format('H:i') }}</span>
            </div>
            @endif
        </div>
        <div>
            <div class="field">
                <span class="field-label">Mécanicien:</span>
                <span class="field-value">{{ $workOrder->mechanic->last_name }} {{ $workOrder->mechanic->first_name }}</span>
            </div>
            @if($workOrder->diagnostic)
            <div class="field">
                <span class="field-label">Diagnostic associé:</span>
                <span class="field-value">{{ $workOrder->diagnostic->reference }}</span>
            </div>
            @endif
            <div class="field">
                <span class="field-label">Statut:</span>
                <span class="field-value">{{ $workOrder->status_label }}</span>
            </div>
            @if($workOrder->validation_date)
            <div class="field">
                <span class="field-label">Date validation:</span>
                <span class="field-value">{{ $workOrder->validation_date->format('d/m/Y') }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">DESCRIPTION DES TRAVAUX</div>
        <div class="textarea">{{ $workOrder->work_description }}</div>
    </div>

    <div class="section">
        <div class="section-title">PIÈCES ET MATÉRIEL</div>
        
        @if($workOrder->parts_used)
        <div class="field">
            <span class="field-label">PIÈCES UTILISÉES:</span>
            <div class="textarea">{{ $workOrder->parts_used }}</div>
        </div>
        @endif

        @if($workOrder->parts_removed)
        <div class="field">
            <span class="field-label">PIÈCES RETIRÉES:</span>
            <div class="textarea">{{ $workOrder->parts_removed }}</div>
        </div>
        @endif

        @if($workOrder->equipment_used)
        <div class="field">
            <span class="field-label">MATÉRIEL UTILISÉ:</span>
            <div class="textarea">{{ $workOrder->equipment_used }}</div>
        </div>
        @endif

        @if($workOrder->tools_used)
        <div class="field">
            <span class="field-label">OUTILS UTILISÉS:</span>
            <div class="textarea">{{ $workOrder->tools_used }}</div>
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">DÉTAILS TECHNIQUES</div>
        
        @if($workOrder->problems_found)
        <div class="field">
            <span class="field-label">PROBLÈMES IDENTIFIÉS:</span>
            <div class="textarea">{{ $workOrder->problems_found }}</div>
        </div>
        @endif

        @if($workOrder->solutions_applied)
        <div class="field">
            <span class="field-label">SOLUTIONS APPLIQUÉES:</span>
            <div class="textarea">{{ $workOrder->solutions_applied }}</div>
        </div>
        @endif

        @if($workOrder->technical_notes)
        <div class="field">
            <span class="field-label">NOTES TECHNIQUES:</span>
            <div class="textarea">{{ $workOrder->technical_notes }}</div>
        </div>
        @endif

        @if($workOrder->quality_control)
        <div class="field">
            <span class="field-label">CONTRÔLE QUALITÉ:</span>
            <div class="textarea">{{ $workOrder->quality_control }}</div>
        </div>
        @endif

        @if($workOrder->final_checks)
        <div class="field">
            <span class="field-label">VÉRIFICATIONS FINALES:</span>
            <div class="textarea">{{ $workOrder->final_checks }}</div>
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">COÛTS</div>
        <div class="cost-grid">
            <div>
                <span class="field-label">Main d'œuvre:</span>
                <div class="field-value">
                    @if($workOrder->labor_cost)
                        {{ number_format($workOrder->labor_cost, 2, ',', ' ') }} FCFA
                    @else
                        -
                    @endif
                </div>
            </div>
            <div>
                <span class="field-label">Pièces:</span>
                <div class="field-value">
                    @if($workOrder->parts_cost)
                        {{ number_format($workOrder->parts_cost, 2, ',', ' ') }} FCFA
                    @else
                        -
                    @endif
                </div>
            </div>
            <div>
                <span class="field-label">Total:</span>
                <div class="field-value cost-total">
                    @if($workOrder->total_cost)
                        {{ number_format($workOrder->total_cost, 2, ',', ' ') }} FCFA
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($workOrder->completion_notes)
    <div class="section">
        <div class="section-title">NOTES DE FIN DE TRAVAUX</div>
        <div class="textarea">{{ $workOrder->completion_notes }}</div>
    </div>
    @endif

    <div class="footer">
        <div class="info-grid">
            <div>
                <div class="field">
                    <span class="field-label">Le mécanicien:</span>
                    <div style="height: 50px; border-bottom: 1px solid #ccc;"></div>
                </div>
            </div>
            <div>
                <div class="field">
                    <span class="field-label">Le superviseur:</span>
                    <div style="height: 50px; border-bottom: 1px solid #ccc;"></div>
                </div>
            </div>
        </div>
        <div style="margin-top: 30px;">
            <div class="field">
                <span class="field-label">Le client:</span>
                <div style="height: 50px; border-bottom: 1px solid #ccc;"></div>
            </div>
        </div>
        @if($workOrder->validation_date)
        <div style="margin-top: 20px; text-align: center;">
            <strong>Date de validation: {{ $workOrder->validation_date->format('d/m/Y') }}</strong>
        </div>
        @endif
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 12px; color: #666;">
        <p>Pôle de Référence au Cœur du Golfe de Guinée | Pole Of Reference at the Heart of the Gulf of Guinea</p>
        <p>Société Anonyme S.A à Capital public | Capital social: FCFA 18 902 000 000</p>
        <p>N° de Contribuable: M069900009499X | RCCM: 030.153 | NACAM: 034006</p>
        <p>B.P./P.O. Box 4020 Douala, Bonanjo-Centre des Affaires Maritimes</p>
        <p>Tél: +237 233 42 01 33 | Fax: +237 233 42 67 97 | www.oad.cm</p>
    </div>
</body>
</html>
