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
        Schema::create('stock_pieces', function (Blueprint $table) {
            $table->id();
            $table->string('designation');
            $table->integer('quantite')->default(0);
            $table->integer('seuil_alerte')->default(5); // Déclenchera l'alerte de stock bas
            $table->decimal('prix_unitaire', 8, 2); // 8 chiffres au total, dont 2 après la virgule

            // Clé étrangère vers le fournisseur
            $table->foreignId('fournisseur_id')->nullable()->constrained('fournisseurs')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_pieces');
    }
};
