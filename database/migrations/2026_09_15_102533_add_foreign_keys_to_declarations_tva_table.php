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
        Schema::table('declarations_tva', function (Blueprint $table) {
            $table->foreign(['societe_id'], 'fk_tva_societe')->references(['id'])->on('societes')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('declarations_tva', function (Blueprint $table) {
            $table->dropForeign('fk_tva_societe');
        });
    }
};
