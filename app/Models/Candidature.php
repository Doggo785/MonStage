<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    use HasFactory;

    protected $table = 'Candidature'; // Nom de la table
    public $timestamps = false; // Désactiver les colonnes created_at et updated_at
    protected $primaryKey = null; // Indiquer que la table n'a pas de clé primaire auto-incrémentée
    public $incrementing = false; // Désactive l'auto-incrément

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array
     */
    protected $fillable = [
        'ID_User',
        'ID_Offre',
        'CV_path',
        'LM_Path',
        'Date_postule',
        'ID_Statut',
    ];

    /**
     * Relation avec l'utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ID_User', 'ID_User');
    }

    /**
     * Relation avec l'offre.
     */
    public function offre(): BelongsTo
    {
        return $this->belongsTo(Offre::class, 'ID_Offre', 'ID_Offre');
    }

    public function statut() {
        return $this->belongsTo(Statuts_Candidature::class, 'ID_Statut');
    }
}
