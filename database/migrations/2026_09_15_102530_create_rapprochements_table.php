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
        Schema::create('rapprochements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('societe_id')->index('fk_rapp_societe');
            $table->unsignedBigInteger('compte_id')->index('fk_rapp_compte');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->decimal('solde_releve', 15)->default(0);
            $table->decimal('solde_comptable', 15)->default(0);
            $table->decimal('ecart', 15)->default(0);
            $table->enum('statut', ['en_cours', 'valide'])->default('en_cours');
            $table->timestamp('valide_at')->nullable();
            $table->unsignedBigInteger('valide_par')->nullable()->index('fk_rapp_user');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapprochements');
    }
};
