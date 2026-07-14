{{-- Vue lisible de la fiche de contrôle (modal ou impression) --}}
@php
    $checkSections = [
        ['key' => 'docs_administratifs',      'title' => 'Dossier Administratif',           'type' => 'docs'],
        ['key' => 'controle_exterieur',        'title' => 'Contrôle Extérieur du Véhicule',  'type' => 'check4'],
        ['key' => 'compartiment_moteur',       'title' => 'Compartiment Moteur',              'type' => 'check4'],
        ['key' => 'controle_fonctionnalites',  'title' => 'Contrôle des Fonctionnalités',     'type' => 'check4'],
        ['key' => 'outillages',                'title' => 'Outillages / Accessoires',         'type' => 'outillage'],
    ];
@endphp

<div class="row g-2 mb-3" style="font-size:.88rem">
    <div class="col-md-3"><strong>Véhicule :</strong> {{ $sheet->vehicle?->registration ?? '—' }}</div>
    <div class="col-md-3"><strong>Chauffeur :</strong> {{ $sheet->driver?->full_name ?? '—' }}</div>
    <div class="col-md-3"><strong>N° OM :</strong> {{ $sheet->ordre_mission ?? '—' }}</div>
    <div class="col-md-3"><strong>Lieu :</strong> {{ $sheet->lieu ?? '—' }}</div>
    <div class="col-md-3"><strong>Départ :</strong> {{ $sheet->date_depart?->format('d/m/Y') ?? '—' }}</div>
    <div class="col-md-3"><strong>Retour :</strong> {{ $sheet->date_retour?->format('d/m/Y') ?? '—' }}</div>
    <div class="col-md-3"><strong>KM départ :</strong> {{ number_format($sheet->km_depart ?? 0) }}</div>
    <div class="col-md-3"><strong>KM retour :</strong> {{ number_format($sheet->km_retour ?? 0) }}</div>
</div>

@foreach($checkSections as $section)
@php $sData = $sheet->{$section['key']} ?? []; @endphp
<div class="card mb-2 border">
    <div class="card-header py-1 fw-bold" style="background:rgba(26,84,144,.07);font-size:.85rem">{{ $section['title'] }}</div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0" style="font-size:.78rem">
            <thead class="text-center" style="background:#f8f9fa">
                <tr>
                    <th style="width:40%" class="text-start">Élément</th>
                    @if($section['type'] === 'docs')
                        <th colspan="2">Présent</th><th colspan="2">Non présent</th>
                    @elseif($section['type'] === 'outillage')
                        <th colspan="2">Présent</th><th colspan="2">Absent</th>
                    @else
                        <th colspan="2">Correct</th><th colspan="2">Défaut</th>
                    @endif
                </tr>
                <tr style="font-size:.72rem"><th></th><th>D</th><th>R</th><th>D</th><th>R</th></tr>
            </thead>
            <tbody>
                @foreach($sData as $itemKey => $vals)
                <tr>
                    <td>{{ $labels[$itemKey] ?? $itemKey }}</td>
                    @if($section['type'] === 'docs')
                        @php $pd=$vals['depart']??null; $pr=$vals['retour']??null; @endphp
                        <td class="text-center">@if($pd==='ok') ✓ @endif</td>
                        <td class="text-center">@if($pr==='ok') ✓ @endif</td>
                        <td class="text-center">@if($pd==='absent') ✗ @endif</td>
                        <td class="text-center">@if($pr==='absent') ✗ @endif</td>
                    @else
                        @php
                            $k1 = $section['type']==='outillage' ? 'present_d' : 'correct_d';
                            $k2 = $section['type']==='outillage' ? 'present_r' : 'correct_r';
                            $k3 = $section['type']==='outillage' ? 'absent_d'  : 'defaut_d';
                            $k4 = $section['type']==='outillage' ? 'absent_r'  : 'defaut_r';
                        @endphp
                        <td class="text-center">@if($vals[$k1]??false) ✓ @endif</td>
                        <td class="text-center">@if($vals[$k2]??false) ✓ @endif</td>
                        <td class="text-center @if($vals[$k3]??false) text-danger @endif">@if($vals[$k3]??false) ✗ @endif</td>
                        <td class="text-center @if($vals[$k4]??false) text-danger @endif">@if($vals[$k4]??false) ✗ @endif</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@endforeach

