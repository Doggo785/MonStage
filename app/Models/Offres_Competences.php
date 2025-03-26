<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $ID_Offre
 * @property int $ID_Competence
 * @property-read \App\Models\Competence $competence
 * @property-read \App\Models\Offre $offre
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offres_Competences newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offres_Competences newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offres_Competences query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offres_Competences whereIDCompetence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offres_Competences whereIDOffre($value)
 * @mixin \Eloquent
 */
class Offres_Competences extends Model
{
    protected $table = 'Offres_Competences';
    public $timestamps = false;

    protected $fillable = [
        'ID_Offre',
        'ID_Competence',
    ];

    // Relation avec le modèle Offre
    public function offre()
    {
        return $this->belongsTo(Offre::class, 'ID_Offre', 'ID_Offre');
    }

    // Relation avec le modèle Competence
    public function competence()
    {
        return $this->belongsTo(Competence::class, 'ID_Competence', 'ID_Competence');
    }
}
