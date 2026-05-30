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
        Schema::create('fiche_travail_stock_piece', function (Blueprint $table) {
            $table->id();

            // Clés étrangères vers la fiche de travail et la pièce du stock
            $table->foreignId('fiche_travail_id')->constrained('fiche_travails')->cascadeOnDelete();
            $table->foreignId('stock_piece_id')->constrained('stock_pieces')->cascadeOnDelete();

            $table->integer('quantite'); // Quantité de pièces consommées pour cette fiche

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiche_travail_stock_piece');
    }
};
