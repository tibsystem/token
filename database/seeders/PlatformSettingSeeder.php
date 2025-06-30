<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlatformSetting;
use App\Models\PlatformWallet;

class PlatformSettingSeeder extends Seeder
{
    public function run(): void
    {
        PlatformSetting::firstOrCreate([], [
            'taxa_compra_token' => 0,
            'taxa_negociacao_p2p' => 0,
        ]);
        PlatformWallet::firstOrCreate([], [
            'saldo_disponivel' => 0,
            'saldo_bloqueado' => 0,
        ]);
    }
}
