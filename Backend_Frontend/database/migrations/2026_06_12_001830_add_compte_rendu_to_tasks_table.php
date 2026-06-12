<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // On ajoute la colonne. "nullable()" est très important pour ne pas
            // faire planter les anciennes tâches qui n'ont pas de compte-rendu !
            $table->text('compte_rendu')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Permet d'annuler cette modification si besoin
            $table->dropColumn('compte_rendu');
        });
    }
};
