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
        Schema::table('rapprochements', function (Blueprint $table) {
            $table->foreign(['compte_id'], 'fk_rapp_compte')->references(['id'])->on('comptes')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['societe_id'], 'fk_rapp_societe')->references(['id'])->on('societes')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['valide_par'], 'fk_rapp_user')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rapprochements', function (Blueprint $table) {
            $table->dropForeign('fk_rapp_compte');
            $table->dropForeign('fk_rapp_societe');
            $table->dropForeign('fk_rapp_user');
        });
    }
};
