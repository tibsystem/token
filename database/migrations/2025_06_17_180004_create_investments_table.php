<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_investidor')->constrained('investors');
            $table->foreignId('id_imovel')->constrained('properties');
            $table->integer('qtd_tokens');
            $table->decimal('valor_unitario', 15, 2);
            $table->dateTime('data_compra');
            $table->enum('origem', ['plataforma', 'p2p']);
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('investments');
    }
};
