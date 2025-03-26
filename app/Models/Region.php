<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $ID_Region
 * @property string|null $Nom
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereIDRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereNom($value)
 * @mixin \Eloquent
 */
class Region extends Model
{
    protected $table = 'Region';
    protected $primaryKey = 'ID_Region';
    public $timestamps = false;

    protected $fillable = [
        'Nom',
    ];
}
