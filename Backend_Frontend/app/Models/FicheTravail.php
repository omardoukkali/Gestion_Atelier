<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FicheTravail extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_debut',
        'date_fin',
        'description',
        'tache_id',
        'ouvrier_id',
    ];

    // Une fiche appartient à une tâche
    public function tache(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'tache_id');
    }

    // Une fiche appartient à un ouvrier (User)
    public function ouvrier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ouvrier_id');
    }
}
