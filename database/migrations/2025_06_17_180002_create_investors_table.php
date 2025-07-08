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
            $table->string('name');
            $table->string('email')->unique();
            $table->string('document');
            $table->string('phone')->nullable();
            $table->string('password')->nullable();
            $table->enum('status_kyc', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('type', ['pf', 'pj']);
            $table->string('wallet_blockchain')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('investors');
    }
};
