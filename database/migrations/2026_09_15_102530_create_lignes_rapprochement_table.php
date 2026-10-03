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
        Schema::create('lignes_rapprochement', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('rapprochement_id')->index('fk_lr_rapprochement');
            $table->unsignedBigInteger('ligne_ecriture_id')->nullable()->index('fk_lr_ligne_ecriture');
            $table->date('date_operation');
            $table->string('libelle');
            $table->decimal('montant', 15);
            $table->enum('source', ['releve', 'comptabilite', 'les_deux']);
            $table->boolean('pointe')->default(false);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lignes_rapprochement');
    }
};
