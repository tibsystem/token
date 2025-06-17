<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kycs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('document_type');
            $table->string('document_number');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kycs');
    }
};
