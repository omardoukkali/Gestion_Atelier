<?php

namespace App\Http\Controllers;

use App\Models\Livraison;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LivraisonController extends Controller
{
    // Afficher la page dédiée aux livraisons (Historique + Commandes en attente)
    public function index()
    {
        // On récupère les commandes qui attendent d'être livrées
        $commandesEnAttente = Commande::with(['fournisseur', 'lignes.piece'])
            ->where('statut', 'en_attente')
            ->get();

        // On récupère l'historique des livraisons
        $livraisons = Livraison::with(['commande.fournisseur', 'ouvrier'])->latest()->get();

        return Inertia::render('Livraisons/Index', [
            'commandesEnAttente' => $commandesEnAttente,
            'livraisons' => $livraisons
        ]);
    }

    // Traiter la réception d'une livraison
    public function store(Request $request)
    {
        $validated = $request->validate([
            'commande_id' => 'required|exists:commandes,id',
            'statut' => 'required|in:conforme,non_conforme',
        ]);

        $commande = Commande::with('lignes.piece')->findOrFail($validated['commande_id']);

        DB::transaction(function () use ($validated, $commande) {
            // 1. Créer le bon de livraison
            Livraison::create([
                'commande_id' => $commande->id,
                'ouvrier_id' => Auth::id(), // L'ouvrier connecté
                'statut' => $validated['statut'],
                // date_reception est géré par useCurrent() !
            ]);

            // 2. Si la livraison est conforme, on met à jour le stock et la commande
            if ($validated['statut'] === 'conforme') {

                // Mettre à jour le statut de la commande
                $commande->update(['statut' => 'livree']);

                // Mettre à jour les quantités en stock
                foreach ($commande->lignes as $ligne) {
                    $piece = $ligne->piece;
                    $piece->update([
                        'quantite' => $piece->quantite + $ligne->quantite
                    ]);
                }
            }
            // Si non conforme, on annule la commande (selon ta logique métier)
            else {
                $commande->update(['statut' => 'annulee']);
            }
        });

        return redirect()->route('livraisons.index')->with('message', 'Livraison enregistrée avec succès !');
    }
}
