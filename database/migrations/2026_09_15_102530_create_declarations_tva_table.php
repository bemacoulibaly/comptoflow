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
        Schema::create('declarations_tva', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('societe_id')->index('fk_tva_societe');
            $table->date('periode_debut');
            $table->date('periode_fin');
            $table->decimal('tva_collectee', 15)->default(0);
            $table->decimal('tva_deductible', 15)->default(0);
            $table->decimal('tva_nette', 15)->default(0);
            $table->enum('statut', ['brouillon', 'validee', 'deposee'])->default('brouillon');
            $table->date('date_echeance')->nullable();
            $table->timestamp('validee_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declarations_tva');
    }
};
