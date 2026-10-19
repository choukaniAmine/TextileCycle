<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Devis {{ $devis->reference }} - {{ $devis->atelier->nom }}</title>
    <style>
        @page {
            margin: 28px 32px;
        }
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #2d3748;
            line-height: 1.45;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .brand-title {
            font-size: 22px;
            font-weight: bold;
            color: #198754;
            letter-spacing: -0.5px;
            margin: 0;
        }
        .brand-subtitle {
            font-size: 10px;
            color: #718096;
            margin-top: 2px;
        }
        .doc-title {
            text-align: right;
        }
        .doc-title h1 {
            font-size: 20px;
            color: #2b6cb0;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .badge-ref {
            display: inline-block;
            background-color: #ebf8ff;
            color: #2b6cb0;
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            margin-top: 4px;
        }
        .info-cards {
            margin-top: 22px;
            margin-bottom: 22px;
        }
        .info-card {
            width: 48%;
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 14px;
            vertical-align: top;
        }
        .card-header {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #4a5568;
            border-bottom: 2px solid #cbd5e0;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .items-table {
            width: 100%;
            margin-top: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        .items-table th {
            background-color: #2d3748;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9.5px;
            letter-spacing: 0.5px;
            padding: 9px 10px;
            text-align: left;
        }
        .items-table td {
            padding: 9px 10px;
            border-bottom: 1px solid #edf2f7;
            font-size: 10.5px;
        }
        .items-table tr:nth-child(even) {
            background-color: #f7fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-box {
            margin-top: 15px;
            float: right;
            width: 40%;
        }
        .total-table td {
            padding: 6px 10px;
        }
        .total-table .final-total {
            background-color: #198754;
            color: #ffffff;
            font-size: 13px;
            font-weight: bold;
            border-radius: 4px;
        }
        .eco-box {
            clear: both;
            margin-top: 25px;
            background-color: #f0fff4;
            border: 1px solid #9ae6b4;
            border-radius: 6px;
            padding: 12px 14px;
        }
        .eco-title {
            color: #22543d;
            font-weight: bold;
            font-size: 11.5px;
            margin-bottom: 6px;
        }
        .eco-grid td {
            width: 33.33%;
            padding: 4px 6px;
            vertical-align: top;
        }
        .eco-val {
            font-size: 13px;
            font-weight: bold;
            color: #276749;
        }
        .eco-desc {
            font-size: 9px;
            color: #4a5568;
        }
        .conditions {
            margin-top: 25px;
            font-size: 8.5px;
            color: #718096;
            line-height: 1.4;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        .signatures {
            margin-top: 30px;
        }
        .sig-box {
            width: 45%;
            height: 70px;
            border: 1px dashed #cbd5e0;
            border-radius: 4px;
            padding: 8px;
            font-size: 9px;
            color: #a0aec0;
        }
    </style>
</head>
<body>

    {{-- En-tête --}}
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="brand-title">♻ {{ config('app.name', 'ReFashion') }}</div>
                <div class="brand-subtitle">Plateforme d'Économie Circulaire &amp; Consommation Textile Responsable</div>
                <div style="margin-top: 6px; font-size: 9.5px; color: #4a5568;">
                    Partenaire officiel du réseau d'ateliers certifiés
                </div>
            </td>
            <td class="doc-title" style="width: 45%;">
                <h1>DEVIS ESTIMATIF</h1>
                <div class="badge-ref">{{ $devis->reference }}</div>
                <div style="margin-top: 6px; font-size: 9.5px; color: #718096;">
                    Date d'émission : <strong>{{ $devis->created_at->format('d/m/Y') }}</strong><br>
                    Validité : <strong>30 jours</strong> (jusqu'au {{ $devis->created_at->addDays(30)->format('d/m/Y') }})
                </div>
            </td>
        </tr>
    </table>

    {{-- Cartes Infos Atelier et Client --}}
    <table class="info-cards">
        <tr>
            <td class="info-card">
                <div class="card-header">Atelier Prestataire</div>
                <strong style="font-size: 12px; color: #1a202c;">{{ $devis->atelier->nom }}</strong><br>
                {{ $devis->atelier->adresse }}<br>
                {{ $devis->atelier->code_postal }} {{ $devis->atelier->ville }}<br>
                <strong>Tél :</strong> {{ $devis->atelier->telephone ?? 'Non renseigné' }}<br>
                <strong>E-mail :</strong> {{ $devis->atelier->email ?? 'contact@atelier.test' }}<br>
                @if($devis->atelier->horaires)
                    <span style="font-size: 9px; color: #718096;">Horaires : {{ $devis->atelier->horaires }}</span>
                @endif
            </td>
            <td style="width: 4%;"></td>
            <td class="info-card">
                <div class="card-header">Client Destinataire</div>
                <strong style="font-size: 12px; color: #1a202c;">{{ $devis->nom_client }}</strong><br>
                <strong>E-mail :</strong> {{ $devis->email_client }}<br>
                <strong>Téléphone :</strong> {{ $devis->telephone_client ?? 'Non renseigné' }}<br>
                <div style="margin-top: 8px; font-size: 9.5px; color: #4a5568;">
                    Délai estimé : <strong>~{{ $devis->duree_estimee_jours }} jours ouvrés</strong><br>
                    Statut : <span style="color: #276749; font-weight: bold;">Validé / En attente de dépôt</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Tableau des prestations --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 45%;">Désignation de la prestation</th>
                <th style="width: 20%;">Catégorie</th>
                <th style="width: 15%; text-align: right;">Prix Unitaire</th>
                <th style="width: 5%; text-align: center;">Qté</th>
                <th style="width: 15%; text-align: right;">Sous-total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($devis->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->nom_service }}</strong>
                    </td>
                    <td>{{ $item->type_service ?? 'Retouche / Réparation' }}</td>
                    <td class="text-right">{{ number_format($item->prix_unitaire, 2, ',', ' ') }} TND</td>
                    <td class="text-center">{{ $item->quantite }}</td>
                    <td class="text-right"><strong>{{ number_format($item->sous_total, 2, ',', ' ') }} TND</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Total --}}
    <div class="total-box">
        <table class="total-table">
            <tr>
                <td class="text-right" style="color: #718096;">Total HT estimé :</td>
                <td class="text-right"><strong>{{ number_format($devis->montant_total, 2, ',', ' ') }} TND</strong></td>
            </tr>
            <tr>
                <td class="text-right" style="color: #718096;">TVA (Régime circulaire) :</td>
                <td class="text-right"><strong>0,00 TND</strong></td>
            </tr>
            <tr>
                <td class="text-right final-total">TOTAL ESTIMÉ :</td>
                <td class="text-right final-total">{{ number_format($devis->montant_total, 2, ',', ' ') }} TND</td>
            </tr>
        </table>
    </div>

    {{-- Bloc Impact Écologique ReFashion --}}
    <div class="eco-box">
        <div class="eco-title">🌱 Bilan Écologique ReFashion (Impact Positif Estimé)</div>
        <table class="eco-grid">
            <tr>
                <td>
                    <div class="eco-val">≈ {{ $devis->co2_economise }} kg</div>
                    <div class="eco-desc">d'émissions de CO₂ évitées</div>
                </td>
                <td>
                    <div class="eco-val">≈ {{ number_format($devis->eau_economisee, 0, ',', ' ') }} Litres</div>
                    <div class="eco-desc">d'eau préservée</div>
                </td>
                <td>
                    <div class="eco-val">{{ $devis->items->sum('quantite') }} pièce(s)</div>
                    <div class="eco-desc">textile sauvées du rebut</div>
                </td>
            </tr>
        </table>
    </div>

    @if($devis->remarques)
        <div style="margin-top: 15px; padding: 8px 12px; background: #fffaf0; border-left: 3px solid #dd6b20; font-size: 9.5px;">
            <strong>Remarques du client :</strong> {{ $devis->remarques }}
        </div>
    @endif

    {{-- Signatures --}}
    <table class="signatures">
        <tr>
            <td class="sig-box">
                Cachet et signature de l'Atelier :
            </td>
            <td style="width: 10%;"></td>
            <td class="sig-box">
                Bon pour accord et signature du Client :
            </td>
        </tr>
    </table>

    {{-- Conditions légales --}}
    <div class="conditions">
        <strong>Conditions Générales &amp; Mentions :</strong> Ce devis est une estimation tarifaire calculée sur la base des prestations standards sélectionnées. L'atelier se réserve le droit de réajuster le coût ou le délai après analyse physique des tissus (matières complexes, soies, doublures spécifiques ou fermetures atypiques). En confiant vos textiles aux ateliers ReFashion, vous participez activement à la prolongation de durée de vie des vêtements et à l'économie locale.
    </div>

</body>
</html>
