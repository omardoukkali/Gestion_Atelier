<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\StockPiece;
use App\Models\LigneDevis;
use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\LigneCommande;
use Illuminate\Support\Facades\DB;
use App\Mail\BonDeCommandeMail;
use Illuminate\Support\Facades\Mail;


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

        $piece = $ligneDevis->piece;
        $commande = null;

        // On enveloppe tout dans une transaction : soit tout réussit, soit rien
        DB::transaction(function () use ($ligneDevis, $validated, $piece, &$commande) {
            // Envoi du Bon de Commande au fournisseur (Option A)
            $commande->load(['fournisseur', 'lignes.piece']);
            if ($commande->fournisseur->email) {
                Mail::to($commande->fournisseur->email)->send(new BonDeCommandeMail($commande));
            }

            // 2. Mettre à jour le statut ET la quantité de la ligne de devis
            $ligneDevis->update([
                'statut'   => 'validee',
                'quantite' => $validated['quantite'],
            ]);

            // 3. Créer le Bon de Commande — statut "en_attente" pour qu'il
            //    apparaisse dans la page Livraisons (réception à venir)
            $commande = Commande::create([
                'fournisseur_id' => $piece->fournisseur_id,
                'chef_id'        => auth()->id(),
                'statut'         => 'en_attente',
                'date_commande'  => now(),
            ]);

            // 4. Créer la LIGNE de commande (c'est elle qui réapprovisionnera le stock)
            LigneCommande::create([
                'commande_id'    => $commande->id,
                'stock_piece_id' => $piece->id,
                'quantite'       => $validated['quantite'],
                'prix_unitaire'  => $ligneDevis->prix_unitaire,
            ]);
        });

        // 5. Message de confirmation
        return back()->with('message', "Devis validé pour '{$piece->designation}' (Quantité : {$validated['quantite']}). Bon de Commande #{$commande->id} généré et envoyé à {$piece->fournisseur->nom}. En attente de livraison.");
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
