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
        Schema::table('lignes_rapprochement', function (Blueprint $table) {
            $table->foreign(['ligne_ecriture_id'], 'fk_lr_ligne_ecriture')->references(['id'])->on('lignes_ecriture')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['rapprochement_id'], 'fk_lr_rapprochement')->references(['id'])->on('rapprochements')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lignes_rapprochement', function (Blueprint $table) {
            $table->dropForeign('fk_lr_ligne_ecriture');
            $table->dropForeign('fk_lr_rapprochement');
        });
    }
};
