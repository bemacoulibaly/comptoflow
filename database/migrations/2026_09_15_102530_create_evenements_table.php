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
        Schema::create('evenements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('societe_id')->index('fk_ev_societe');
            $table->unsignedBigInteger('user_id')->index('fk_ev_user');
            $table->string('titre', 191);
            $table->text('description')->nullable();
            $table->date('date_echeance')->index('idx_evenement_date');
            $table->enum('type', ['tva', 'cnps', 'bic', 'facture', 'rapprochement', 'autre'])->default('autre');
            $table->enum('priorite', ['haute', 'normale', 'basse'])->default('normale');
            $table->boolean('notifie')->default(false);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evenements');
    }
};
