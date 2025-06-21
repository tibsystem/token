<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('investors', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('documento');
            $table->string('telefone')->nullable();
            $table->enum('status_kyc', ['pendente', 'aprovado', 'rejeitado'])->default('pendente');
            $table->string('carteira_blockchain')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('investors');
    }
};
