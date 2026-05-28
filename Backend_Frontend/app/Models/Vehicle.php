<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    // Autoriser l'enregistrement en masse pour ces colonnes
    protected $fillable = [
        'immatriculation',
        'marque',
        'modele',
        'annee',
        'km_actuel',
    ];

    // Relation: Un véhicule a plusieurs tâches (HasMany)
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
