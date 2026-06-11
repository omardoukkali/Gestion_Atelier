<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\StockPiece;

class TaskController extends Controller
{
    // 1. Liste de toutes les tâches
    public function index()
    {
        // 👈 On ajoute 'lignesDevis.piece' pour récupérer le devis ET les infos de la pièce associée
        $tasks = Task::with(['vehicle', 'assigne', 'lignesDevis.piece'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 📦 2. On récupère le catalogue de pièces (id, nom, prix et quantité dispo)
        $stockPieces = StockPiece::orderBy('designation', 'asc')
            ->get(['id', 'designation', 'prix_unitaire', 'quantite']);

        // 3. On envoie le tout à la vue Vue.js
        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'stockPieces' => $stockPieces // 👈 Le catalogue est maintenant dispo côté Front !
        ]);
    }

    // 2. Formulaire de création
    public function create()
    {
        $vehicles = Vehicle::all(['id', 'immatriculation', 'marque', 'modele']);

        // Sécurité/Logique : On ne prend que les utilisateurs "ouvrier"
        $mechanics = User::where('role', 'ouvrier')->get(['id', 'name']);

        return Inertia::render('Tasks/Create', [
            'vehicles' => $vehicles,
            'mechanics' => $mechanics
        ]);
    }

    // 3. Enregistrement en base de données
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'assigne_id' => 'required|exists:users,id',
            'type' => 'required|in:entretien,fabrication',
            'priorite' => 'required|in:basse,normale,haute,urgente',
            'description' => 'required|string|max:1000',
        ]);

        // Le statut est 'en_attente' par défaut d'après ta migration
        Task::create($validated);

        return redirect()->route('tasks.index')->with('message', 'Tâche assignée avec succès !');
    }

    // Démarrer une tâche (Ouvrier)
    public function start(Task $task)
    {
        $task->update(['statut' => 'en_cours']);
        return back()->with('message', 'Tâche démarrée ! Bon courage.');
    }

    // Terminer une tâche (Ouvrier)
    public function complete(Task $task)
    {
        $task->update(['statut' => 'terminee']);
        return back()->with('message', 'Félicitations, tâche terminée !');
    }


}
