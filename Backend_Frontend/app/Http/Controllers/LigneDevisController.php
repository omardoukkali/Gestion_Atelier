<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\StockPiece;
use App\Models\LigneDevis;
use Illuminate\Http\Request;
use App\Models\Commande;


class LigneDevisController extends Controller
{
    /**
     * Ajoute une pièce au devis d'une tâche spécifique (Action Ouvrier)
     */
    public function store(Request $request, Task $task)
    {
        // 1. Validation des données envoyées par le formulaire Vue.js
        $validated = $request->validate([
            'stock_piece_id' => 'required|exists:stock_pieces,id',
            'quantite' => 'required|integer|min:1',
        ]);

        // 2. Récupérer la pièce dans le stock pour obtenir son prix réel
        $piece = StockPiece::findOrFail($validated['stock_piece_id']);

        // 3. Création de la ligne de devis
        LigneDevis::create([
            'tache_id' => $task->id,
            'stock_piece_id' => $piece->id,
            'quantite' => $validated['quantite'],
            'prix_unitaire' => $piece->prix_unitaire, // 🔒 On prend le prix de la base de données !
        ]);

        // 4. Redirection avec un message de succès
        return back()->with('message', 'Pièce ajoutée au devis avec succès !');
    }


    public function valider(Request $request, LigneDevis $ligneDevis)
    {
        // 1. On valide la quantité envoyée par le chef (sécurité)
        $validated = $request->validate([
            'quantite' => 'required|integer|min:1'
        ]);

        // 2. On met à jour le statut ET la quantité (qu'elle ait changé ou non !)
        $ligneDevis->update([
            'statut' => 'validee',
            'quantite' => $validated['quantite']
        ]);

        // 3. AUTOMATISATION : Transformation automatique en Bon de Commande
        $piece = $ligneDevis->piece;
        $commande = Commande::create([
            'fournisseur_id' => $piece->fournisseur_id,
            'chef_id'        => auth()->id(),
            'statut' => 'validee', // Marqué comme envoyé selon ton UML
            'date_commande' => now(),
        ]);

        // 4. Message flash de confirmation avec le récapitulatif
        return back()->with('message', "Le devis pour la pièce '{$piece->designation}' a été validé (Quantité fixée à : {$validated['quantite']}). Le Bon de Commande #{$commande->id} a été généré et envoyé à {$piece->fournisseur->nom}.");
    }

    /**
     * Refus du devis par le Chef
     */
    public function refuser(LigneDevis $ligneDevis)
    {
        $ligneDevis->update(['statut' => 'refusee']);

        return back()->with('message', 'La demande de matériel a été refusée.');
    }

}
