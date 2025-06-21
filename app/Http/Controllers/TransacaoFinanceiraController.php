<?php

namespace App\Http\Controllers;

use App\Models\TransacaoFinanceira;
use App\Models\CarteiraInterna;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Financial Transactions",
 *     description="Movimentações financeiras"
 * )
 */
class TransacaoFinanceiraController extends Controller
{
    /**
     * Listar transações financeiras.
     *
     * @OA\Get(
     *     path="/api/transacoes-financeiras",
     *     tags={"Financial Transactions"},
     *     security={{"sanctum":{}}},
     *     summary="Listar transações",
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function index()
    {
        return response()->json(TransacaoFinanceira::all());
    }

    /**
     * Mostrar uma transação específica.
     *
     * @OA\Get(
     *     path="/api/transacoes-financeiras/{id}",
     *     tags={"Financial Transactions"},
     *     security={{"sanctum":{}}},
     *     summary="Detalhes da transação",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function show($id)
    {
        $transacao = TransacaoFinanceira::findOrFail($id);
        return response()->json($transacao);
    }

    /**
     * Criar nova transação financeira.
     *
     * @OA\Post(
     *     path="/api/transacoes-financeiras",
     *     tags={"Financial Transactions"},
     *     security={{"sanctum":{}}},
     *     summary="Registrar transação",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id_investidor","tipo","valor","data_transacao"},
     *             @OA\Property(property="id_investidor", type="integer", example=1),
     *             @OA\Property(property="tipo", type="string", example="deposito"),
     *             @OA\Property(property="valor", type="number", example=1000),
     *             @OA\Property(property="status", type="string", example="pendente"),
     *             @OA\Property(property="referencia", type="string", example="PIX"),
     *             @OA\Property(property="data_transacao", type="string", example="2024-01-01")
     *         )
     *     ),

     *     @OA\Response(response=201, description="Criada")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_investidor' => 'required|integer|exists:investors,id',
            'tipo' => 'required|in:deposito,saque,rendimento,taxa,compra_token',
            'valor' => 'required|numeric',
            'status' => 'in:pendente,concluido,falhou',
            'referencia' => 'nullable|string',
            'data_transacao' => 'required|date',
        ]);
        $data['id'] = (string) Str::uuid();
        $transacao = TransacaoFinanceira::create($data);

        $carteira = CarteiraInterna::where('id_investidor', $data['id_investidor'])->first();

        if ($carteira) {
            if ($data['tipo'] === 'deposito') {
                $carteira->saldo_disponivel += $data['valor'];
            } elseif (in_array($data['tipo'], ['saque', 'compra_token'])) {
                $carteira->saldo_disponivel -= $data['valor'];
            }

            $carteira->save();
        }

        return response()->json($transacao, 201);
    }

    /**
     * Atualizar transação financeira.
     *
     * @OA\Put(
     *     path="/api/transacoes-financeiras/{id}",
     *     tags={"Financial Transactions"},
     *     security={{"sanctum":{}}},
     *     summary="Atualizar transação",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id_investidor","tipo","valor","data_transacao"},
     *             @OA\Property(property="id_investidor", type="integer", example=1),
     *             @OA\Property(property="tipo", type="string", example="deposito"),
     *             @OA\Property(property="valor", type="number", example=500.00),
     *             @OA\Property(property="status", type="string", example="pendente"),
     *             @OA\Property(property="referencia", type="string", example="PIX"),
     *             @OA\Property(property="data_transacao", type="string", example="2024-01-01")
     *         )
     *     ),

     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function update(Request $request, $id)
    {
        $transacao = TransacaoFinanceira::findOrFail($id);
        $data = $request->validate([
            'id_investidor' => 'sometimes|integer|exists:investors,id',
            'tipo' => 'sometimes|in:deposito,saque,rendimento,taxa,compra_token',
            'valor' => 'sometimes|numeric',
            'status' => 'sometimes|in:pendente,concluido,falhou',
            'referencia' => 'nullable|string',
            'data_transacao' => 'sometimes|date',
        ]);
        $transacao->update($data);
        return response()->json($transacao);
    }

    /**
     * Remover transação financeira.
     *
     * @OA\Delete(
     *     path="/api/transacoes-financeiras/{id}",
     *     tags={"Financial Transactions"},
     *     security={{"sanctum":{}}},
     *     summary="Excluir transação",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function destroy($id)
    {
        $transacao = TransacaoFinanceira::findOrFail($id);
        $transacao->delete();
        return response()->json(['deleted' => true]);
    }
}
