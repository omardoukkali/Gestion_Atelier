<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\StockPiece;
use App\Models\Fournisseur;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Création des Utilisateurs par Rôle
        User::create([
            'name' => 'Omar Chef',
            'email' => 'chef@atelier.com',
            'password' => Hash::make('password'),
            'role' => 'chef_atelier',
        ]);

        User::create([
            'name' => 'Jean Ouvrier',
            'email' => 'ouvrier@atelier.com',
            'password' => Hash::make('password'),
            'role' => 'ouvrier',
        ]);

        User::create([
            'name' => 'Alice Employe',
            'email' => 'employe@atelier.com',
            'password' => Hash::make('password'),
            'role' => 'employe',
        ]);

        // 2. Création de quelques Véhicules
        Vehicle::create([
            'immatriculation' => '1234-A-44',
            'marque' => 'Toyota',
            'modele' => 'Hilux',
            'annee' => 2022,
            'km_actuel' => 45000,
        ]);

        Vehicle::create([
            'immatriculation' => '5678-B-44',
            'marque' => 'Renault',
            'modele' => 'Master',
            'annee' => 2021,
            'km_actuel' => 89200,
        ]);

        // 3. Création d'un Fournisseur et de Stock
        $fournisseur = Fournisseur::create([
            'nom' => 'AutoPart Sarl',
            'email' => 'contact@autopart.com',
            'telephone' => '0522000000',
            'adresse' => 'Zone Industrielle, Casablanca',
        ]);

        StockPiece::create([
            'designation' => 'Filtre à huile',
            'quantite' => 20,
            'seuil_alerte' => 5,
            'prix_unitaire' => 150.00,
            'fournisseur_id' => $fournisseur->id,
        ]);

        StockPiece::create([
            'designation' => 'Plaquettes de frein',
            'quantite' => 3, // <--- C'est sous le seuil d'alerte pour tester !
            'seuil_alerte' => 5,
            'prix_unitaire' => 450.00,
            'fournisseur_id' => $fournisseur->id,
        ]);
    }
}
