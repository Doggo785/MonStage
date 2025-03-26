<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competence extends Model {
    protected $table = 'Competence';
    protected $primaryKey = 'ID_Competence';
    public $timestamps = false;

    public function offres() {
        return $this->belongsToMany(Offre::class, 'Offres_Competences', 'ID_Competence', 'ID_Offre');
    }
}
