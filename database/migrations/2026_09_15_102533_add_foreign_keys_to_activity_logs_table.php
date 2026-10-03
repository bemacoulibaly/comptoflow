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
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->foreign(['societe_id'], 'fk_al_societe')->references(['id'])->on('societes')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['user_id'], 'fk_al_user')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign('fk_al_societe');
            $table->dropForeign('fk_al_user');
        });
    }
};
