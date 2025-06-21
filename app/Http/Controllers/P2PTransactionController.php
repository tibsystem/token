<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\P2PListing;
use App\Models\TransacaoToken;
use App\Models\Investment;
use App\Models\CarteiraInterna;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="P2P Transactions",
 *     description="Transações de compra e venda P2P"
 * )
 */
class P2PTransactionController extends Controller
{
    /**
     * Listar transações P2P.
     *
     * @OA\Get(
     *     path="/api/p2p/transactions",
     *     tags={"P2P Transactions"},
     *     security={{"sanctum":{}}},
     *     summary="Listar transações",
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function index(Request $request)
    {
        $id = $request->query('investidor_id');
        $query = TransacaoToken::query();
        if ($id) {
            $query->where(function ($q) use ($id) {
                $q->where('vendedor_id', $id)->orWhere('comprador_id', $id);
            });
        }
        return response()->json($query->get());
    }

    /**
     * Realizar transação de compra de oferta P2P.
     *
     * @OA\Post(
     *     path="/api/p2p/transactions",
     *     tags={"P2P Transactions"},
     *     security={{"sanctum":{}}},
     *     summary="Comprar oferta",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"listing_id","comprador_id"},
     *             @OA\Property(property="listing_id", type="integer", example=5),
     *             @OA\Property(property="comprador_id", type="integer", example=2)
     *         )
     *     ),

     *     @OA\Response(response=201, description="Criada")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'listing_id' => 'required|integer|exists:p2p_listings,id',
            'comprador_id' => 'required|integer|exists:investors,id',
        ]);

        $listing = P2PListing::findOrFail($data['listing_id']);

        if ($listing->status !== 'ativa') {
            return response()->json(['message' => 'Oferta indisponivel'], 400);
        }

        $total = $listing->qtd_tokens * $listing->valor_unitario;

        $carteiraComprador = CarteiraInterna::where('id_investidor', $data['comprador_id'])->first();
        $carteiraVendedor = CarteiraInterna::where('id_investidor', $listing->vendedor_id)->first();

        if (!$carteiraComprador || $carteiraComprador->saldo_disponivel < $total) {
            return response()->json(['message' => 'Saldo insuficiente'], 400);
        }

        $invVend = Investment::where('id_investidor', $listing->vendedor_id)
            ->where('id_imovel', $listing->id_imovel)
            ->first();

        if (!$invVend || $invVend->qtd_tokens < $listing->qtd_tokens) {
            return response()->json(['message' => 'Vendedor sem tokens'], 400);
        }

        $invCompr = Investment::firstOrCreate(
            [
                'id_investidor' => $data['comprador_id'],
                'id_imovel' => $listing->id_imovel,
            ],
            [
                'valor_unitario' => $listing->valor_unitario,
                'qtd_tokens' => 0,
                'data_compra' => now(),
                'origem' => 'p2p',
                'status' => 'ativo',
            ]
        );

        $invVend->qtd_tokens -= $listing->qtd_tokens;
        $invVend->save();

        $invCompr->qtd_tokens += $listing->qtd_tokens;
        $invCompr->save();

        $carteiraComprador->saldo_disponivel -= $total;
        $carteiraVendedor->saldo_disponivel += $total;
        $carteiraComprador->save();
        $carteiraVendedor->save();

        $listing->status = 'concluida';
        $listing->save();

        $transacao = TransacaoToken::create([
            'id' => (string) Str::uuid(),
            'vendedor_id' => $listing->vendedor_id,
            'comprador_id' => $data['comprador_id'],
            'id_imovel' => $listing->id_imovel,
            'qtd_tokens' => $listing->qtd_tokens,
            'valor_unitario' => $listing->valor_unitario,
            'data_transacao' => now(),
            'status' => 'concluida',
        ]);

        return response()->json($transacao, 201);
    }
}
