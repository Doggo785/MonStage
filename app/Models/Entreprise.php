<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $ID_Entreprise
 * @property string $Nom
 * @property string|null $Telephone
 * @property string $Email
 * @property string $Site
 * @property string $Description
 * @property int $ID_Ville
 * @property-read \App\Models\Ville $ville
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise whereIDEntreprise($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise whereIDVille($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise whereSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise whereTelephone($value)
 * @mixin \Eloquent
 */
class Entreprise extends Model
{
    protected $table = 'Entreprise';
    protected $primaryKey = 'ID_Entreprise';
    public $timestamps = false;

    protected $fillable = [
        'Nom',
        'Telephone',
        'Email',
        'Site',
        'Description',
        'ID_Ville',
        'pfp_path',
    ];

    // Relation avec le modèle Ville
    public function ville()
    {
        return $this->belongsTo(Ville::class, 'ID_Ville', 'ID_Ville');
    }

    // Relation avec le modèle Avis
    public function avis()
    {
        return $this->hasMany(Avis::class, 'ID_Entreprise', 'ID_Entreprise');
    }
}
