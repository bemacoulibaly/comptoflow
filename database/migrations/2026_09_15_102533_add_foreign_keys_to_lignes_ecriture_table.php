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
        Schema::table('lignes_ecriture', function (Blueprint $table) {
            $table->foreign(['compte_id'], 'fk_lignes_compte')->references(['id'])->on('comptes')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ecriture_id'], 'fk_lignes_ecriture')->references(['id'])->on('ecritures')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['tiers_id'], 'fk_lignes_tiers')->references(['id'])->on('tiers')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lignes_ecriture', function (Blueprint $table) {
            $table->dropForeign('fk_lignes_compte');
            $table->dropForeign('fk_lignes_ecriture');
            $table->dropForeign('fk_lignes_tiers');
        });
    }
};
