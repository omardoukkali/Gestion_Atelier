<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FournisseurController extends Controller
{
    public function index()
    {
        return Inertia::render('Fournisseurs/Index', [
            'fournisseurs' => Fournisseur::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('Fournisseurs/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:fournisseurs',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string',
        ]);

        Fournisseur::create($validated);

        return redirect()->route('fournisseurs.index')->with('message', 'Fournisseur ajouté !');
    }
}
