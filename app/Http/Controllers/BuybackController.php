<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Investor;
use App\Models\CarteiraInterna;
use App\Models\TransacaoFinanceira;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\Process\Process;
use App\Helpers\LogTransacaoHelper;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Buyback",
 *     description="Recompra administrativa de tokens"
 * )
 */
class BuybackController extends Controller
{
    /**
     * Processar recompra de tokens dos investidores.
     *
     * @OA\Post(
     *     path="/api/admin/imoveis/{id}/buyback",
     *     tags={"Buyback"},
     *     security={{"sanctum":{}}},
     *     summary="Recomprar tokens dos investidores",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="valor_pago",
     *                 type="number",
     *                 description="Valor a ser pago por token"
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function buyback(Request $request, $id)
    {
        $property = Property::with('investments')->findOrFail($id);

        $data = $request->validate([
            'valor_pago' => 'required|numeric',
        ]);

        $addresses = [];
        $amounts = [];

        $investmentData = $property->investments()
            ->selectRaw('id_investidor, SUM(qtd_tokens) as total_tokens')
            ->groupBy('id_investidor')
            ->get();

        foreach ($investmentData as $info) {
            $investor = Investor::find($info->id_investidor);
            if (!$investor) {
                continue;
            }

            $payout = $info->total_tokens * $data['valor_pago'];

            $wallet = CarteiraInterna::where('id_investidor', $investor->id)->lockForUpdate()->first();
            if ($wallet) {
                $wallet->saldo_disponivel += $payout;
                $wallet->save();
            }

            TransacaoFinanceira::create([
                'id' => (string) Str::uuid(),
                'id_investidor' => $investor->id,
                'tipo' => 'rendimento',
                'valor' => $payout,
                'status' => 'concluido',
                'referencia' => 'buyback:' . $property->id,
                'data_transacao' => now(),
            ]);

            if ($investor->carteira_blockchain) {
                $addresses[] = $investor->carteira_blockchain;
                $amounts[] = bcmul((string) $info->total_tokens, '1000000000000000000');
            }
        }

        if (
            $property->contract_address &&
            $property->contract_abi &&
            $property->user &&
            $property->user->wallet &&
            !empty($addresses)
        ) {
            try {
                $privKey = Crypt::decryptString($property->user->wallet->private_key_enc);
                $abiPath = storage_path('app/' . uniqid('abi_') . '.json');
                file_put_contents($abiPath, $property->contract_abi);

                $process = new Process([
                    'node', base_path('scripts/admin_buyback.js'),
                    $property->contract_address,
                    $abiPath,
                    $privKey,
                    implode(',', $addresses),
                    implode(',', $amounts),
                ]);
                $process->run();
                @unlink($abiPath);
            } catch (\Exception $e) {
                LogTransacaoHelper::registrar('admin_buyback_error', ['error' => $e->getMessage()], auth('api')->user(), $property->id);
            }
        }

        $property->status = 'vendido';
        $property->save();

        LogTransacaoHelper::registrar('admin_buyback', $data, auth('api')->user(), $property->id);

        return response()->json(['status' => 'success']);
    }
}
