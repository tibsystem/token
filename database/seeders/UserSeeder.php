<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Models\Wallet;
use App\Helpers\WalletHelper;
use App\Models\CarteiraInterna;
use App\Models\Investor;
use App\Models\Participant;
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

        // Cria um investidor do tipo PF com carteira blockchain
        $walletInvestor = WalletHelper::generatePolygonWallet();

        $investorPF = Investor::create([
            'name' => 'Investidor PF',
            'email' => 'investidor@ibsystem.com.br',
            'document' => "111.111.111-11",
            'phone' => "11999999999",
            'password' => bcrypt('password'),
            'status_kyc' => "pending",
            'type' => 'pf',
            'wallet_blockchain' => $walletInvestor['address'],
            'wallet_private_key' => Crypt::encryptString($walletInvestor['private_key']),
        ]);

        CarteiraInterna::create([
            'id_investidor' => $investorPF->id,
            'endereco_wallet' => $walletInvestor['address'],
            'saldo_disponivel' => 0,
            'saldo_bloqueado' => 0,
            'saldo_tokenizado' => [],
        ]);

        // Cria um investidor do tipo PJ com participantes vinculados
        $walletPJ = WalletHelper::generatePolygonWallet();

        $investorPJ = Investor::create([
            'name' => 'Empresa Exemplo LTDA',
            'email' => 'empresa@ibsystem.com.br',
            'document' => '12.345.678/0001-99',
            'phone' => '11999999998',
            'password' => bcrypt('empresa123'),
            'status_kyc' => 'pending',
            'type' => 'pj',
            'wallet_blockchain' => $walletPJ['address'],
            'wallet_private_key' => Crypt::encryptString($walletPJ['private_key']),
        ]);

        CarteiraInterna::create([
            'id_investidor' => $investorPJ->id,
            'endereco_wallet' => $walletPJ['address'],
            'saldo_disponivel' => 0,
            'saldo_bloqueado' => 0,
            'saldo_tokenizado' => [],
        ]);

        // Participantes da empresa
        Participant::create([
            'investor_id' => $investorPJ->id,
            'name' => 'João Participante',
            'email' => 'joao@empresa.com.br',
            'document' => '123.456.789-00',
            'password' => bcrypt('joao123'),
        ]);

        Participant::create([
            'investor_id' => $investorPJ->id,
            'name' => 'Maria Participante',
            'email' => 'maria@empresa.com.br',
            'document' => '987.654.321-00',
            'password' => bcrypt('maria123'),
        ]);
    }
}
