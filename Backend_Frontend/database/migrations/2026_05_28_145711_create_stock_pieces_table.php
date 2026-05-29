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
            $table->integer('seuil_alerte')->default(5);
            $table->decimal('prix_unitaire', 10, 2);

            // La relation avec le fournisseur (clé étrangère)
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('cascade');

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
