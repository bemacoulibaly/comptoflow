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
        Schema::table('factures', function (Blueprint $table) {
            $table->foreign(['ecriture_id'], 'fk_factures_ecriture')->references(['id'])->on('ecritures')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['societe_id'], 'fk_factures_societe')->references(['id'])->on('societes')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['tiers_id'], 'fk_factures_tiers')->references(['id'])->on('tiers')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'], 'fk_factures_user')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->dropForeign('fk_factures_ecriture');
            $table->dropForeign('fk_factures_societe');
            $table->dropForeign('fk_factures_tiers');
            $table->dropForeign('fk_factures_user');
        });
    }
};
