<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $ID_Ville
 * @property string|null $CP
 * @property string|null $Nom
 * @property int $ID_Region
 * @property-read \App\Models\Region $region
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ville newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ville newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ville query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ville whereCP($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ville whereIDRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ville whereIDVille($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ville whereNom($value)
 * @mixin \Eloquent
 */
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
