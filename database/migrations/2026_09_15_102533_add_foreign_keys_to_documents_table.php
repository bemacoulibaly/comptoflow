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
        Schema::table('documents', function (Blueprint $table) {
            $table->foreign(['societe_id'], 'fk_doc_societe')->references(['id'])->on('societes')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['user_id'], 'fk_doc_user')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign('fk_doc_societe');
            $table->dropForeign('fk_doc_user');
        });
    }
};
