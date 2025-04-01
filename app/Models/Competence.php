<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $ID_Competence
 * @property string $Libelle
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Offre> $offres
 * @property-read int|null $offres_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competence query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competence whereIDCompetence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competence whereLibelle($value)
 * @mixin \Eloquent
 */
class Competence extends Model
{
    use HasFactory;

    protected $table = 'Competence'; // Nom de la table des compétences
    protected $primaryKey = 'ID_Competence';

    // Relation avec les offres
    public function offres()
    {
        return $this->belongsToMany(
            Offre::class,             // Modèle cible
            'Offres_Competences',     // Nom de la table pivot
            'ID_Competence',          // Clé étrangère dans la table pivot pour la compétence
            'ID_Offre'                // Clé étrangère dans la table pivot pour l'offre
        );
    }
}
