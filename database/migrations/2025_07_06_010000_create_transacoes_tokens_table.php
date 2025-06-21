<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transacoes_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('vendedor_id')->constrained('investors');
            $table->foreignId('comprador_id')->constrained('investors');
            $table->foreignId('id_imovel')->constrained('properties');
            $table->integer('qtd_tokens');
            $table->decimal('valor_unitario', 15, 2);
            $table->dateTime('data_transacao');
            $table->text('tx_hash')->nullable();
            $table->enum('status', ['pendente', 'concluida', 'cancelada'])->default('concluida');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transacoes_tokens');
    }
};
