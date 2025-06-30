<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('platform_wallets', function (Blueprint $table) {
            $table->id();
            $table->decimal('saldo_disponivel', 15, 2)->default(0);
            $table->decimal('saldo_bloqueado', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('platform_wallets');
    }
};
