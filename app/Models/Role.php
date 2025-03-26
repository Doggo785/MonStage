<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {
    protected $table = 'Role';
    protected $primaryKey = 'ID_Role';
    public $timestamps = false;

    public function utilisateurs() {
        return $this->hasMany(Utilisateur::class, 'ID_Role');
    }
}
