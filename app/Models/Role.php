<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $ID_Role
 * @property string $Libelle
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Utilisateur> $utilisateurs
 * @property-read int|null $utilisateurs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereIDRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereLibelle($value)
 * @mixin \Eloquent
 */
class Role extends Model {
    protected $table = 'Role';
    protected $primaryKey = 'ID_Role';
    public $timestamps = false;

    public function utilisateurs() {
        return $this->hasMany(Utilisateur::class, 'ID_Role');
    }
}
