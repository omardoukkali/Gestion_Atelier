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
    
}
