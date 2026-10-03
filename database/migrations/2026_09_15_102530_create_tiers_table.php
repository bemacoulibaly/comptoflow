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
        Schema::create('tiers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('societe_id');
            $table->enum('type', ['client', 'fournisseur', 'les_deux'])->index('idx_tiers_type');
            $table->string('nom', 191)->index('idx_tiers_nom');
            $table->string('email', 191)->nullable();
            $table->string('telephone', 30)->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville', 100)->nullable();
            $table->string('numero_contribuable', 50)->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->index(['societe_id', 'actif'], 'idx_tiers_societe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiers');
    }
};
