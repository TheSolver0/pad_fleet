<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport consommation véhicules</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; }
        .header { text-align: center; margin-bottom: 16px; }
        .header h2, .header h3, .header p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f3f3f3; text-align: left; }
        .meta { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PORT AUTONOME DE DOUALA</h2>
        <h3>DIRECTION DES AFFAIRES GENERALES</h3>
        <p>Rapport de consommation des vehicules</p>
    </div>

    <div class="meta">
        <strong>Periode :</strong> {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}<br>
        <strong>Categorie :</strong> {{ $vehicle_category !== '' ? $vehicle_category : 'Toutes' }}<br>
        <strong>Genere le :</strong> {{ $generated_at->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Piece</th>
                <th>Reference</th>
                <th>Quantite</th>
                <th>Cout total (F)</th>
                <th>Utilisations</th>
            </tr>
        </thead>
        <tbody>
            @forelse($parts as $part)
                <tr>
                    <td>{{ $part['name'] }}</td>
                    <td>{{ $part['reference'] }}</td>
                    <td>{{ $part['total_quantity'] }}</td>
                    <td>{{ number_format($part['total_cost'], 0, ',', ' ') }}</td>
                    <td>{{ $part['usage_count'] }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Aucune donnee.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
