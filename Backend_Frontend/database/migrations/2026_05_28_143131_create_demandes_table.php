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
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['entretien', 'fabrication']);
            $table->text('description');

            // Le statut basé sur ton diagramme de séquence 1
            $table->enum('statut', ['en_attente', 'acceptee', 'refusee'])->default('en_attente');

            // Clé étrangère vers l'employé qui fait la demande
            $table->foreignId('employe_id')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};
