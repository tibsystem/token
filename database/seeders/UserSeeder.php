<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Models\Wallet;
use App\Helpers\WalletHelper;


class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'nome' => 'wesley',
            'email' => 'wesley@ibsystem.com.br',
            'password' => bcrypt('12345678'),
            'tipo' => 'admin',
        ]);

        $wallet = WalletHelper::generatePolygonWallet();

        Wallet::create([
            'user_id' => $user->id,
            'polygon_address' => $wallet['address'],
            'private_key_enc' => Crypt::encryptString($wallet['private_key']),
            'saldo' => 0,
        ]);
    }
}

