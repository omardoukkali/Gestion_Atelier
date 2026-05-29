<?php

namespace App\Http\Controllers;

use App\Models\StockPiece;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockPieceController extends Controller
{
    public function index()
    {
        // On récupère les pièces avec les infos du fournisseur associé
        $pieces = StockPiece::with('fournisseur')->latest()->get();

        return Inertia::render('StockPieces/Index', [
            'pieces' => $pieces
        ]);
    }

    public function create()
    {
        // On a besoin de la liste des fournisseurs pour le menu déroulant du formulaire
        return Inertia::render('StockPieces/Create', [
            'fournisseurs' => Fournisseur::all(['id', 'nom'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
            'quantite' => 'required|integer|min:0',
            'seuil_alerte' => 'required|integer|min:0',
            'prix_unitaire' => 'required|numeric|min:0',
            'fournisseur_id' => 'required|exists:fournisseurs,id',
        ]);

        StockPiece::create($validated);

        return redirect()->route('stock-pieces.index')->with('message', 'Pièce ajoutée au stock !');
    }
}
