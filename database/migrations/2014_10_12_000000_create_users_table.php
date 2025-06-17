<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        // database/migrations/xxxx_xx_xx_create_users_table.php
        public function up()
        {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('nome', 100);
                $table->string('email', 120)->unique();
                $table->string('senha_hash', 256);
                $table->enum('tipo', ['investidor','admin','compliance','suporte'])->default('investidor');
                $table->string('telefone', 30)->nullable();
                $table->enum('status', ['ativo','inativo','pendente'])->default('pendente');
                $table->enum('status_kyc', ['aprovado','pendente','recusado'])->default('pendente');
                $table->timestamps();
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
