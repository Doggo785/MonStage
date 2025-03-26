<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statuts_Candidature extends Model
{
    protected $table = 'Statuts_Candidature';
    protected $primaryKey = 'ID_Statut';
    public $timestamps = false;

    protected $fillable = [
        'Libelle',
    ];
}
