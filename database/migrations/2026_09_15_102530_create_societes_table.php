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
        Schema::create('societes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('raison_sociale', 191);
            $table->string('numero_contribuable', 50)->unique('numero_contribuable');
            $table->enum('regime_fiscal', ['reel_simplifie', 'reel_normal', 'forfait'])->default('reel_simplifie');
            $table->string('devise', 10)->default('XOF');
            $table->decimal('taux_tva', 5)->default(18);
            $table->string('adresse')->nullable();
            $table->string('ville', 100)->nullable();
            $table->string('telephone', 30)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('logo')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('societes');
    }
};
