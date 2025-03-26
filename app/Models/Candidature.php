<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidature extends Model {
    protected $table = 'Candidature';
    public $timestamps = false;
    public $incrementing = false; // Désactive l'auto-incrément

    // Relations
    public function utilisateur() {
        return $this->belongsTo(Utilisateur::class, 'ID_User');
    }

    public function offre() {
        return $this->belongsTo(Offre::class, 'ID_Offre');
    }

    public function statut() {
        return $this->belongsTo(Statuts_Candidature::class, 'ID_Statut');
    }
}
