<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offre extends Model
{
    protected $table = 'Offre';
    protected $primaryKey = 'ID_Offre';
    public $timestamps = false;

    protected $fillable = [
        'Titre',
        'Description',
        'Remuneration',
        'Etat',
        'Date_publication',
        'Date_expiration',
        'ID_Secteur',
        'ID_Ville',
        'ID_Entreprise',
    ];

    // Relations avec les autres modèles
    public function secteur()
    {
        return $this->belongsTo(Secteur::class, 'ID_Secteur', 'ID_Secteur');
    }

    public function ville()
    {
        return $this->belongsTo(Ville::class, 'ID_Ville', 'ID_Ville');
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'ID_Entreprise', 'ID_Entreprise');
    }
}
