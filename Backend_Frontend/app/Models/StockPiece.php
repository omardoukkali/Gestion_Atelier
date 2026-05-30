<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockPiece extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation',
        'quantite',
        'seuil_alerte',
        'prix_unitaire',
        'fournisseur_id',
    ];

    // Une pièce provient d'un fournisseur (BelongsTo)
    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function fichesTravail() {
        return $this->belongsToMany(FicheTravail::class, 'fiche_travail_stock_piece')
            ->withPivot('quantite')
            ->withTimestamps();
    }

}
