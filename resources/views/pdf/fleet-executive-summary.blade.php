<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Synthese executive flotte</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; }
        .header { text-align: center; margin-bottom: 16px; }
        .header h2, .header h3, .header p { margin: 2px 0; }
        .meta { margin: 10px 0 14px; }
        .kpi { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .kpi td { border: 1px solid #ddd; padding: 8px; width: 25%; }
        .kpi .label { color: #666; font-size: 11px; }
        .kpi .value { font-size: 16px; font-weight: bold; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f3f3f3; text-align: left; }
        h4 { margin: 12px 0 6px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PORT AUTONOME DE DOUALA</h2>
        <h3>DIRECTION DES AFFAIRES GENERALES</h3>
        <p>Synthese executive flotte</p>
    </div>

    <div class="meta">
        <strong>Periode :</strong> {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}<br>
        <strong>Categorie :</strong> {{ $vehicle_category !== '' ? $vehicle_category : 'Toutes' }}<br>
        <strong>Genere le :</strong> {{ $generated_at->format('d/m/Y H:i') }}
    </div>

    <table class="kpi">
        <tr>
            <td><div class="label">Missions</div><div class="value">{{ $stats['missions_count'] }}</div></td>
            <td><div class="label">Distance totale (km)</div><div class="value">{{ number_format($stats['missions_distance'], 0, ',', ' ') }}</div></td>
            <td><div class="label">Reparations</div><div class="value">{{ $stats['repairs_count'] }}</div></td>
            <td><div class="label">Cout reparations (F)</div><div class="value">{{ number_format($stats['repairs_cost'], 0, ',', ' ') }}</div></td>
        </tr>
        <tr>
            <td><div class="label">Sinistres</div><div class="value">{{ $stats['sinistres_count'] }}</div></td>
            <td><div class="label">Cout sinistres estime (F)</div><div class="value">{{ number_format($stats['sinistres_estimated_cost'], 0, ',', ' ') }}</div></td>
            <td><div class="label">Consommation pieces (qte)</div><div class="value">{{ number_format($stats['parts_quantity'], 0, ',', ' ') }}</div></td>
            <td><div class="label">Consommation pieces (F)</div><div class="value">{{ number_format($stats['parts_cost'], 0, ',', ' ') }}</div></td>
        </tr>
    </table>

    <h4>Top pieces consommees</h4>
    <table>
        <thead>
            <tr>
                <th>Piece</th>
                <th>Reference</th>
                <th>Quantite</th>
                <th>Cout total (F)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($top_parts as $part)
                <tr>
                    <td>{{ $part['name'] }}</td>
                    <td>{{ $part['reference'] }}</td>
                    <td>{{ $part['total_quantity'] }}</td>
                    <td>{{ number_format($part['total_cost'], 0, ',', ' ') }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Aucune donnee.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

