<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Demande extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'description',
        'statut',
        'employe_id',
    ];

    // Une demande appartient à un employé (User)
    public function employe(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employe_id');
    }
}
