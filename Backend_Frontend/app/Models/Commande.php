<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    public function chef() { return $this->belongsTo(User::class, 'chef_id'); }
    public function fournisseur() { return $this->belongsTo(Fournisseur::class); }
    public function lignes() { return $this->hasMany(LigneCommande::class); }


}
