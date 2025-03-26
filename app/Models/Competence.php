<?php

namespace App\Models;

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
class Competence extends Model {
    protected $table = 'Competence';
    protected $primaryKey = 'ID_Competence';
    public $timestamps = false;

    public function offres() {
        return $this->belongsToMany(Offre::class, 'Offres_Competences', 'ID_Competence', 'ID_Offre');
    }
}
