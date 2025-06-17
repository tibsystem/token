<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nome' => 'wesley',
            'email' => 'wesley@ibsystem.com.br',
            'senha_hash' => hash('sha256', '12345678'),
        ]);
    }
}

