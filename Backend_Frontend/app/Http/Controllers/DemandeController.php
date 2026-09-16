<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Task;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DemandeController extends Controller
{
    // 1. Afficher les demandes
    public function index()
    {
        $user = Auth::user();
        $isChef = $user->role === 'chef_atelier';

        if ($isChef) {
            $demandes = Demande::with('employe')->latest()->get();
        } else {
            $demandes = Demande::where('employe_id', $user->id)->latest()->get();
        }

        return Inertia::render('Demandes/Index', [
            'demandes' => $demandes,
            // Envoyés seulement au chef, pour le formulaire d'acceptation
            'ouvriers' => $isChef
                ? User::where('role', 'ouvrier')->get(['id', 'name'])
                : [],
            'vehicules' => $isChef
                ? Vehicle::get(['id', 'immatriculation', 'marque', 'modele'])
                : [],
        ]);
    }

    public function create()
    {
        return inertia('Demandes/Create', [
            'vehicules' => Vehicle::get(['id', 'immatriculation', 'marque', 'modele']),
        ]);
    }

    // 2. Enregistrer une nouvelle demande (faite par un employé)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'        => 'required|in:entretien,fabrication',
            'description' => 'required|string|max:1000',
            // Obligatoire seulement si c'est un entretien, sinon facultatif
            'vehicle_id'  => 'required_if:type,entretien|nullable|exists:vehicles,id',
        ], [
            'vehicle_id.required_if' => 'Le véhicule est obligatoire pour une demande d\'entretien.',
        ]);

        Demande::create([
            'type'        => $validated['type'],
            'description' => $validated['description'],
            'vehicle_id'  => $validated['vehicle_id'] ?? null,
            'statut'      => 'en_attente',
            'employe_id'  => Auth::id(),
        ]);

        return redirect()->route('demandes.index')->with('message', 'Demande créée avec succès !');
    }

    // 3. Accepter une demande => créer une tâche affectée à un ouvrier
    public function accepter(Request $request, Demande $demande)
    {
        // Sécurité : on ne traite qu'une demande encore en attente
        if ($demande->statut !== 'en_attente') {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $validated = $request->validate([
            'assigne_id' => 'required|exists:users,id',
            'priorite'   => 'required|in:basse,normale,haute,urgente',
            'vehicle_id' => 'nullable|exists:vehicles,id',
        ], [
            'assigne_id.required' => 'Vous devez affecter la tâche à un ouvrier.',
            'priorite.required'   => 'Vous devez définir une priorité.',
        ]);

        DB::transaction(function () use ($demande, $validated) {
            // a) Création de la tâche à partir de la demande
            Task::create([
                'demande_id'  => $demande->id,
                'type'        => $demande->type,
                'description' => $demande->description,
                'statut'      => 'en_attente',
                'priorite'    => $validated['priorite'],
                'assigne_id'  => $validated['assigne_id'],
                'vehicle_id'  => $validated['vehicle_id'] ?? null,
            ]);

            // b) La demande passe à "acceptée"
            $demande->update(['statut' => 'acceptee']);
        });

        return back()->with('message', "Demande acceptée : la tâche a été créée et affectée à l'ouvrier.");
    }

    // Refuser une demande
    public function refuser(Demande $demande)
    {
        $demande->update(['statut' => 'refusee']);

        return back()->with('message', 'Demande refusée.');
    }
}
