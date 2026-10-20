<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Étiquettes QR</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .barre { margin-bottom: 20px; }
        .grille { display: flex; flex-wrap: wrap; gap: 10px; }
        .etiquette { width: 230px; border: 1px dashed #888; padding: 10px; text-align: center; page-break-inside: avoid; }
        .etiquette svg { width: 130px; height: 130px; }
        .marque { font-size: 11px; color: #28a745; font-weight: bold; letter-spacing: 1px; }
        .nom { font-size: 14px; font-weight: bold; margin: 4px 0 2px; }
        .info { font-size: 11px; color: #555; }
        @media print { .barre { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="barre">
        <button onclick="window.print()">🖨️ Imprimer</button>
        <a href="{{ route('admin.vetements.index') }}">← Retour</a>
        <span>({{ $vetements->count() }} étiquette(s))</span>
    </div>

    <div class="grille">
        @foreach($vetements as $v)
            <div class="etiquette">
                <div class="marque">TEXTILECYCLE</div>
                <img src="{{ \App\Support\Qr::svg(route('vetements.show', $v), 130) }}" width="130" height="130" alt="QR {{ $v->nom }}">
                <div class="nom">{{ $v->nom }}</div>
                <div class="info">{{ $v->categorie?->nom }} · Taille {{ $v->taille }} · {{ $v->etat->label() }}</div>
                <div class="info">#{{ $v->id }}</div>
            </div>
        @endforeach
    </div>
</body>
</html>