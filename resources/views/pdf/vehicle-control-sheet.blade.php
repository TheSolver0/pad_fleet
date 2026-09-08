<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Fiche de contrôle — {{ $sheet->vehicle?->registration }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9pt; color: #111; background: #fff; }

    /* ── En-tête ── */
    .header { display: table; width: 100%; border-bottom: 2.5pt solid #1a5490; padding-bottom: 6pt; margin-bottom: 8pt; }
    .header-logo { display: table-cell; vertical-align: middle; width: 20%; }
    .header-logo-box { width: 60pt; height: 40pt; border: 1pt solid #ccc; text-align: center; font-size: 6pt; color: #999; line-height: 40pt; }
    .header-title { display: table-cell; vertical-align: middle; text-align: center; }
    .header-title h1 { font-size: 13pt; font-weight: bold; color: #1a5490; text-transform: uppercase; letter-spacing: 0.5pt; }
    .header-title p  { font-size: 8pt; color: #555; margin-top: 2pt; }
    .header-ref { display: table-cell; vertical-align: middle; width: 20%; text-align: right; font-size: 7.5pt; color: #555; }

    /* ── Info fiche ── */
    .info-grid { display: table; width: 100%; border: 1pt solid #c5d5e8; border-radius: 3pt; background: #f0f5fb; margin-bottom: 8pt; }
    .info-grid-row { display: table-row; }
    .info-cell { display: table-cell; padding: 3pt 6pt; width: 25%; border-right: 0.5pt solid #d5e0ee; vertical-align: top; }
    .info-cell:last-child { border-right: none; }
    .info-label { font-size: 7pt; color: #666; text-transform: uppercase; letter-spacing: 0.3pt; }
    .info-value { font-size: 9pt; font-weight: bold; color: #1a5490; }

    /* ── Sections ── */
    .section { margin-bottom: 7pt; }
    .section-title { background: #1a5490; color: #fff; font-size: 8.5pt; font-weight: bold; padding: 3pt 6pt; text-transform: uppercase; letter-spacing: 0.3pt; }
    table.checks { width: 100%; border-collapse: collapse; }
    table.checks th, table.checks td { border: 0.5pt solid #b0bec5; padding: 2pt 4pt; font-size: 8pt; }
    table.checks thead th { background: #e8edf5; text-align: center; font-size: 7.5pt; font-weight: bold; }
    table.checks thead th.label-col { text-align: left; width: 42%; }
    table.checks tbody tr:nth-child(even) { background: #f7f9fc; }
    table.checks td.check-col { text-align: center; width: 8%; }
    .ok   { color: #1b7a34; font-weight: bold; font-size: 10pt; }
    .nok  { color: #c0392b; font-weight: bold; font-size: 10pt; }

    /* ── Observations ── */
    .obs-table { display: table; width: 100%; border: 0.5pt solid #b0bec5; margin-bottom: 8pt; }
    .obs-cell  { display: table-cell; width: 50%; padding: 4pt 6pt; vertical-align: top; }
    .obs-cell:first-child { border-right: 0.5pt solid #b0bec5; }
    .obs-label { font-size: 7pt; font-weight: bold; text-transform: uppercase; color: #555; margin-bottom: 3pt; }
    .obs-text  { font-size: 8.5pt; min-height: 30pt; }

    /* ── Signatures ── */
    .sig-outer  { display: table; width: 100%; margin-top: 14pt; border: 1pt solid #b0bec5; border-collapse: collapse; }
    .sig-group  { display: table-cell; vertical-align: top; border: 1pt solid #b0bec5; }
    .sig-group-title { background: #1a5490; color: #fff; font-size: 7.5pt; font-weight: bold; text-align: center; padding: 3pt; text-transform: uppercase; letter-spacing: 0.3pt; }
    .sig-row    { display: table; width: 100%; border-collapse: collapse; }
    .sig-cell   { display: table-cell; width: 50%; padding: 4pt 6pt; text-align: center; vertical-align: bottom; border: 0.5pt solid #b0bec5; }
    .sig-cell-full { padding: 4pt 6pt; text-align: center; vertical-align: bottom; }
    .sig-label  { font-size: 7pt; font-weight: bold; text-transform: uppercase; color: #444; margin-top: 2pt; border-top: 1pt solid #555; padding-top: 2pt; }
    .sig-sublabel { font-size: 6.5pt; color: #777; }

    /* ── Footer ── */
    .footer { margin-top: 10pt; border-top: 0.5pt solid #b0bec5; padding-top: 4pt; font-size: 7pt; color: #888; text-align: center; }

    /* ── Page break helpers ── */
    .page-break { page-break-after: always; }
    .no-break   { page-break-inside: avoid; }
</style>
</head>
<body>

{{-- ══ EN-TÊTE ══ --}}
<div class="header">
    <div class="header-logo">
        <img src="{{ public_path('img/logo.png') }}" style="max-height:40pt;max-width:55pt;" alt="PAD">
    </div>
    <div class="header-title">
        <p style="font-size:7.5pt;color:#666;text-transform:uppercase;letter-spacing:0.3pt;margin-bottom:2pt;">Port Autonome de Douala — Direction des Affaires Générales</p>
        <h1>Fiche de Contrôle Véhicule</h1>
        <p>Avant et après déplacement</p>
    </div>
    <div class="header-ref">
        @if($sheet->ordre_mission)Réf : {{ $sheet->ordre_mission }}<br>@endif
        Édité le : {{ now()->format('d/m/Y') }}
    </div>
</div>

{{-- ══ INFORMATIONS PRINCIPALES ══ --}}
<div class="info-grid">
    <div class="info-grid-row">
        <div class="info-cell">
            <div class="info-label">Véhicule</div>
            <div class="info-value">{{ $sheet->vehicle?->registration ?? '—' }}</div>
        </div>
        <div class="info-cell">
            <div class="info-label">Chauffeur</div>
            <div class="info-value">{{ $sheet->driver?->full_name ?? '—' }}</div>
        </div>
        <div class="info-cell">
            <div class="info-label">Lieu de départ</div>
            <div class="info-value">{{ $sheet->lieu ?? '—' }}</div>
        </div>
        <div class="info-cell">
            <div class="info-label">N° Ordre de mission</div>
            <div class="info-value">{{ $sheet->ordre_mission ?? '—' }}</div>
        </div>
    </div>
    <div class="info-grid-row">
        <div class="info-cell">
            <div class="info-label">Destination</div>
            <div class="info-value">{{ $sheet->destination ?? ($sheet->mission?->destination ?? '—') }}</div>
        </div>
        <div class="info-cell">
            <div class="info-label">Date départ</div>
            <div class="info-value">{{ $sheet->date_depart?->format('d/m/Y') ?? '—' }}</div>
        </div>
        <div class="info-cell">
            <div class="info-label">Date retour</div>
            <div class="info-value">{{ $sheet->date_retour?->format('d/m/Y') ?? '—' }}</div>
        </div>
        <div class="info-cell">
            <div class="info-label">KM départ / retour</div>
            <div class="info-value">
                {{ $sheet->km_depart !== null ? number_format($sheet->km_depart, 0, ',', ' ') : '—' }}
                →
                {{ $sheet->km_retour !== null ? number_format($sheet->km_retour, 0, ',', ' ') : '—' }} km
            </div>
        </div>
    </div>
</div>

@php
$checkSections = [
    ['key' => 'docs_administratifs',     'title' => 'Dossier Administratif',          'type' => 'docs'],
    ['key' => 'controle_exterieur',       'title' => 'Contrôle Extérieur du Véhicule', 'type' => 'check4'],
    ['key' => 'compartiment_moteur',      'title' => 'Compartiment Moteur',             'type' => 'check4'],
    ['key' => 'controle_fonctionnalites', 'title' => 'Contrôle des Fonctionnalités',    'type' => 'check4'],
    ['key' => 'outillages',               'title' => 'Outillages / Accessoires',        'type' => 'outillage'],
];
@endphp

{{-- ══ SECTIONS DE CONTRÔLE ══ --}}
@foreach($checkSections as $section)
@php $sData = $sheet->{$section['key']} ?? []; @endphp
<div class="section no-break">
    <div class="section-title">{{ $section['title'] }}</div>
    <table class="checks">
        <thead>
            <tr>
                <th class="label-col">Élément</th>
                @if($section['type'] === 'docs')
                    <th colspan="2">Présent</th>
                    <th colspan="2">Non présent</th>
                @elseif($section['type'] === 'outillage')
                    <th colspan="2">Présent</th>
                    <th colspan="2">Absent</th>
                @else
                    <th colspan="2">Correct</th>
                    <th colspan="2">Défaut</th>
                @endif
            </tr>
            <tr>
                <th class="label-col"></th>
                <th>Dép.</th><th>Ret.</th>
                <th>Dép.</th><th>Ret.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sData as $itemKey => $vals)
            @php
                if ($section['type'] === 'docs') {
                    $pd = $vals['depart'] ?? null;
                    $pr = $vals['retour'] ?? null;
                    $c1 = $pd === 'ok';     $c2 = $pr === 'ok';
                    $c3 = $pd === 'absent'; $c4 = $pr === 'absent';
                } elseif ($section['type'] === 'outillage') {
                    $c1 = $vals['present_d'] ?? false;
                    $c2 = $vals['present_r'] ?? false;
                    $c3 = $vals['absent_d']  ?? false;
                    $c4 = $vals['absent_r']  ?? false;
                } else {
                    $c1 = $vals['correct_d'] ?? false;
                    $c2 = $vals['correct_r'] ?? false;
                    $c3 = $vals['defaut_d']  ?? false;
                    $c4 = $vals['defaut_r']  ?? false;
                }
            @endphp
            <tr>
                <td>{{ $labels[$itemKey] ?? $itemKey }}</td>
                <td class="check-col"><span class="ok">{{ $c1 ? '✓' : '' }}</span></td>
                <td class="check-col"><span class="ok">{{ $c2 ? '✓' : '' }}</span></td>
                <td class="check-col"><span class="{{ $c3 ? 'nok' : '' }}">{{ $c3 ? '✗' : '' }}</span></td>
                <td class="check-col"><span class="{{ $c4 ? 'nok' : '' }}">{{ $c4 ? '✗' : '' }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endforeach

{{-- ══ OBSERVATIONS ══ --}}
@if($sheet->observations_depart || $sheet->observations_retour)
<div class="obs-table no-break" style="margin-top:8pt">
    <div class="obs-cell">
        <div class="obs-label">Observations départ</div>
        <div class="obs-text">{{ $sheet->observations_depart ?? '' }}</div>
    </div>
    <div class="obs-cell">
        <div class="obs-label">Observations retour</div>
        <div class="obs-text">{{ $sheet->observations_retour ?? '' }}</div>
    </div>
</div>
@endif

{{-- ══ SIGNATURES ══ --}}
<div class="sig-outer no-break">
    {{-- Colonne CHAUFFEUR (2 sous-colonnes Départ / Retour) --}}
    <div class="sig-group" style="width:66%">
        <div class="sig-group-title">Chauffeur</div>
        <div class="sig-row">
            <div class="sig-cell">
                @if($sheet->signature_depart_path)
                    <img src="{{ public_path('storage/' . $sheet->signature_depart_path) }}" style="max-height:50pt;max-width:120pt;" alt="">
                @else
                    <div style="height:50pt;"></div>
                @endif
                <div class="sig-label">DÉPART</div>
            </div>
            <div class="sig-cell">
                @if($sheet->signature_retour_path)
                    <img src="{{ public_path('storage/' . $sheet->signature_retour_path) }}" style="max-height:50pt;max-width:120pt;" alt="">
                @else
                    <div style="height:50pt;"></div>
                @endif
                <div class="sig-label">RETOUR</div>
            </div>
        </div>
    </div>
    {{-- Colonne BUREAU DE CONTRÔLE --}}
    <div class="sig-group" style="width:34%">
        <div class="sig-group-title">Bureau de Contrôle &amp; Gestion du Parc</div>
        <div class="sig-cell-full">
            @if($sheet->signature_bureau_path)
                <img src="{{ public_path('storage/' . $sheet->signature_bureau_path) }}" style="max-height:50pt;max-width:120pt;" alt="">
            @else
                <div style="height:50pt;"></div>
            @endif
            <div class="sig-label">Nom &amp; Signature</div>
        </div>
    </div>
</div>

{{-- ══ PIED DE PAGE ══ --}}
<div class="footer">
    Fiche de contrôle véhicule — {{ $sheet->vehicle?->registration }} —
    Générée le {{ now()->format('d/m/Y à H:i') }}
    @if($sheet->ordre_mission) — OM : {{ $sheet->ordre_mission }}@endif
</div>

</body>
</html>
