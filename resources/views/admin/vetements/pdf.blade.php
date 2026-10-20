<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 18px; margin-bottom: 2px; color: #28a745; }
        .sub { color: #777; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #28a745; color: #fff; text-align: left; padding: 6px; }
        td { border-bottom: 1px solid #ddd; padding: 5px 6px; }
        tr:nth-child(even) td { background: #f6f6f6; }
    </style>
</head>
<body>
    <h1>TextileCycle : liste des vêtements</h1>
    <div class="sub">
        Généré le {{ now()->format('d/m/Y à H:i') }}
        @if(request('categorie_id')) · Catégorie : {{ \App\Models\Categorie::find(request('categorie_id'))?->nom }} @endif
        @if(request('etat')) · État : {{ \App\Enums\EtatVetement::tryFrom(request('etat'))?->label() }} @endif
        · {{ $vetements->count() }} vêtement(s)
    </div>

    <table>
        <thead>
            <tr><th>Nom</th><th>Catégorie</th><th>Taille</th><th>État</th><th>Type</th><th>Ajouté le</th></tr>
        </thead>
        <tbody>
        @forelse($vetements as $v)
            <tr>
                <td>{{ $v->nom }}</td>
                <td>{{ $v->categorie?->nom }}</td>
                <td>{{ $v->taille }}</td>
                <td>{{ $v->etat->label() }}</td>
                <td>{{ $v->type->label() }}</td>
                <td>{{ $v->created_at->format('d/m/Y') }}</td>
            </tr>
        @empty
            <tr><td colspan="6">Aucun vêtement.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>