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
        Schema::create('factures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('societe_id');
            $table->unsignedBigInteger('tiers_id')->index('idx_fac_tiers');
            $table->unsignedBigInteger('user_id')->index('fk_factures_user');
            $table->string('numero', 50)->unique('numero');
            $table->enum('type', ['client', 'fournisseur'])->index('idx_facture_type');
            $table->date('date_emission');
            $table->date('date_echeance')->index('idx_facture_echeance');
            $table->decimal('montant_ht', 15)->default(0);
            $table->decimal('taux_tva', 5)->default(18);
            $table->decimal('montant_tva', 15)->default(0);
            $table->decimal('montant_ttc', 15)->default(0);
            $table->decimal('montant_paye', 15)->default(0);
            $table->enum('statut', ['brouillon', 'emise', 'partielle', 'payee', 'en_retard', 'annulee'])->default('brouillon')->index('idx_facture_statut');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('ecriture_id')->nullable()->index('fk_factures_ecriture');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->index(['societe_id', 'type', 'statut'], 'idx_fac_societe_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
