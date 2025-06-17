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
            'password' => hash('sha256', '12345678'),
        ]);
    }
}

