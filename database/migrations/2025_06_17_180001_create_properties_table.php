<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao');
            $table->string('localizacao');
            $table->decimal('valor_total', 15, 2);
            $table->integer('qtd_tokens');
            $table->unsignedBigInteger('modelo_smart_id')->nullable();
            $table->enum('status', ['ativo', 'vendido', 'oculto'])->default('ativo');
            $table->dateTime('data_tokenizacao');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('properties');
    }
};
