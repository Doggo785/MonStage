<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
class Etudiant extends Model
{
    use HasFactory;

    protected $table = 'Etudiant'; // Nom de la table
    protected $primaryKey = 'ID_User'; // Clé primaire
    public $timestamps = false; // Désactiver les timestamps automatiques

    protected $fillable = [
        'ID_User',
        'Statut_recherche',
    ];
}
