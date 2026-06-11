<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ligne_devis', function (Blueprint $table) {
            // Le statut par défaut sera 'en_attente' quand l'ouvrier fait sa demande
            $table->string('statut')->default('en_attente')->after('prix_unitaire');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ligne_devis', function (Blueprint $table) {
            $table->dropColumn('statut');
        });
    }
};
