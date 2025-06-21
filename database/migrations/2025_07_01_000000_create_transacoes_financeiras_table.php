<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transacoes_financeiras', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('id_investidor')->constrained('investors');
            $table->enum('tipo', ['deposito', 'saque', 'rendimento', 'taxa', 'compra_token']);
            $table->decimal('valor', 15, 2);
            $table->enum('status', ['pendente', 'concluido', 'falhou'])->default('pendente');
            $table->text('referencia')->nullable();
            $table->dateTime('data_transacao');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transacoes_financeiras');
    }
};
