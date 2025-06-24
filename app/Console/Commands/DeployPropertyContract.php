<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Property;
use App\Models\SmartContractModel;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\Process\Process;
use App\Helpers\LogTransacaoHelper;

class DeployPropertyContract extends Command
{
    protected $signature = 'deploy:property {propertyId} {--model=} {--name=} {--symbol=} {--supply=}';

    protected $description = 'Deploy smart contract for a property';

    public function handle(): int
    {
        $property = Property::findOrFail($this->argument('propertyId'));

        if ($property->contract_address) {
            $this->error('Property already tokenized');
            return 1;
        }

        $modelId = $this->option('model');
        $model = SmartContractModel::findOrFail($modelId);

        $wallet = $property->user->wallet;
        $privateKey = Crypt::decryptString($wallet->private_key_enc);

        $tmpSol = storage_path('app/'.uniqid('contract_').'.sol');
        file_put_contents($tmpSol, $model->solidity_code);

        $process = new Process([
            'node', base_path('scripts/deploy_contract.js'),
            $tmpSol,
            $privateKey,
            $this->option('name'),
            $this->option('symbol'),
            $this->option('supply')
        ]);
        $process->run();

        if (!$process->isSuccessful()) {
            LogTransacaoHelper::registrar('deploy_error', ['error' => $process->getErrorOutput()], $property->user, $property->id);
            $this->error('Deploy failed');
            @unlink($tmpSol);
            return 1;
        }

        $result = json_decode($process->getOutput(), true);

        $property->update([
            'contract_model_id' => $model->id,
            'contract_address' => $result['address'] ?? null,
            'contract_abi' => json_encode($result['abi'] ?? []),
            'token_name' => $this->option('name'),
            'token_symbol' => $this->option('symbol'),
            'total_supply' => $this->option('supply'),
        ]);

        LogTransacaoHelper::registrar('deploy', ['address' => $result['address']], $property->user, $property->id);
        @unlink($tmpSol);
        $this->info('Contract deployed');
        return 0;
    }
}
