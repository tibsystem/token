<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\P2PListing;
use App\Models\Investment;
use App\Http\Resources\P2PListingResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="P2P Listings",
 *     description="Listagens de tokens para venda P2P"
 * )
 */
class P2PListingController extends Controller
{
    /**
     * Listar ofertas P2P ativas.
     *
     * @OA\Get(
     *     path="/api/p2p/listings",
     *     tags={"P2P Listings"},
     *     security={{"sanctum":{}}},
     *     summary="Listar ofertas",
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function index()
    {
        return P2PListingResource::collection(
            P2PListing::where('status', 'ativa')->get()
        );
    }

    /**
     * Criar nova oferta P2P.
     *
     * @OA\Post(
     *     path="/api/p2p/listings",
     *     tags={"P2P Listings"},
     *     security={{"sanctum":{}}},
     *     summary="Criar oferta",
     *     @OA\RequestBody(required=true, @OA\JsonContent()),
     *     @OA\Response(response=201, description="Criado")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'vendedor_id' => 'required|integer|exists:investors,id',
            'id_imovel' => 'required|integer|exists:properties,id',
            'qtd_tokens' => 'required|integer|min:1',
            'valor_unitario' => 'required|numeric',
        ]);

        $investment = Investment::where('id_investidor', $data['vendedor_id'])
            ->where('id_imovel', $data['id_imovel'])
            ->first();

        if (!$investment || $investment->qtd_tokens < $data['qtd_tokens']) {
            return response()->json(['message' => 'Tokens insuficientes'], 400);
        }

        $listing = P2PListing::create($data);
        return new P2PListingResource($listing);
    }

    /**
     * Cancelar uma oferta P2P.
     *
     * @OA\Delete(
     *     path="/api/p2p/listings/{id}",
     *     tags={"P2P Listings"},
     *     security={{"sanctum":{}}},
     *     summary="Cancelar oferta",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function destroy($id)
    {
        $listing = P2PListing::findOrFail($id);
        $listing->status = 'cancelada';
        $listing->save();
        return new P2PListingResource($listing);
    }
}
