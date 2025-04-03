<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Spécifie la table utilisée par le modèle
    protected $table = 'Utilisateur';

    // Spécifie la clé primaire
    protected $primaryKey = 'ID_User';

    // Désactive les timestamps automatiques (si non utilisés dans la table)
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'Nom',
        'Prenom',
        'Email',
        'Telephone',
        'Password',
        'ID_Role',
        'pfp_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'Password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'pfp_path' => 'string',
        ];
    }

    // Relations
    public function etudiant()
    {
        return $this->hasOne(Etudiant::class, 'ID_User');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'ID_Role');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'ID_User', 'ID_User');
    }

    public function avis()
    {
        return $this->hasMany(Avis::class, 'ID_User', 'ID_User');
    }

    /**
     * Relation avec les candidatures.
     */
    public function candidatures(): HasMany
    {
        return $this->hasMany(Candidature::class, 'ID_User', 'ID_User');
    }

    public function getAuthPassword()
    {
        return $this->attributes['Password'];
    }
}
