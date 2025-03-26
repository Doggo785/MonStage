<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    protected $table = 'Avis';
    protected $primaryKey = 'ID_Avis';
    public $timestamps = false;

    protected $fillable = [
        'Note',
        'ID_Entreprise',
        'ID_User',
    ];

    // Relation avec le modèle Entreprise
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'ID_Entreprise', 'ID_Entreprise');
    }

    // Relation avec le modèle Utilisateur
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'ID_User', 'ID_User');
    }
}
