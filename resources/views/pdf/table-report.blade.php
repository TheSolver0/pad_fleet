<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Rapport' }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; }
        .header { text-align: center; margin-bottom: 16px; }
        .header h2, .header h3, .header p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px; vertical-align: top; }
        th { background: #f3f3f3; text-align: left; }
        .meta { margin-bottom: 10px; }
        .muted { color: #666; font-size: 11px; }
    </style>
</head>
<body>
    @include('pdf.partials.header', ['headerTitle' => $title ?? 'Rapport'])

    <div class="meta">
        <strong>Période :</strong>
        {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }}
        -
        {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
        <br>
        @if(isset($vehicle_category))
            <strong>Catégorie :</strong> {{ $vehicle_category !== '' ? $vehicle_category : 'Toutes' }}<br>
        @endif
        <span class="muted">Généré le {{ $generated_at->format('d/m/Y H:i') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headers as $h)
                    <th>{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($headers) }}">Aucune donnée.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

