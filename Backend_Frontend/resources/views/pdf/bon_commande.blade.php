<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; }
        .total { text-align: right; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
<h1>Bon de Commande #{{ $commande->id }}</h1>
<p>
    <strong>Date :</strong> {{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') }}<br>
    <strong>Fournisseur :</strong> {{ $commande->fournisseur->nom }}<br>
    <strong>Email :</strong> {{ $commande->fournisseur->email }}
</p>

<table>
    <thead>
    <tr>
        <th>Pièce</th><th>Quantité</th><th>Prix unitaire</th><th>Total</th>
    </tr>
    </thead>
    <tbody>
    @php $totalGeneral = 0; @endphp
    @foreach ($commande->lignes as $ligne)
        @php $sousTotal = $ligne->quantite * $ligne->prix_unitaire; $totalGeneral += $sousTotal; @endphp
        <tr>
            <td>{{ $ligne->piece->designation }}</td>
            <td>{{ $ligne->quantite }}</td>
            <td>{{ number_format($ligne->prix_unitaire, 2, ',', ' ') }} €</td>
            <td>{{ number_format($sousTotal, 2, ',', ' ') }} €</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p class="total">Total : {{ number_format($totalGeneral, 2, ',', ' ') }} €</p>
</body>
</html>
