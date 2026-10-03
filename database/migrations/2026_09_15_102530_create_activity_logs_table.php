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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('societe_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_nom_snapshot', 191)->nullable();
            $table->string('action', 100);
            $table->string('description');
            $table->string('sujet_type', 191)->nullable();
            $table->unsignedBigInteger('sujet_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index(['societe_id', 'created_at'], 'idx_al_societe_date');
            $table->index(['sujet_type', 'sujet_id'], 'idx_al_sujet');
            $table->index(['user_id', 'created_at'], 'idx_al_user_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
