<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneCommande extends Model
{
    use HasFactory;

    public function commande() { return $this->belongsTo(Commande::class); }
    public function piece() { return $this->belongsTo(StockPiece::class, 'stock_piece_id'); }

}
