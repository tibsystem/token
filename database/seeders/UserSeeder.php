<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Models\Wallet;
use App\Helpers\WalletHelper;
use App\Models\CarteiraInterna;
use App\Models\Investor;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Cria um usuário administrador
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

        // Cria um investidor com carteira blockchain
        $walletInvestor = WalletHelper::generatePolygonWallet();

        $user = Investor::create([
            'nome' => 'Investidor',
            'email' => 'investidor@ibsystem.com.br',
            'documento' => "111.111.111-11",
            "telefone" =>  "11999999999",
            'senha_hash' => bcrypt('password'),
            "status_kyc" =>  "pendente",
            'carteira_blockchain' => $walletInvestor['address'],
            'carteira_private_key' => Crypt::encryptString($walletInvestor['private_key']),
        ]);
        CarteiraInterna::create([
            'id_investidor' => 1,
            'endereco_wallet' => $walletInvestor['address'],
            'saldo_disponivel' => 0,
            'saldo_bloqueado' => 0,
            'saldo_tokenizado' => [],
        ]);
    }
}
