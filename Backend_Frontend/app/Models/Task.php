<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'statut',
        'priorite',
        'description',
        'compte_rendu',
        'vehicle_id',
        'assigne_id',
    ];

    // Relation: Une tâche appartient à un véhicule (BelongsTo)
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Relation: Une tâche est assignée à un utilisateur/ouvrier (BelongsTo)
    public function assigne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigne_id');
    }

    // Une tâche peut avoir plusieurs fiches de travail (ex: plusieurs jours d'intervention)
    public function fichesTravail()
    {
        return $this->hasMany(FicheTravail::class, 'tache_id');
    }

    // Les pièces demandées en devis pour cette tâche
    public function lignesDevis()
    {
        return $this->hasMany(LigneDevis::class, 'tache_id');
    }

    // Une tâche peut provenir d'une demande d'employé
    public function demande(): BelongsTo
    {
        return $this->belongsTo(Demande::class);
    }

}
