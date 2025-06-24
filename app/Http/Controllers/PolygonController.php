<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Polygon",
 *     description="Consultas à blockchain Polygon"
 * )
 */
class PolygonController extends Controller
{
    /**
     * Retorna o saldo de uma carteira Polygon.
     *
     * @OA\Get(
     *     path="/api/polygon/balance/{address}",
     *     tags={"Polygon"},
     *     summary="Consultar saldo da carteira Polygon",
     *
     *     @OA\Parameter(name="address", in="path", required=true, @OA\Schema(type="string")),
     *
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function balance(string $address)
    {
        $apiKey = config('services.polygonscan.key');

        $response = Http::get('https://api.polygonscan.com/api', [
            'module' => 'account',
            'action' => 'balance',
            'address' => $address,
            'tag' => 'latest',
            'apikey' => $apiKey,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Unable to fetch balance'], 500);
        }

        $data = $response->json();
        $balanceWei = $data['result'] ?? '0';
        $balanceMatic = (float) $balanceWei / 1e18;

        return response()->json([
            'address' => $address,
            'balance' => $balanceMatic,
        ]);
    }
}
