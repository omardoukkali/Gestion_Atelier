<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('demande_id')
                ->nullable()          // nullable : les tâches créées "à la main" n'ont pas de demande
                ->after('id')
                ->constrained('demandes')
                ->nullOnDelete();     // si la demande est supprimée, la tâche reste
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['demande_id']);
            $table->dropColumn('demande_id');
        });
    }
};
