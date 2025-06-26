<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use App\Models\Wallet;

class ShowWalletPrivateKey extends Command
{
    protected $signature = 'wallet:show-private {walletId}';
    protected $description = 'Exibe a chave privada descriptografada de uma carteira específica';

    public function handle()
    {
        $walletId = $this->argument('walletId');

        $wallet = Wallet::find($walletId);

        if (!$wallet) {
            $this->error("Carteira com ID {$walletId} não encontrada.");
            return 1;
        }

        try {
            $privateKey = Crypt::decryptString($wallet->private_key_enc);
            $this->info("Endereço: {$wallet->address}");
            $this->info("Chave privada: {$privateKey}");
        } catch (\Exception $e) {
            $this->error("Erro ao descriptografar a chave privada: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
