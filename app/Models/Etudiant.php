<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etudiant extends Model {
    protected $table = 'Etudiant';
    protected $primaryKey = 'ID_User';
    public $timestamps = false;

    public function utilisateur() {
        return $this->belongsTo(Utilisateur::class, 'ID_User');
    }
}
