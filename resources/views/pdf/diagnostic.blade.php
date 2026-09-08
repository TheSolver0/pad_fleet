<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Diagnostic {{ $diagnostic->reference }}</title>
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
        .footer { margin-top: 50px; text-align: center; }
        @media print { body { margin: 10px; } }
    </style>
</head>
<body>
    @include('pdf.partials.header', ['headerTitle' => 'Fiche de Pré-diagnostic'])
    <div class="header" style="margin-bottom:15px;">
        <h3 style="margin:0;color:#555;">Département de Gestion du Patrimoine</h3>
    </div>

    <div class="info-grid">
        <div>
            <div class="field">
                <span class="field-label">Référence:</span>
                <span class="field-value">{{ $diagnostic->reference }}</span>
            </div>
            <div class="field">
                <span class="field-label">Date:</span>
                <span class="field-value">{{ $diagnostic->diagnostic_date->format('d/m/Y') }}</span>
            </div>
            <div class="field">
                <span class="field-label">Véhicule:</span>
                <span class="field-value">{{ $diagnostic->vehicle->registration }}</span>
            </div>
            <div class="field">
                <span class="field-label">Km arrivée:</span>
                <span class="field-value">{{ number_format($diagnostic->km_arrival, 0, '.', ' ') }}</span>
            </div>
        </div>
        <div>
            <div class="field">
                <span class="field-label">Utilisateur:</span>
                <span class="field-value">{{ $diagnostic->user_name }}</span>
            </div>
            <div class="field">
                <span class="field-label">Fonction:</span>
                <span class="field-value">{{ $diagnostic->user_role }}</span>
            </div>
            <div class="field">
                <span class="field-label">Mécanicien:</span>
                <span class="field-value">
                    @if($diagnostic->mechanic)
                        {{ $diagnostic->mechanic->last_name }} {{ $diagnostic->mechanic->first_name }}
                    @else
                        Non assigné
                    @endif
                </span>
            </div>
        </div>
    </div>

    @if($diagnostic->observations)
    <div class="section">
        <div class="section-title">OBSERVATIONS</div>
        <div class="textarea">{{ $diagnostic->observations }}</div>
    </div>
    @endif

    <div class="section">
        <div class="section-title">CONSTATS PAR CATÉGORIE</div>
        
        @if($diagnostic->engine_issues)
        <div class="field">
            <span class="field-label">MOTEUR:</span>
            <div class="textarea">{{ $diagnostic->engine_issues }}</div>
        </div>
        @endif

        @if($diagnostic->suspension_transmission)
        <div class="field">
            <span class="field-label">SUSPENSION / TRANSMISSION:</span>
            <div class="textarea">{{ $diagnostic->suspension_transmission }}</div>
        </div>
        @endif

        @if($diagnostic->braking_system)
        <div class="field">
            <span class="field-label">FREINAGE:</span>
            <div class="textarea">{{ $diagnostic->braking_system }}</div>
        </div>
        @endif

        @if($diagnostic->electronics_electricity)
        <div class="field">
            <span class="field-label">ÉLECTRONIQUE ET ÉLECTRICITÉ:</span>
            <div class="textarea">{{ $diagnostic->electronics_electricity }}</div>
        </div>
        @endif

        @if($diagnostic->bodywork_paint)
        <div class="field">
            <span class="field-label">CARROSSERIE ET PEINTURE:</span>
            <div class="textarea">{{ $diagnostic->bodywork_paint }}</div>
        </div>
        @endif

        @if($diagnostic->air_conditioning)
        <div class="field">
            <span class="field-label">CLIMATISATION:</span>
            <div class="textarea">{{ $diagnostic->air_conditioning }}</div>
        </div>
        @endif

        @if($diagnostic->other_issues)
        <div class="field">
            <span class="field-label">AUTRES:</span>
            <div class="textarea">{{ $diagnostic->other_issues }}</div>
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">TRAVAUX À EFFECTUER</div>
        
        @if($diagnostic->internal_works)
        <div class="field">
            <span class="field-label">TRAVAUX EN INTERNE:</span>
            <div class="textarea">{{ $diagnostic->internal_works }}</div>
        </div>
        @endif

        @if($diagnostic->external_works)
        <div class="field">
            <span class="field-label">TRAVAUX EN EXTERNE:</span>
            <div class="textarea">{{ $diagnostic->external_works }}</div>
        </div>
        @endif
    </div>

    @if($diagnostic->conclusion)
    <div class="section">
        <div class="section-title">CONCLUSION APRÈS DIAGNOSTIC</div>
        <div class="textarea">{{ $diagnostic->conclusion }}</div>
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
                    <span class="field-label">Le Chef de Bureau Maintenance:</span>
                    <div style="height: 50px; border-bottom: 1px solid #ccc;"></div>
                </div>
            </div>
        </div>
        <div style="margin-top: 30px;">
            <div class="field">
                <span class="field-label">Le Chef du Service de la Gestion véhicule du parc Automobile:</span>
                <div style="height: 50px; border-bottom: 1px solid #ccc;"></div>
            </div>
        </div>
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
