<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bon de Travail {{ $workOrder->reference }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #0066cc;
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header h2 {
            color: #333;
            margin: 5px 0;
            font-size: 16px;
            font-weight: bold;
        }
        .header h3 {
            color: #666;
            margin: 5px 0;
            font-size: 14px;
        }
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-section .left, .info-section .right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }
        .field {
            margin-bottom: 8px;
            clear: both;
        }
        .field-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
            color: #0066cc;
        }
        .field-value {
            background: #f8f9fa;
            padding: 4px 8px;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            display: inline-block;
            min-height: 20px;
            line-height: 1.4;
        }
        .textarea {
            background: #f8f9fa;
            padding: 8px;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            min-height: 60px;
            white-space: pre-wrap;
            line-height: 1.4;
            width: 100%;
            box-sizing: border-box;
        }
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .section-title {
            font-weight: bold;
            color: #0066cc;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #0066cc;
            padding-bottom: 5px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .table th, .table td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            text-align: left;
        }
        .table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #0066cc;
        }
        .cost-summary {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .cost-summary .cost-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 10px;
        }
        .cost-summary .cost-label {
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 5px;
        }
        .cost-summary .cost-value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .cost-summary .cost-total {
            background: #e3f2fd;
            border: 2px solid #0066cc;
        }
        .systems-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .systems-grid .system-item {
            display: table-cell;
            width: 20%;
            text-align: center;
            padding: 8px;
            border: 1px solid #dee2e6;
        }
        .systems-grid .system-label {
            font-weight: bold;
            color: #0066cc;
            font-size: 10px;
            margin-bottom: 5px;
        }
        .systems-grid .system-value {
            font-size: 12px;
            padding: 4px;
            background: #f8f9fa;
            border-radius: 3px;
        }
        .signatures {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 20px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            height: 40px;
            margin-bottom: 10px;
        }
        .signature-label {
            font-weight: bold;
            color: #0066cc;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }
        .footer p {
            margin: 3px 0;
        }
        .progress-bar {
            background: #e9ecef;
            height: 20px;
            border-radius: 10px;
            margin: 5px 0;
            overflow: hidden;
        }
        .progress-fill {
            background: #0066cc;
            height: 100%;
            border-radius: 10px;
        }
        @media print {
            body { margin: 10px; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <h1>BON DE TRAVAIL</h1>
        <h2>PORT AUTONOME DE DOUALA</h2>
        <h3>DIRECTION DES AFFAIRES GÉNÉRALES - DÉPARTEMENT DE GESTION DU PATRIMOINE</h3>
    </div>

    <!-- INFORMATIONS GÉNÉRALES -->
    <div class="section">
        <div class="section-title">Informations générales</div>
        <div class="info-section">
            <div class="left">
                <div class="field">
                    <span class="field-label">Référence:</span>
                    <span class="field-value">{{ $workOrder->reference }}</span>
                </div>
                <div class="field">
                    <span class="field-label">Date travaux:</span>
                    <span class="field-value">{{ $workOrder->work_date->format('d/m/Y') }}</span>
                </div>
                <div class="field">
                    <span class="field-label">Véhicule:</span>
                    <span class="field-value">{{ $workOrder->vehicle->registration ?? 'N/A' }}</span>
                </div>
                @if($workOrder->mileage)
                <div class="field">
                    <span class="field-label">Kilométrage:</span>
                    <span class="field-value">{{ number_format($workOrder->mileage, 0, ',', ' ') }} km</span>
                </div>
                @endif
                @if($workOrder->transfer_reference)
                <div class="field">
                    <span class="field-label">Réf. transfert:</span>
                    <span class="field-value">{{ $workOrder->transfer_reference }}</span>
                </div>
                @endif
                @if($workOrder->transfer_date)
                <div class="field">
                    <span class="field-label">Date transfert:</span>
                    <span class="field-value">{{ $workOrder->transfer_date->format('d/m/Y') }}</span>
                </div>
                @endif
            </div>
            <div class="right">
                <div class="field">
                    <span class="field-label">Mécanicien:</span>
                    <span class="field-value">{{ $workOrder->mechanic->last_name ?? '' }} {{ $workOrder->mechanic->first_name ?? '' }}</span>
                </div>
                @if($workOrder->diagnostic)
                <div class="field">
                    <span class="field-label">Diagnostic:</span>
                    <span class="field-value">{{ $workOrder->diagnostic->reference ?? 'N/A' }}</span>
                </div>
                @endif
                <div class="field">
                    <span class="field-label">Statut:</span>
                    <span class="field-value">{{ $workOrder->status_label }}</span>
                </div>
                @if($workOrder->completion_percent)
                <div class="field">
                    <span class="field-label">Progression:</span>
                    <span class="field-value">{{ $workOrder->completion_percent }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $workOrder->completion_percent }}%"></div>
                </div>
                @endif
                @if($workOrder->start_time && $workOrder->end_time)
                <div class="field">
                    <span class="field-label">Durée:</span>
                    <span class="field-value">{{ $workOrder->start_time->format('H:i') }} - {{ $workOrder->end_time->format('H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- CLASSIFICATION DE L'INCIDENT -->
    @if($workOrder->failure_cause || $workOrder->failure_type || $workOrder->maintenance_type || $workOrder->operation_type)
    <div class="section">
        <div class="section-title">Classification de l'incident</div>
        <div class="info-section">
            <div class="left">
                @if($workOrder->failure_cause)
                <div class="field">
                    <span class="field-label">Cause défaillance:</span>
                    <span class="field-value">{{ $workOrder->failure_cause_label }}</span>
                </div>
                @endif
                @if($workOrder->failure_type)
                <div class="field">
                    <span class="field-label">Type défaillance:</span>
                    <span class="field-value">{{ $workOrder->failure_type_label }}</span>
                </div>
                @endif
            </div>
            <div class="right">
                @if($workOrder->maintenance_type)
                <div class="field">
                    <span class="field-label">Type maintenance:</span>
                    <span class="field-value">{{ $workOrder->maintenance_type_label }}</span>
                </div>
                @endif
                @if($workOrder->operation_type)
                <div class="field">
                    <span class="field-label">Opération:</span>
                    <span class="field-value">{{ $workOrder->operation_type_label }}</span>
                </div>
                @endif
            </div>
        </div>
        @if($workOrder->failure_cause_comment)
        <div class="field">
            <span class="field-label">Commentaire:</span>
            <div class="textarea">{{ $workOrder->failure_cause_comment }}</div>
        </div>
        @endif
    </div>
    @endif

    <!-- ÉTAT DES SYSTÈMES -->
    @if($workOrder->system_engine || $workOrder->system_suspension || $workOrder->system_electrical || $workOrder->system_body || $workOrder->system_ac)
    <div class="section">
        <div class="section-title">État des systèmes</div>
        <div class="systems-grid">
            <div class="system-item">
                <div class="system-label">Moteur</div>
                <div class="system-value">{{ $workOrder->system_engine ? \App\Models\WorkOrder::systemStates()[$workOrder->system_engine] : '-' }}</div>
            </div>
            <div class="system-item">
                <div class="system-label">Suspension / Transmission</div>
                <div class="system-value">{{ $workOrder->system_suspension ? \App\Models\WorkOrder::systemStates()[$workOrder->system_suspension] : '-' }}</div>
            </div>
            <div class="system-item">
                <div class="system-label">Électricité / Électronique</div>
                <div class="system-value">{{ $workOrder->system_electrical ? \App\Models\WorkOrder::systemStates()[$workOrder->system_electrical] : '-' }}</div>
            </div>
            <div class="system-item">
                <div class="system-label">Carrosserie</div>
                <div class="system-value">{{ $workOrder->system_body ? \App\Models\WorkOrder::systemStates()[$workOrder->system_body] : '-' }}</div>
            </div>
            <div class="system-item">
                <div class="system-label">Climatisation</div>
                <div class="system-value">{{ $workOrder->system_ac ? \App\Models\WorkOrder::systemStates()[$workOrder->system_ac] : '-' }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- DESCRIPTION DES TRAVAUX -->
    <div class="section">
        <div class="section-title">Description des travaux</div>
        <div class="textarea">{{ $workOrder->work_description }}</div>
    </div>

    <!-- TÂCHES -->
    @if($workOrder->tasks->count() > 0)
    <div class="section">
        <div class="section-title">Tâches réalisées</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Tâche</th>
                    <th>Mécanicien</th>
                    <th>Durée estimée</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($workOrder->tasks as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->mechanic ? $task->mechanic->first_name . ' ' . $task->mechanic->last_name : '-' }}</td>
                    <td>{{ $task->estimated_minutes }} min</td>
                    <td>{{ $task->is_done ? 'Terminée' : 'En cours' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- PIÈCES ET MATÉRIEL -->
    <div class="section">
        <div class="section-title">Pièces et matériel</div>

        @if($workOrder->parts->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Désignation</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Total</th>
                    <th>Emplacement</th>
                </tr>
            </thead>
            <tbody>
                @foreach($workOrder->parts as $part)
                <tr>
                    <td>{{ $part->article->reference ?? 'N/A' }}</td>
                    <td>{{ $part->article->name ?? 'N/A' }}</td>
                    <td>{{ $part->quantity }}</td>
                    <td>{{ number_format($part->unit_price ?? 0, 2, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($part->total_cost ?? 0, 2, ',', ' ') }} FCFA</td>
                    <td>{{ $part->stock_location }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if($workOrder->parts_used)
        <div class="field">
            <span class="field-label">Pièces utilisées:</span>
            <div class="textarea">{{ $workOrder->parts_used }}</div>
        </div>
        @endif

        @if($workOrder->parts_removed)
        <div class="field">
            <span class="field-label">Pièces retirées:</span>
            <div class="textarea">{{ $workOrder->parts_removed }}</div>
        </div>
        @endif

        @if($workOrder->equipment_used)
        <div class="field">
            <span class="field-label">Matériel utilisé:</span>
            <div class="textarea">{{ $workOrder->equipment_used }}</div>
        </div>
        @endif

        @if($workOrder->tools_used)
        <div class="field">
            <span class="field-label">Outils utilisés:</span>
            <div class="textarea">{{ $workOrder->tools_used }}</div>
        </div>
        @endif
    </div>

    <!-- DÉTAILS TECHNIQUES -->
    <div class="section">
        <div class="section-title">Détails techniques</div>

        @if($workOrder->problems_found)
        <div class="field">
            <span class="field-label">Problèmes identifiés:</span>
            <div class="textarea">{{ $workOrder->problems_found }}</div>
        </div>
        @endif

        @if($workOrder->solutions_applied)
        <div class="field">
            <span class="field-label">Solutions appliquées:</span>
            <div class="textarea">{{ $workOrder->solutions_applied }}</div>
        </div>
        @endif

        @if($workOrder->technical_notes)
        <div class="field">
            <span class="field-label">Notes techniques:</span>
            <div class="textarea">{{ $workOrder->technical_notes }}</div>
        </div>
        @endif

        @if($workOrder->quality_control)
        <div class="field">
            <span class="field-label">Contrôle qualité:</span>
            <div class="textarea">{{ $workOrder->quality_control }}</div>
        </div>
        @endif

        @if($workOrder->final_checks)
        <div class="field">
            <span class="field-label">Vérifications finales:</span>
            <div class="textarea">{{ $workOrder->final_checks }}</div>
        </div>
        @endif
    </div>

    <!-- COÛTS -->
    <div class="section">
        <div class="section-title">Récapitulatif des coûts</div>
        <div class="cost-summary">
            <div class="cost-item">
                <div class="cost-label">Main d'œuvre</div>
                <div class="cost-value">
                    @if($workOrder->labor_cost)
                        {{ number_format($workOrder->labor_cost, 2, ',', ' ') }} FCFA
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="cost-item">
                <div class="cost-label">Pièces</div>
                <div class="cost-value">
                    @if($workOrder->parts_cost)
                        {{ number_format($workOrder->parts_cost, 2, ',', ' ') }} FCFA
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="cost-item cost-total">
                <div class="cost-label">Total</div>
                <div class="cost-value">
                    @if($workOrder->total_cost)
                        {{ number_format($workOrder->total_cost, 2, ',', ' ') }} FCFA
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- NOTES DE FIN DE TRAVAUX -->
    @if($workOrder->completion_notes)
    <div class="section">
        <div class="section-title">Notes de fin de travaux</div>
        <div class="textarea">{{ $workOrder->completion_notes }}</div>
    </div>
    @endif

    <!-- PHOTOS -->
    @if($workOrder->photos->count() > 0)
    <div class="section">
        <div class="section-title">Photos avant/après</div>
        <div style="text-align: center; color: #666; font-style: italic;">
            Les photos sont disponibles dans l'application web
        </div>
    </div>
    @endif

    <!-- SIGNATURES -->
    <div class="signatures">
        <div class="section-title">Validations</div>
        <div class="signature-row">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Le mécanicien</div>
                @if($workOrder->mechanic_signature)
                <div style="font-size: 10px; color: #666; margin-top: 5px;">Signé le {{ $workOrder->updated_at->format('d/m/Y') }}</div>
                @endif
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Le superviseur</div>
                @if($workOrder->supervisor_signature)
                <div style="font-size: 10px; color: #666; margin-top: 5px;">Signé le {{ $workOrder->updated_at->format('d/m/Y') }}</div>
                @endif
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Le client</div>
                @if($workOrder->client_signature && $workOrder->validation_date)
                <div style="font-size: 10px; color: #666; margin-top: 5px;">Validé le {{ $workOrder->validation_date->format('d/m/Y') }}</div>
                @endif
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p><strong>Pôle de Référence au Cœur du Golfe de Guinée | Pole Of Reference at the Heart of the Gulf of Guinea</strong></p>
        <p>Société Anonyme S.A à Capital public | Capital social: FCFA 18 902 000 000</p>
        <p>N° de Contribuable: M069900009499X | RCCM: 030.153 | NACAM: 034006</p>
        <p>B.P./P.O. Box 4020 Douala, Bonanjo-Centre des Affaires Maritimes</p>
        <p>Tél: +237 233 42 01 33 | Fax: +237 233 42 67 97 | www.oad.cm</p>
        <p style="margin-top: 10px; font-size: 9px; color: #999;">
            Document généré le {{ now()->format('d/m/Y à H:i') }} - Référence: {{ $workOrder->reference }}
        </p>
    </div>
</body>
</html>
