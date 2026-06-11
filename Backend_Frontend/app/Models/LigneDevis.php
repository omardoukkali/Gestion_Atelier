<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneDevis extends Model
{
    use HasFactory;

    protected $fillable = [
        'tache_id',
        'stock_piece_id',
        'quantite',
        'prix_unitaire',
        'statut',
    ];

    public function tache() { return $this->belongsTo(Task::class, 'tache_id'); }
    public function piece() { return $this->belongsTo(StockPiece::class, 'stock_piece_id'); }

}
