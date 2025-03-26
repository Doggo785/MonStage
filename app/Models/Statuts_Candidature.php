<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $ID_Statut
 * @property string $Libelle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Statuts_Candidature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Statuts_Candidature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Statuts_Candidature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Statuts_Candidature whereIDStatut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Statuts_Candidature whereLibelle($value)
 * @mixin \Eloquent
 */
class Statuts_Candidature extends Model
{
    protected $table = 'Statuts_Candidature';
    protected $primaryKey = 'ID_Statut';
    public $timestamps = false;

    protected $fillable = [
        'Libelle',
    ];
}
