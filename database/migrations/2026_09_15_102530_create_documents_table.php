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
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('societe_id');
            $table->unsignedBigInteger('user_id')->index('fk_doc_user');
            $table->string('documentable_type', 191);
            $table->unsignedBigInteger('documentable_id');
            $table->string('nom_original');
            $table->string('chemin', 500);
            $table->string('type_mime', 100);
            $table->unsignedBigInteger('taille');
            $table->enum('categorie', ['facture', 'devis', 'contrat', 'recu', 'bon_livraison', 'releve', 'autre'])->default('autre');
            $table->text('description')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->index(['documentable_type', 'documentable_id'], 'idx_doc_documentable');
            $table->index(['societe_id', 'created_at'], 'idx_doc_societe_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
