<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model {
    protected $table = 'Utilisateur';
    protected $primaryKey = 'ID_User';
    public $timestamps = false;

    // Relation avec Etudiant
    public function etudiant() {
        return $this->hasOne(Etudiant::class, 'ID_User');
    }

    // Relation avec Role
    public function role() {
        return $this->belongsTo(Role::class, 'ID_Role');
    }
}
