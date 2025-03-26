<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'Region';
    protected $primaryKey = 'ID_Region';
    public $timestamps = false;

    protected $fillable = [
        'Nom',
    ];
}
