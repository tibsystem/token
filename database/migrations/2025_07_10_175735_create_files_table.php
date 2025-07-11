<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('path'); // Caminho no storage
            $table->string('type_file')->nullable();
            $table->bigInteger('size')->nullable();

            $table->unsignedBigInteger('fileable_id');
            $table->string('fileable_type');

            $table->timestamps();

            $table->index(['fileable_id', 'fileable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
