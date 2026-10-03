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
        Schema::create('lignes_facture', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('facture_id')->index('fk_lignes_facture');
            $table->string('designation');
            $table->decimal('quantite', 10)->default(1);
            $table->string('unite', 20)->nullable();
            $table->decimal('prix_unitaire', 15)->default(0);
            $table->decimal('taux_tva', 5)->default(18);
            $table->decimal('montant_ht', 15)->default(0);
            $table->decimal('montant_tva', 15)->default(0);
            $table->integer('ordre')->default(0);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lignes_facture');
    }
};
