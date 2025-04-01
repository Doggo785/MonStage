<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        // Pour la table Utilisateur
        Schema::table('Utilisateur', function (Blueprint $table) {
            $table->string('pfp_path')->nullable()->comment('Chemin de la photo de profil');
        });
    
        // Pour la table Entreprise
        Schema::table('Entreprise', function (Blueprint $table) {
            $table->string('pfp_path')->nullable()->comment('Chemin de la photo de profil');
        });
    }
};
