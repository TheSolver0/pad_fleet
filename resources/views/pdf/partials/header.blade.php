{{-- En-tête PAD commun à tous les exports PDF (logo + typographie). --}}
<div style="display:table; width:100%; border-bottom:2.5pt solid #1a5490; padding-bottom:6pt; margin-bottom:10pt;">
    <div style="display:table-cell; vertical-align:middle; width:60pt;">
        <img src="{{ public_path('img/logo.png') }}" style="max-height:42pt; max-width:55pt;" alt="PAD">
    </div>
    <div style="display:table-cell; vertical-align:middle; text-align:center;">
        <div style="font-size:13pt; font-weight:bold; color:#1a5490; text-transform:uppercase; letter-spacing:0.5pt;">Port Autonome de Douala</div>
        <div style="font-size:8.5pt; color:#555; margin-top:2pt;">Direction des Affaires Générales</div>
        @isset($headerTitle)
            <div style="font-size:9.5pt; color:#1a5490; font-weight:bold; margin-top:3pt;">{{ $headerTitle }}</div>
        @endisset
    </div>
    <div style="display:table-cell; vertical-align:middle; width:70pt; text-align:right; font-size:7.5pt; color:#555;">
        Édité le<br>{{ now()->format('d/m/Y H:i') }}
    </div>
</div>
