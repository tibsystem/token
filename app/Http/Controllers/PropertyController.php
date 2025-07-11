<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
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
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'sometimes|required|string|max:255',
            'total_value' => 'sometimes|required|numeric',
            'total_tokens' => 'sometimes|required|integer',
            'smart_contract_model_id' => 'nullable|integer',
            'status' => 'sometimes|required|in:active,sold,pending,hidden',
            'tokenization_date' => 'nullable|date',
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'total_value' => 'required|numeric',
            'total_tokens' => 'required|integer',
            'smart_contract_model_id' => 'required|integer',
            'status' => 'nullable|in:active,sold,pending,hidden',
            'tokenization_date' => 'nullable|date',
            'attachments' => 'nullable|array',
            'attachments.*.description' => 'required_with:attachments.*.file|string',
            'attachments.*.file' => [
                'required_with:attachments.*.description',
                'string',
                'regex:/^data:(application\/pdf|application\/msword|application\/vnd\.openxmlformats-officedocument\.wordprocessingml\.document);base64,/'
            ],
            'attachments.*.name' => 'nullable|string',
            'attachments.*.size' => 'nullable|integer',
            'attachments.*.type' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*' => 'string|starts_with:data:image/',
        ]);

        $property = $request->user()->properties()->create(
            $data + ['original_total_tokens' => $data['total_tokens']]
        );

        \App\Models\PropertyWallet::create([
            'property_id' => $property->id,
            'saldo_disponivel' => 0,
            'saldo_bloqueado' => 0,
        ]);

        if (!empty($data['files'])) {
            foreach ($data['files'] as $fileBase64) {
                try {
                    if (!str_contains($fileBase64, ',')) continue;

                    [$meta, $base64] = explode(',', $fileBase64, 2);
                    if (!preg_match('/^data:image\/(\w+);base64$/', $meta, $matches)) continue;

                    $extension = strtolower($matches[1]);
                    $fileName = Str::random(10) . '_' . date('dmY') . '.' . $extension;
                    $path = 'properties/' . $property->id . '/' . $fileName;

                    Storage::put($path, base64_decode($base64));
                    $size = Storage::size($path);
                    Storage::setVisibility($path, 'public');
                    $url = Storage::url($path);

                    $property->files()->create([
                        'name' => 'Property image - ' . $property->title,
                        'path' => $url,
                        'type_file' => 'property_photo',
                        'size' => $size,
                    ]);
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }
            }
        }
        if (!empty($data['attachments'])) {
            foreach ($data['attachments'] as $attachment) {
                try {
                    [$meta, $base64] = explode(',', $attachment['file'], 2);

                    preg_match('/^data:(.*);base64$/', $meta, $matches);
                    $mime = $matches[1] ?? 'application/octet-stream';

                    switch ($mime) {
                        case 'application/pdf':
                            $extension = 'pdf';
                            break;
                        case 'application/msword':
                            $extension = 'doc';
                            break;
                        case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                            $extension = 'docx';
                            break;
                        default:
                            return response()->json(['error' => 'Unsupported file type'], 422);
                    }

                    $fileName = $attachment['name'] ?? Str::random(10) . '_' . date('dmY') . '.' . $extension;
                    $path = 'properties/' . $property->id . '/attachments/' . $fileName;

                    Storage::put($path, base64_decode($base64));
                    $size = Storage::size($path);
                    Storage::setVisibility($path, 'public');
                    $url = Storage::url($path);

                    $property->files()->create([
                        'name' => $fileName,
                        'description' => $attachment['description'],
                        'path' => $url,
                        'type_file' => 'attachment',
                        'size' => $size,
                        'mime_type' => $mime,
                    ]);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Failed to save attachment: ' . $e->getMessage()], 500);
                }
            }
        }

        return response()->json($property->load('files'), 201);
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
