<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livraison extends Model
{
    use HasFactory;


    protected $fillable = ['commande_id', 'ouvrier_id', 'date_reception', 'statut'];

    public function commande() {
        return $this->belongsTo(Commande::class);
    }

    public function ouvrier() {
        return $this->belongsTo(User::class, 'ouvrier_id');
    }

}
