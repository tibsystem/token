<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('p2p_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendedor_id')->constrained('investors');
            $table->foreignId('id_imovel')->constrained('properties');
            $table->integer('qtd_tokens');
            $table->decimal('valor_unitario', 15, 2);
            $table->enum('status', ['ativa', 'concluida', 'cancelada'])->default('ativa');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('p2p_listings');
    }
};
