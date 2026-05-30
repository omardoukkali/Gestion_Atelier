<?php

namespace App\Http\Controllers;

use App\Models\FicheTravail;
use App\Models\Task;
use App\Models\StockPiece;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FicheTravailController extends Controller
{
    public function create()
    {
        // On récupère les tâches qui ne sont pas encore terminées
        $tasks = Task::where('statut', '!=', 'terminee')->get();

        // On récupère uniquement les pièces qui sont en stock (quantité > 0)
        $pieces = StockPiece::where('quantite', '>', 0)->get();

        return Inertia::render('FichesTravail/Create', [
            'tasks' => $tasks,
            'pieces' => $pieces
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tache_id' => 'required|exists:tasks,id',
            'date_debut' => 'required|date',
            'description' => 'required|string',
            'pieces' => 'nullable|array',
            'pieces.*.stock_piece_id' => 'required|exists:stock_pieces,id',
            'pieces.*.quantite' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            // 1. Création de la fiche de travail
            $fiche = FicheTravail::create([
                'tache_id' => $validated['tache_id'],
                'ouvrier_id' => Auth::id(),
                'date_debut' => $validated['date_debut'],
                'description' => $validated['description'],
            ]);

            // 2. Si des pièces ont été utilisées
            if (!empty($validated['pieces'])) {
                foreach ($validated['pieces'] as $pieceData) {

                    // A. On lie la pièce à la fiche (ça remplit la table pivot)
                    $fiche->pieces()->attach($pieceData['stock_piece_id'], [
                        'quantite' => $pieceData['quantite']
                    ]);

                    // B. On déduit la quantité du stock !
                    $stockPiece = StockPiece::find($pieceData['stock_piece_id']);
                    $stockPiece->decrement('quantite', $pieceData['quantite']);
                }
            }

            // 3. On passe le statut de la tâche à "en_cours" si elle était "a_faire"
            $task = Task::find($validated['tache_id']);
            if ($task->statut === 'a_faire') {
                $task->update(['statut' => 'en_cours']);
            }
        });

        // Pour le test, on redirige vers l'accueil ou le stock pour voir la différence
        return redirect()->route('stock-pieces.index')->with('message', 'Fiche de travail créée et stock mis à jour !');
    }
}
