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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('email', 191)->unique('email');
            $table->string('password');
            $table->enum('role', ['admin', 'editeur', 'lecteur'])->default('lecteur');
            $table->unsignedBigInteger('role_id')->nullable()->index('fk_users_role');
            $table->boolean('actif')->default(true);
            $table->unsignedBigInteger('societe_id')->nullable()->index('fk_users_societe');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
