<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $ID_Offre
 * @property string $Titre
 * @property string|null $Description
 * @property string|null $Remuneration
 * @property int $Etat
 * @property string $Date_publication
 * @property string|null $Date_expiration
 * @property int $ID_Secteur
 * @property int $ID_Ville
 * @property int $ID_Entreprise
 * @property-read \App\Models\Entreprise $entreprise
 * @property-read \App\Models\Secteur $secteur
 * @property-read \App\Models\Ville $ville
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offre query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offre whereDateExpiration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offre whereDatePublication($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offre whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offre whereEtat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offre whereIDEntreprise($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offre whereIDOffre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offre whereIDSecteur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offre whereIDVille($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offre whereRemuneration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Offre whereTitre($value)
 * @mixin \Eloquent
 */
class Offre extends Model
{
    use HasFactory;

    protected $table = 'Offre'; // Nom exact de la table
    protected $primaryKey = 'ID_Offre'; // Clé primaire
    public $timestamps = false; // Désactive les colonnes created_at et updated_at

    protected $fillable = [
        'Titre',
        'Description',
        'Remuneration',
        'Etat',
        'Date_publication',
        'Date_expiration',
        'ID_Secteur',
        'ID_Ville',
        'ID_Entreprise',
    ];

    // Relations avec les autres modèles
    public function secteur()
    {
        return $this->belongsTo(Secteur::class, 'ID_Secteur', 'ID_Secteur');
    }

    public function ville()
    {
        return $this->belongsTo(Ville::class, 'ID_Ville', 'ID_Ville');
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'ID_Entreprise', 'ID_Entreprise');
    }

    public function competences()
    {
        return $this->belongsToMany(
            Competence::class,
            'Offres_Competences',
            'ID_Offre',
            'ID_Competence'
        );
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class, 'ID_Offre');
    }
}
