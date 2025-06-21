<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Wallet",
 *     description="Operações da carteira do investidor"
 * )
 */
class WalletController extends Controller
{
    /**
     * Exibe saldo da carteira.
     *
     * @OA\Get(
     *     path="/api/wallet",
     *     tags={"Wallet"},
     *     security={{"sanctum":{}}},
     *     summary="Consultar saldo",
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function show()
    {
        return response()->json(['balance' => 0]);
    }

    /**
     * Adicionar fundos à carteira.
     *
     * @OA\Post(
     *     path="/api/wallet/add-funds",
     *     tags={"Wallet"},
     *     security={{"sanctum":{}}},
     *     summary="Adicionar fundos",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"valor"},
     *             @OA\Property(property="valor", type="number", example=500)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function addFunds(Request $request)
    {
        return response()->json(['message' => 'Funds added']);
    }

    /**
     * Solicitar saque de fundos.
     *
     * @OA\Post(
     *     path="/api/wallet/withdraw",
     *     tags={"Wallet"},
     *     security={{"sanctum":{}}},
     *     summary="Sacar fundos",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"valor"},
     *             @OA\Property(property="valor", type="number", example=200)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function withdraw(Request $request)
    {
        return response()->json(['message' => 'Withdraw processed']);
    }
}
