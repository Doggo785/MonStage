<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $ID_Secteur
 * @property string $Nom
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secteur newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secteur newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secteur query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secteur whereIDSecteur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Secteur whereNom($value)
 * @mixin \Eloquent
 */
class Secteur extends Model {
    protected $table = 'Secteur';
    protected $primaryKey = 'ID_Secteur';
    public $timestamps = false;
}
