<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ville extends Model
{
    protected $table = 'Ville';
    protected $primaryKey = 'ID_Ville';
    public $timestamps = false;

    protected $fillable = [
        'CP',
        'Nom',
        'ID_Region',
    ];

    // Relation avec le modèle Region
    public function region()
    {
        return $this->belongsTo(Region::class, 'ID_Region', 'ID_Region');
    }
}
