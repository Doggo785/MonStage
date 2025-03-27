<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $ID_User
 * @property string|null $Statut_recherche
 * @property-read \App\Models\User $utilisateur
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etudiant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etudiant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etudiant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etudiant whereIDUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Etudiant whereStatutRecherche($value)
 * @mixin \Eloquent
 */
class Etudiant extends Model {
    protected $table = 'Etudiant';
    protected $primaryKey = 'ID_User';
    public $timestamps = false;

    public function user() {
        return $this->belongsTo(User::class, 'ID_User');
    }
}
