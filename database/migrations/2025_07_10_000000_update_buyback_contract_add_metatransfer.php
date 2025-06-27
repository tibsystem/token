<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up()
    {
        $path = base_path('contracts/FractionalPropertyToken.sol');
        $code = file_exists($path) ? file_get_contents($path) : null;

        if ($code) {
            DB::table('smart_contract_models')
                ->where('type', 'erc20_buyback')
                ->update([
                    'solidity_code' => $code,
                    'version' => '0.8.20',
                ]);
        }
    }

    public function down()
    {
        // No rollback
    }
};
