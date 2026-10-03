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
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('societe_id');
            $table->unsignedBigInteger('user_id')->nullable()->index('fk_at_user');
            $table->string('user_nom_snapshot', 191)->nullable();
            $table->string('sujet_type', 191);
            $table->unsignedBigInteger('sujet_id');
            $table->enum('evenement', ['cree', 'modifie', 'supprime']);
            $table->json('valeurs_avant')->nullable();
            $table->json('valeurs_apres')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index(['societe_id', 'created_at'], 'idx_at_societe_date');
            $table->index(['sujet_type', 'sujet_id'], 'idx_at_sujet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
