<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Vehicle;
use App\Models\Demande;
use App\Models\Commande;
use App\Models\FicheTravail;
use App\Models\StockPiece;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // --- Cartes KPI (haut de page) ---
        $stats = [
            'vehicules'         => Vehicle::count(),
            'taches_en_cours'   => Task::where('statut', 'en_cours')->count(),
            'taches_en_attente' => Task::where('statut', 'en_attente')->count(),
            'fiches_du_jour'    => FicheTravail::whereDate('date_debut', today())->count(),
            'demandes_attente'  => Demande::where('statut', 'en_attente')->count(),
            'commandes_attente' => Commande::where('statut', 'en_attente')->count(),
            'stock_alerte'      => StockPiece::whereColumn('quantite', '<=', 'seuil_alerte')->count(),
        ];

        // --- Rapport 1 : répartition des tâches par statut (pour le graphe) ---
        $tachesParStatut = Task::select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut');

        // --- Rapport 2 : pièces sous le seuil d'alerte (liste actionnable) ---
        $piecesEnAlerte = StockPiece::whereColumn('quantite', '<=', 'seuil_alerte')
            ->with('fournisseur:id,nom')
            ->orderBy('quantite')
            ->get(['id', 'designation', 'quantite', 'seuil_alerte', 'fournisseur_id']);

        // --- Rapport 3 : valeur totale du stock ---
        $valeurStock = StockPiece::select(DB::raw('SUM(quantite * prix_unitaire) as total'))
            ->value('total') ?? 0;

        // --- Rapport 4 : dernières demandes ---
        $dernieresDemandes = Demande::with('employe:id,name')
            ->latest()->take(5)->get();

        // --- Rapport 5 : charge de travail par ouvrier (tâches non terminées) ---
        $chargeOuvriers = Task::select('assigne_id', DB::raw('count(*) as total'))
            ->where('statut', '!=', 'terminee')
            ->whereNotNull('assigne_id')
            ->with('assigne:id,name')
            ->groupBy('assigne_id')
            ->get()
            ->map(fn ($t) => [
                'ouvrier' => $t->assigne?->name ?? 'N/A',
                'total'   => (int) $t->total,
            ]);

        return Inertia::render('Dashboard', [
            'stats'             => $stats,
            'tachesParStatut'   => $tachesParStatut,
            'piecesEnAlerte'    => $piecesEnAlerte,
            'valeurStock'       => $valeurStock,
            'dernieresDemandes' => $dernieresDemandes,
            'chargeOuvriers'    => $chargeOuvriers,
        ]);
    }
}
