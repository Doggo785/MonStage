<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $ID_User
 * @property int $ID_Offre
 * @property int $ID_Statut
 * @property string $Date_postule
 * @property string|null $LM_Path
 * @property string|null $CV_path
 * @property-read \App\Models\Offre $offre
 * @property-read \App\Models\Statuts_Candidature $statut
 * @property-read \App\Models\User $utilisateur
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereCVPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereDatePostule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereIDOffre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereIDStatut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereIDUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidature whereLMPath($value)
 * @mixin \Eloquent
 */
class Candidature extends Model {
    protected $table = 'Candidature';
    public $timestamps = false;
    public $incrementing = false; // Désactive l'auto-incrément

    // Relations
    public function user() {
        return $this->belongsTo(User::class, 'ID_User');
    }

    public function offre() {
        return $this->belongsTo(Offre::class, 'ID_Offre');
    }

    public function statut() {
        return $this->belongsTo(Statuts_Candidature::class, 'ID_Statut');
    }
}
