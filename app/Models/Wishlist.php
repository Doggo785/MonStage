<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $table = 'Wishlist';
    public $timestamps = false;

    protected $fillable = [
        'ID_User',
        'ID_Offre',
        'Date_ajout',
    ];

    // Relation avec le modèle Utilisateur
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'ID_User', 'ID_User');
    }

    // Relation avec le modèle Offre
    public function offre()
    {
        return $this->belongsTo(Offre::class, 'ID_Offre', 'ID_Offre');
    }
}
