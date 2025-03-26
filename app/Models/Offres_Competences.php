<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offres_Competences extends Model
{
    protected $table = 'Offres_Competences';
    public $timestamps = false;

    protected $fillable = [
        'ID_Offre',
        'ID_Competence',
    ];

    // Relation avec le modèle Offre
    public function offre()
    {
        return $this->belongsTo(Offre::class, 'ID_Offre', 'ID_Offre');
    }

    // Relation avec le modèle Competence
    public function competence()
    {
        return $this->belongsTo(Competence::class, 'ID_Competence', 'ID_Competence');
    }
}
