<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Fournisseur;
use App\Models\StockPiece;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CommandeController extends Controller
{
    // Afficher la liste des commandes
    public function index()
    {
        // On charge la commande avec son fournisseur, le chef, et toutes ses lignes (avec les pièces)
        $commandes = Commande::with(['fournisseur', 'chef', 'lignes.piece'])->latest()->get();

        return Inertia::render('Commandes/Index', [
            'commandes' => $commandes
        ]);
    }

    // Afficher le formulaire de création
    public function create()
    {
        // Le formulaire a besoin de la liste des fournisseurs et des pièces disponibles
        return Inertia::render('Commandes/Create', [
            'fournisseurs' => Fournisseur::all(['id', 'nom']),
            'pieces' => StockPiece::all(['id', 'designation', 'prix_unitaire'])
        ]);
    }

    // Sauvegarder la commande ET ses lignes
    public function store(Request $request)
    {
        // 1. Validation des données envoyées par Vue.js
        $validated = $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'lignes' => 'required|array|min:1', // Il faut au moins une pièce commandée
            'lignes.*.stock_piece_id' => 'required|exists:stock_pieces,id',
            'lignes.*.quantite' => 'required|integer|min:1',
        ]);

        // 2. Transaction DB : On s'assure que tout s'enregistre, ou rien du tout
        DB::transaction(function () use ($validated) {

            // A. Création de l'en-tête de la commande
            $commande = Commande::create([
                'chef_id' => Auth::id(), // L'utilisateur connecté est le chef
                'fournisseur_id' => $validated['fournisseur_id'],
                'statut' => 'en_attente',
                // date_commande est géré automatiquement par ton useCurrent() dans la migration !
            ]);

            // B. Création des lignes de commande
            foreach ($validated['lignes'] as $ligne) {
                // On va chercher la pièce en DB pour récupérer son prix unitaire officiel
                $piece = StockPiece::find($ligne['stock_piece_id']);

                $commande->lignes()->create([
                    'stock_piece_id' => $piece->id,
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire' => $piece->prix_unitaire,
                ]);
            }
        });

        return redirect()->route('commandes.index')->with('message', 'Commande créée avec succès !');
    }
}
