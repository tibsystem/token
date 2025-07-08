<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Investment;
use App\Models\TransacaoFinanceira;
use App\Models\CarteiraInterna;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\Process\Process;
use App\Helpers\LogTransacaoHelper;
use App\Models\Property;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Investments",
 *     description="Operações de investimentos"
 * )
 */
class InvestmentController extends Controller
{
    /**
     * Realizar compra de tokens de um imóvel.
     *
     * @OA\Post(
     *     path="/api/investments/purchase",
     *     tags={"Investments"},
     *     security={{"sanctum":{}}},
     *     summary="Comprar tokens",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id_investidor","id_imovel","qtd_tokens","valor_unitario","data_compra","origem"},
     *             @OA\Property(property="id_investidor", type="integer", example=1),
     *             @OA\Property(property="id_imovel", type="integer", example=10),
     *             @OA\Property(property="qtd_tokens", type="integer", example=100),
     *             @OA\Property(property="valor_unitario", type="number", example=9.99),
     *             @OA\Property(property="data_compra", type="string", example="2024-01-01"),
     *             @OA\Property(property="origem", type="string", example="plataforma"),
     *             @OA\Property(property="status", type="string", example="ativo")
     *         )
     *     ),

     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function purchase(Request $request)
    {
        $data = $request->validate([
            'id_investidor' => 'required|integer|exists:investors,id',
            'id_imovel' => 'required|integer|exists:properties,id',
            'qtd_tokens' => 'required|integer|min:1',
            'valor_unitario' => 'required|numeric',
            'data_compra' => 'required|date',
            'origem' => 'required|in:plataforma,p2p',
            'status' => 'in:ativo,inativo',
        ]);

        try {
            $investment = DB::transaction(function () use ($data) {
                $property = Property::lockForUpdate()->find($data['id_imovel']);
                if (!$property || !$property->decreaseTokens($data['qtd_tokens'])) {
                    throw new \RuntimeException('Tokens insuficientes');
                }

                // Atualiza status do imóvel caso todos os tokens tenham sido vendidos
                if ($property->qtd_tokens === 0 && $property->status !== 'vendido') {
                    $property->status = 'vendido';
                    $property->save();
                }



                $investment = Investment::create($data);

                $settings = \App\Models\PlatformSetting::first();
                $percent = $settings?->taxa_compra_token ?? 0;

                if ($data['origem'] === 'plataforma') {
                    $valorTotal = ($data['qtd_tokens'] * $data['valor_unitario']) + (($data['qtd_tokens'] * $data['valor_unitario']) * ($percent / 100));
                    $valorTotalSem = ($data['qtd_tokens'] * $data['valor_unitario']);



                    $taxa = $valorTotalSem * ($percent / 100);

                    TransacaoFinanceira::create([
                        'id' => (string) Str::uuid(),
                        'id_investidor' => $data['id_investidor'],
                        'tipo' => 'compra_token',
                        'valor' => $valorTotal,
                        'status' => 'concluido',
                        'referencia' => 'investimento:' . $investment->id,
                        'data_transacao' => $data['data_compra'],
                    ]);

                    $carteira = CarteiraInterna::where('id_investidor', $data['id_investidor'])->first();
                    if ($carteira) {
                        $carteira->saldo_disponivel -= $valorTotal;
                        $carteira->save();
                    }



                    $platformWallet = \App\Models\PlatformWallet::first();
                    if ($platformWallet) {
                        $platformWallet->saldo_disponivel += $taxa;
                        $platformWallet->save();
                    }

                    $propertyWallet = \App\Models\PropertyWallet::where('property_id', $data['id_imovel'])->first();
                    if ($propertyWallet) {
                        $propertyWallet->saldo_disponivel += $valorTotalSem - $taxa;
                        $propertyWallet->save();
                    }

                    \App\Helpers\LogTransacaoHelper::registrar(
                        'taxa_compra_token',
                        ['taxa' => $taxa, 'investment_id' => $investment->id],
                        auth('investor')->user(),
                        $data['id_imovel']
                    );
                }

                return $investment;
            });
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        LogTransacaoHelper::registrar(
            'compra_token',
            array_merge($data, ['investment_id' => $investment->id]),
            auth('investor')->user(),
            $data['id_imovel']
        );

        $property = $investment->property;
        $investor = $investment->investor;
        $txHash = null;
        if (
            $property &&
            $property->contract_address &&
            $property->contract_abi &&
            $property->user &&
            $property->user->wallet
        ) {
            try {
                $privKey = Crypt::decryptString($property->user->wallet->private_key_enc);

                $abiPath = storage_path('app/' . uniqid('abi_') . '.json');
                file_put_contents($abiPath, $property->contract_abi);

                $tokenAmount = bcmul((string) $investment->qtd_tokens, '1000000000000000000');
                $process = new Process([
                    'node',
                    base_path('scripts/transfer_token.js'),
                    $property->contract_address,
                    $abiPath,
                    $privKey,
                    $investor->carteira_blockchain,
                    $tokenAmount,
                ]);
                $process->run();
                if ($process->isSuccessful()) {
                    $out = json_decode($process->getOutput(), true);
                    $txHash = $out['txHash'] ?? null;
                } else {
                    LogTransacaoHelper::registrar(
                        'purchase_transfer_error',
                        ['error' => $process->getErrorOutput()],
                        auth('investor')->user(),
                        $property->id
                    );
                }
                @unlink($abiPath);
            } catch (\Exception $e) {
                LogTransacaoHelper::registrar(
                    'purchase_transfer_error',
                    ['error' => $e->getMessage()],
                    auth('investor')->user(),
                    $property->id
                );
            }
        }

        if ($txHash) {
            LogTransacaoHelper::registrar(
                'purchase_transfer',
                ['txHash' => $txHash, 'investment_id' => $investment->id],
                auth('investor')->user(),
                $property->id
            );
        }

        return response()->json($investment);
    }

    /**
     * Histórico de investimentos do usuário autenticado.
     *
     * @OA\Get(
     *     path="/api/investments/history",
     *     tags={"Investments"},
     *     security={{"sanctum":{}}},
     *     summary="Histórico de investimentos",
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function index()
    {
        $investments = Investment::with('property')->get();

        $response = $investments->map(function ($investment) {
            $property = $investment->property;

            return [
                'id' => $investment->id,
                'title' => $property->title,
                'description' => $property->description,
                'location' => $property->location,
                'imageUrl' => $property->image_url,
                'guaranteeLevelPercentage' => $property->guarantee_level_percentage,
                'status' => $investment->status,
                'tokensAvailable' => $property->qtd_tokens,
                'tokensOriginal' => $property->qtd_tokens_original,
                'totalValue' => $property->valor_total,
                'tokenizationDate' => $property->data_tokenizacao,
            ];
        });

        return response()->json($response);
    }


    public function show($id)
    {
        $investment = Investment::with('property', 'investor')->findOrFail($id);
        $property = $investment->property;

        return response()->json([
            'id' => $investment->id,
            'userId' => $investment->id_investidor,
            'title' => $property->title,
            'description' => $property->description,
            'location' => $property->location,
            'totalValue' => $property->valor_total,
            'tokensAvailable' => $property->qtd_tokens,
            'tokensOriginal' => $property->qtd_tokens_original,
            'smartModelId' => $property->smart_model_id,
            'contractModelId' => $property->contract_model_id,
            'status' => $investment->status,
            'tokenizationDate' => $property->data_tokenizacao,
            'createdAt' => $investment->created_at,
            'updatedAt' => $investment->updated_at,
            'contractAddress' => $property->contract_address,
            'tokenSymbol' => $property->token_symbol,
            'tokenName' => $property->token_name,
            'contractAbi' => $property->contract_abi,
            'totalSupply' => $property->total_supply,
            'imageUrl' => $property->image_url,
            'guaranteeLevelPercentage' => $property->guarantee_level_percentage,
            'debtor' => $property->devedor ?? 'N/A',
            'amortizationFlow' => $property->fluxo_amortizacao ?? 'N/A',
            'collateral' => $property->colateral ?? 'N/A',
            'minimumValue' => $property->valor_minimo ?? 1000.00,
            'investmentProgress' => 0.00, // Pode calcular baseado em tokens vendidos / total
            'schedule' => $property->cronograma ?? 'Disponível no contrato',
            'guarantee' => $property->garantia ?? 'Imóveis, Recebíveis, Cotas e Aval',
        ]);
    }


    public function history()
    {
        return response()->json([]);
    }
}
