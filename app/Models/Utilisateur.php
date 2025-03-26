<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $ID_User
 * @property string $Password
 * @property string $Nom
 * @property string $Prenom
 * @property string|null $Telephone
 * @property string $Email
 * @property int $ID_Role
 * @property-read \App\Models\Etudiant|null $etudiant
 * @property-read \App\Models\Role $role
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur whereIDRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur whereIDUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur wherePrenom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utilisateur whereTelephone($value)
 * @mixin \Eloquent
 */
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
