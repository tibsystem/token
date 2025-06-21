<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('carteiras_internas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_investidor')->constrained('investors');
            $table->string('endereco_wallet')->nullable();
            $table->decimal('saldo_disponivel', 15, 2)->default(0);
            $table->decimal('saldo_bloqueado', 15, 2)->default(0);
            $table->json('saldo_tokenizado')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('carteiras_internas');
    }
};
