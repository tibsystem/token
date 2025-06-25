<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Properties",
 *     description="Gerenciamento de imóveis"
 * )
 */
class PropertyController extends Controller
{
    /**
     * Listar imóveis cadastrados.
     *
     * @OA\Get(
     *     path="/api/properties",
     *     tags={"Properties"},
     *     security={{"sanctum":{}}},
     *     summary="Listar imóveis",
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function index()
    {
        return response()->json(Property::all());
    }

    /**
     * Exibir detalhes de um imóvel.
     *
     * @OA\Get(
     *     path="/api/properties/{id}",
     *     tags={"Properties"},
     *     security={{"sanctum":{}}},
     *     summary="Detalhes do imóvel",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function show($id)
    {
        $property = Property::findOrFail($id);
        return response()->json($property);
    }

    /**
     * Atualizar dados de um imóvel.
     *
     * @OA\Put(
     *     path="/api/properties/{id}",
     *     tags={"Properties"},
     *     security={{"sanctum":{}}},
     *     summary="Atualizar imóvel",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"titulo","localizacao","valor_total","qtd_tokens","status"},
     *             @OA\Property(property="titulo", type="string", example="Apartamento Vista Mar"),
     *             @OA\Property(property="descricao", type="string", example="Descrição do imóvel"),
     *             @OA\Property(property="localizacao", type="string", example="Rio de Janeiro"),
     *             @OA\Property(property="valor_total", type="number", example=500000),
     *             @OA\Property(property="qtd_tokens", type="integer", example=10000),
     *             @OA\Property(property="status", type="string", example="ativo"),
     *             @OA\Property(property="data_tokenizacao", type="string", example="2024-05-20")
     *         )
     *     ),

     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);
        $data = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'descricao' => 'nullable|string',
            'localizacao' => 'sometimes|required|string|max:255',
            'valor_total' => 'sometimes|required|numeric',
            'qtd_tokens' => 'sometimes|required|integer',
            'modelo_smart_id' => 'nullable|integer',
            'status' => 'sometimes|required|in:ativo,vendido,oculto',
            'data_tokenizacao' => 'nullable|date',
        ]);
        $property->update($data);

        return response()->json($property);
    }

    /**
     * Remover um imóvel.
     *
     * @OA\Delete(
     *     path="/api/properties/{id}",
     *     tags={"Properties"},
     *     security={{"sanctum":{}}},
     *     summary="Excluir imóvel",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * Cadastrar novo imóvel.
     *
     * @OA\Post(
     *     path="/api/properties",
     *     tags={"Properties"},
     *     security={{"sanctum":{}}},
     *     summary="Criar imóvel",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"titulo","localizacao","valor_total","qtd_tokens","status"},
     *             @OA\Property(property="titulo", type="string", example="Apartamento Vista Mar"),
     *             @OA\Property(property="descricao", type="string", example="Descrição do imóvel"),
     *             @OA\Property(property="localizacao", type="string", example="Rio de Janeiro"),
     *             @OA\Property(property="valor_total", type="number", example=500000),
     *             @OA\Property(property="qtd_tokens", type="integer", example=10000),
     *             @OA\Property(property="status", type="string", example="ativo"),
     *             @OA\Property(property="data_tokenizacao", type="string", example="2024-05-20")
     *         )
     *     ),

     *     @OA\Response(response=201, description="Criado")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'localizacao' => 'required|string|max:255',
            'valor_total' => 'required|numeric',
            'qtd_tokens' => 'required|integer',
            'modelo_smart_id' => 'nullable|integer',
            'status' => 'required|in:ativo,vendido,oculto',
            'data_tokenizacao' => 'nullable|date',
        ]);

        $property = $request->user()->properties()->create(
            $data + ['qtd_tokens_original' => $request->qtd_tokens]
        );

        return response()->json($property, 201);
    }

    /**
     * Listar tokens associados ao imóvel.
     *
     * @OA\Get(
     *     path="/api/properties/{id}/tokens",
     *     tags={"Properties"},
     *     security={{"sanctum":{}}},
     *     summary="Tokens do imóvel",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function tokens($id)
    {
        return response()->json([
            'property_id' => (int) $id,
            'tokens' => []
        ]);
    }

    /**
     * Tokenizar imóvel e implantar contrato na Polygon.
     */
    public function tokenize(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        if ($property->contract_address) {
            return response()->json(['message' => 'Property already tokenized'], 400);
        }

        $data = $request->validate([
            'contract_model_id' => 'required|exists:smart_contract_models,id',
            'token_name' => 'required|string',
            'token_symbol' => 'required|string',
            'total_supply' => 'required|integer'
        ]);

        $exitCode = Artisan::call('deploy:property', [
            'propertyId' => $property->id,
            '--model' => $data['contract_model_id'],
            '--name' => $data['token_name'],
            '--symbol' => $data['token_symbol'],
            '--supply' => $data['total_supply']
        ]);

        $property->refresh();
        if ($exitCode !== 0 || !$property->contract_address) {
            return response()->json([
                'message' => 'Tokenization failed'
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Tokenization complete',
            'contract_address' => $property->contract_address,
            'token_symbol' => $property->token_symbol,
            'token_name' => $property->token_name,
        ]);
    }
}
