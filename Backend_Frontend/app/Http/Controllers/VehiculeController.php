<?php

namespace App\Http\Controllers;

use App\Models\Vehicle; // 1. ON IMPORTE LE MODÈLE EN ANGLAIS
use Illuminate\Http\Request;
use Inertia\Inertia;

class VehiculeController extends Controller
{
    // Afficher la liste des véhicules
    public function index()
    {
        // 2. ON UTILISE LE MODÈLE 'Vehicle'
        $vehicules = Vehicle::latest()->get();

        return Inertia::render('Vehicules/Index', [
            'vehicules' => $vehicules
        ]);
    }

    // Afficher le formulaire de création
    public function create()
    {
        return Inertia::render('Vehicules/Create');
    }

    // Sauvegarder un nouveau véhicule
    public function store(Request $request)
    {
        $validated = $request->validate([
            'immatriculation' => 'required|string|max:20|unique:vehicles',
            'marque' => 'required|string|max:100',
            'modele' => 'required|string|max:100',
            'annee' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'km_actuel' => 'required|integer|min:0',
        ]);

        Vehicle::create($validated);

        return redirect()->route('vehicules.index')->with('message', 'Véhicule ajouté avec succès !');
    }
}
