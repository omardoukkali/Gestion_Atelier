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


    public function valider(LigneDevis $ligneDevis)
    {
        // 1. On passe la ligne de devis en "validée"
        $ligneDevis->update(['statut' => 'validee']);

        // 2. On récupère la pièce pour connaître son fournisseur
        $piece = $ligneDevis->piece;

        // 3. AUTOMATISATION : Transformation du devis en Bon de Commande
        // (On crée la commande pour le fournisseur de la pièce)
        $commande = Commande::create([
            'fournisseur_id' => $piece->fournisseur_id,
            'statut' => 'envoyee', // Directement marquée comme envoyée selon ton diagramme
            'date_commande' => now(),
            // Ajoute ici les autres champs obligatoires de ta table commandes si tu en as (ex: total, ref...)
        ]);

        // 4. [Optionnel] Si tu as une table pivot entre Commande et StockPiece, tu enregistres la ligne ici.

        // 5. Confirmation au Chef d'Atelier
        return back()->with('message', "Devis validé ! Le Bon de Commande #{$commande->id} a été généré et envoyé à {$piece->fournisseur->nom}.");
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