@if($sheet->observations_depart || $sheet->observations_retour)
<div class="row g-2 mt-1">
    @if($sheet->observations_depart)
    <div class="col-md-6">
        <strong style="font-size:.82rem">Observations départ :</strong>
        <p class="mb-0" style="font-size:.82rem">{{ $sheet->observations_depart }}</p>
    </div>
    @endif
    @if($sheet->observations_retour)
    <div class="col-md-6">
        <strong style="font-size:.82rem">Observations retour :</strong>
        <p class="mb-0" style="font-size:.82rem">{{ $sheet->observations_retour }}</p>
    </div>
    @endif
</div>
@endif

@if($sheet->signature_depart_path || $sheet->signature_retour_path || $sheet->signature_bureau_path)
<hr>
<div class="row g-3 mt-1">
    @if($sheet->signature_depart_path)
    <div class="col-md-4">
        <div class="card border text-center">
            <div class="card-header py-1 small fw-semibold" style="background:rgba(26,84,144,.07)">CHAUFFEUR — DÉPART</div>
            <div class="card-body p-2">
                <img src="{{ asset('storage/' . $sheet->signature_depart_path) }}" style="max-height:80px;max-width:100%;object-fit:contain" alt="Signature départ">
            </div>
        </div>
    </div>
    @endif
    @if($sheet->signature_retour_path)
    <div class="col-md-4">
        <div class="card border text-center">
            <div class="card-header py-1 small fw-semibold" style="background:rgba(26,84,144,.07)">CHAUFFEUR — RETOUR</div>
            <div class="card-body p-2">
                <img src="{{ asset('storage/' . $sheet->signature_retour_path) }}" style="max-height:80px;max-width:100%;object-fit:contain" alt="Signature retour">
            </div>
        </div>
    </div>
    @endif
    @if($sheet->signature_bureau_path)
    <div class="col-md-4">
        <div class="card border text-center">
            <div class="card-header py-1 small fw-semibold" style="background:rgba(26,84,144,.07)">BUREAU DE CONTRÔLE</div>
            <div class="card-body p-2">
                <img src="{{ asset('storage/' . $sheet->signature_bureau_path) }}" style="max-height:80px;max-width:100%;object-fit:contain" alt="Signature bureau">
            </div>
        </div>
    </div>
    @endif
</div>
@endif

{{-- Photos --}}
@if($sheet->photos->count() > 0)
@php $beforeP = $sheet->photos->where('type','before'); $afterP = $sheet->photos->where('type','after'); @endphp
<hr>
<div class="row g-3 mt-1">
    @if($beforeP->count())
    <div class="col-md-6">
        <h6 class="fw-bold text-muted"><i class="bi bi-clock me-1"></i>Avant mission</h6>
        <div class="d-flex flex-wrap gap-2">
            @foreach($beforeP as $p)
            <a href="{{ $p->url }}" target="_blank">
                <img src="{{ $p->url }}" class="img-thumbnail" style="width:120px;height:85px;object-fit:cover" alt="">
            </a>
            @endforeach
        </div>
    </div>
    @endif
    @if($afterP->count())
    <div class="col-md-6">
        <h6 class="fw-bold text-muted"><i class="bi bi-check-circle me-1"></i>Après mission</h6>
        <div class="d-flex flex-wrap gap-2">
            @foreach($afterP as $p)
            <a href="{{ $p->url }}" target="_blank">
                <img src="{{ $p->url }}" class="img-thumbnail" style="width:120px;height:85px;object-fit:cover" alt="">
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif
