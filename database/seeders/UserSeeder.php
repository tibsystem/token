<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Wallet;

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

        Wallet::create([
            'user_id' => $user->id,
            'polygon_address' => '0x' . Str::random(40),
            'private_key_enc' => Crypt::encryptString(Str::random(64)),
            'saldo' => 0,
        ]);
    }
}

