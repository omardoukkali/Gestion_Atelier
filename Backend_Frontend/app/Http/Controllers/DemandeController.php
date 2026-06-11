<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DemandeController extends Controller
{
    // 1. Afficher les demandes
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'chef_atelier') {
            // Le chef voit toutes les demandes
            $demandes = Demande::with('employe')->latest()->get();
        } else {
            // L'employé ne voit que ses propres demandes
            $demandes = Demande::where('employe_id', $user->id)->latest()->get();
        }

        return Inertia::render('Demandes/Index', [
            'demandes' => $demandes
        ]);
    }


    public function create()
    {
        return inertia('Demandes/Create');
    }


    // 2. Enregistrer une nouvelle demande (faite par un employé)
    public function store(Request $request)
    {
        // On vérifie que les données envoyées sont correctes
        $validated = $request->validate([
            'type' => 'required|in:entretien,fabrication',
            'description' => 'required|string|max:1000',
        ]);

        // On crée la demande et on l'associe à l'employé connecté
        $demande = Demande::create([
            'type' => $validated['type'],
            'description' => $validated['description'],
            'statut' => 'en_attente',
            'employe_id' => Auth::id(), // Récupère l'ID de la personne connectée
        ]);

        // On redirige vers la liste des demandes plutôt que le dashboard
        return redirect()->route('demandes.index')->with('message', 'Demande créée avec succès !');
    }

    // Accepter une demande
    public function accepter(Demande $demande)
    {
        $demande->update(['statut' => 'acceptee']);

        // back() permet de recharger la page actuelle (le dashboard)
        return back()->with('message', 'Demande acceptée avec succès.');
    }

    // Refuser une demande
    public function refuser(Demande $demande)
    {
        $demande->update(['statut' => 'refusee']);

        return back()->with('message', 'Demande refusée.');
    }
}
