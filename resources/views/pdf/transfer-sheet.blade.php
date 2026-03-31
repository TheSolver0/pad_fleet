<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fiche de transfert</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color:#111; }
        .header { text-align:center; margin-bottom:14px; }
        .header h2,.header h3,.header p { margin:2px 0; }
        .box { border:1px solid #ddd; padding:10px; margin-bottom:10px; }
        table { width:100%; border-collapse:collapse; }
        th,td { border:1px solid #ddd; padding:6px; }
        th { background:#f3f3f3; text-align:left; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PORT AUTONOME DE DOUALA</h2>
        <h3>DIRECTION DES AFFAIRES GENERALES — DGP</h3>
        <p>Fiche de transfert (atelier / garage)</p>
    </div>

    <div class="box">
        <strong>Bon de travail :</strong> {{ $workOrder->reference }}<br>
        <strong>Réf transfert :</strong> {{ $workOrder->transfer_reference ?? '—' }}<br>
        <strong>Date transfert :</strong> {{ $workOrder->transfer_date?->format('d/m/Y') ?? '—' }}<br>
    </div>

    <div class="box">
        <strong>Véhicule :</strong> {{ $workOrder->vehicle?->registration ?? '—' }}<br>
        <strong>Mécanicien :</strong> {{ $workOrder->mechanic?->last_name }} {{ $workOrder->mechanic?->first_name }}<br>
        <strong>Description :</strong><br>
        {!! nl2br(e($workOrder->work_description)) !!}
    </div>

    <h4>Pièces prévues / consommées</h4>
    <table>
        <thead>
            <tr>
                <th>Article</th>
                <th>Qté</th>
                <th>Magasin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($workOrder->parts as $p)
                <tr>
                    <td>{{ $p->article?->name ?? '—' }}</td>
                    <td>{{ $p->quantity }}</td>
                    <td>{{ $p->stock_location === 'main' ? 'Magasin principal' : 'Magasin garage' }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Aucune pièce.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
