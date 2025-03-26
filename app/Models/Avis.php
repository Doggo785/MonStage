<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $ID_Avis
 * @property string $Note
 * @property int $ID_Entreprise
 * @property int $ID_User
 * @property-read \App\Models\Entreprise $entreprise
 * @property-read \App\Models\Utilisateur $utilisateur
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avis newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avis newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avis query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avis whereIDAvis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avis whereIDEntreprise($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avis whereIDUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Avis whereNote($value)
 * @mixin \Eloquent
 */
class Avis extends Model
{
    protected $table = 'Avis';
    protected $primaryKey = 'ID_Avis';
    public $timestamps = false;

    protected $fillable = [
        'Note',
        'ID_Entreprise',
        'ID_User',
    ];

    // Relation avec le modèle Entreprise
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'ID_Entreprise', 'ID_Entreprise');
    }

    // Relation avec le modèle Utilisateur
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'ID_User', 'ID_User');
    }
}
