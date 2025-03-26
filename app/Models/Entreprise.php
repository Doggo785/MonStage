<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    protected $table = 'Entreprise';
    protected $primaryKey = 'ID_Entreprise';
    public $timestamps = false;

    protected $fillable = [
        'Nom',
        'Telephone',
        'Email',
        'Site',
        'Description',
        'ID_Ville',
    ];

    // Relation avec le modèle Ville
    public function ville()
    {
        return $this->belongsTo(Ville::class, 'ID_Ville', 'ID_Ville');
    }
}
