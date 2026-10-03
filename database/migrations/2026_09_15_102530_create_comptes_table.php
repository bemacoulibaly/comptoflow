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
        Schema::create('comptes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('societe_id');
            $table->string('numero', 20)->index('idx_numero');
            $table->string('libelle');
            $table->enum('classe', ['1', '2', '3', '4', '5', '6', '7', '8']);
            $table->enum('type', ['actif', 'passif', 'charge', 'produit']);
            $table->boolean('actif')->default(true);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->unique(['societe_id', 'numero'], 'uq_compte');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comptes');
    }
};
