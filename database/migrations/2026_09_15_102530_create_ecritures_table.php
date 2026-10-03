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
        Schema::create('ecritures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('societe_id');
            $table->unsignedBigInteger('user_id')->index('fk_ecritures_user');
            $table->string('numero_piece', 50);
            $table->date('date_ecriture')->index('idx_ecriture_date');
            $table->enum('journal', ['BQ', 'CA', 'AC', 'VT', 'OD', 'SA'])->index('idx_ecriture_journal');
            $table->string('libelle');
            $table->string('reference_tiers', 191)->nullable();
            $table->enum('statut', ['brouillon', 'validee', 'annulee'])->default('brouillon')->index('idx_ecriture_statut');
            $table->timestamp('validee_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->index(['societe_id', 'date_ecriture'], 'idx_ecr_societe_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecritures');
    }
};
