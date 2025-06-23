<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tipo');
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->foreignId('id_imovel')->nullable()->constrained('properties');
            $table->json('dados');
            $table->string('ip_origem', 45)->nullable();
            $table->string('navegador', 255)->nullable();
            $table->dateTime('criado_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaction_logs');
    }
};
