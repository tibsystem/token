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
            $table->string('title');
            $table->text('description');
            $table->string('location')->nullable(); // se não for mais obrigatório
            $table->decimal('total_value', 15, 2);
            $table->integer('total_tokens');
            $table->unsignedBigInteger('smart_contract_model_id')->nullable();
            $table->enum('status', ['active', 'sold', 'pending', 'hidden'])->default('pending');
            $table->dateTime('tokenization_date')->nullable(); // adicionei nullable caso não venha no início
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('properties');
    }
};
