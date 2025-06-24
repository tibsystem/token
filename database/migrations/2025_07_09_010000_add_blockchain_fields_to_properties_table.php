<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('contract_model_id')->nullable()->after('modelo_smart_id');
            $table->string('contract_address')->nullable();
            $table->string('token_symbol')->nullable();
            $table->string('token_name')->nullable();
            $table->longText('contract_abi')->nullable();
            $table->unsignedBigInteger('total_supply')->nullable();
        });
    }

    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['contract_model_id','contract_address','token_symbol','token_name','contract_abi','total_supply']);
        });
    }
};
