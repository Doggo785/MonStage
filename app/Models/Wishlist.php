<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $ID_User
 * @property int $ID_Offre
 * @property string $Date_ajout
 * @property-read \App\Models\Offre $offre
 * @property-read \App\Models\User $utilisateur
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishlist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishlist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishlist query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishlist whereDateAjout($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishlist whereIDOffre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wishlist whereIDUser($value)
 * @mixin \Eloquent
 */
class Wishlist extends Model
{
    protected $table = 'Wishlist';
    public $timestamps = false;

    protected $fillable = [
        'ID_User',
        'ID_Offre',
        'Date_ajout',
    ];

    // Relation avec le modèle Utilisateur
    public function user()
    {
        return $this->belongsTo(User::class, 'ID_User', 'ID_User');
    }

    // Relation avec le modèle Offre
    public function offre()
    {
        return $this->belongsTo(Offre::class, 'ID_Offre', 'ID_Offre');
    }
}
