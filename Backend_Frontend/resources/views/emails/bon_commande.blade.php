<h2>Bon de Commande #{{ $commande->id }}</h2>

<p>Bonjour {{ $commande->fournisseur->nom }},</p>

<p>Veuillez trouver ci-joint notre bon de commande n°{{ $commande->id }}
    du {{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') }}.</p>

<p>Merci de nous confirmer la disponibilité et le délai de livraison.</p>

<p>Cordialement,<br>L'Atelier</p>

