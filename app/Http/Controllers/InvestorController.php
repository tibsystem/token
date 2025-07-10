<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\CarteiraInterna;
use App\Models\Participant;
use App\Helpers\WalletHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Investors",
 *     description="Gerenciamento de investidores"
 * )
 */
class InvestorController extends Controller
{
    /**
     * Lista todos os investidores.
     *
     * @OA\Get(
     *     path="/api/investors",
     *     tags={"Investors"},
     *     security={{"sanctum":{}}},
     *     summary="Obter lista de investidores",
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function index()
    {
        return response()->json(Investor::all());
    }

    /**
     * Exibe um investidor específico.
     *
     * @OA\Get(
     *     path="/api/investors/{id}",
     *     tags={"Investors"},
     *     security={{"sanctum":{}}},
     *     summary="Detalhes do investidor",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Sucesso"),
     *     @OA\Response(response=404, description="Não encontrado")
     * )
     */
    public function show($id)
    {
        $investor = Investor::findOrFail($id);

        return response()->json($investor);
    }

    /**
     * Atualiza um investidor.
     *
     * @OA\Put(
     *     path="/api/investors/{id}",
     *     tags={"Investors"},
     *     security={{"sanctum":{}}},
     *     summary="Atualizar investidor",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome","email","documento","senha"},
     *             @OA\Property(property="nome", type="string", example="Maria"),
     *             @OA\Property(property="email", type="string", example="maria@example.com"),
     *             @OA\Property(property="documento", type="string", example="12345678900"),
     *             @OA\Property(property="telefone", type="string", example="11888887777"),
     *             @OA\Property(property="senha", type="string", example="senhaSegura"),
     *             @OA\Property(property="status_kyc", type="string", example="pendente"),
     *             @OA\Property(property="carteira_blockchain", type="string", example="0xABC123")
     *         )
     *     ),

     *     @OA\Response(response=200, description="Atualizado")
     * )
     */
    public function update(Request $request, $id)
    {
        $investor = Investor::findOrFail($id);

        $data = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:investors,email,' . $investor->id,
            'documento' => 'sometimes|required|string|max:50',
            'telefone' => 'nullable|string|max:30',
            'status_kyc' => 'in:pendente,aprovado,rejeitado',
            'carteira_blockchain' => 'nullable|string|max:255',
        ]);

        $investor->update($data);

        return response()->json($investor);
    }

    /**
     * Remove um investidor.
     *
     * @OA\Delete(
     *     path="/api/investors/{id}",
     *     tags={"Investors"},
     *     security={{"sanctum":{}}},
     *     summary="Excluir investidor",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Excluído")
     * )
     */
    public function destroy($id)
    {
        $investor = Investor::findOrFail($id);
        $investor->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * Cria um novo investidor.
     *
     * @OA\Post(
     *     path="/api/investors",
     *     tags={"Investors"},
     *     summary="Cadastrar investidor",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome","email","documento","senha"},
     *             @OA\Property(property="nome", type="string", example="Maria"),
     *             @OA\Property(property="email", type="string", example="maria@example.com"),
     *             @OA\Property(property="documento", type="string", example="12345678900"),
     *             @OA\Property(property="telefone", type="string", example="11888887777"),
     *             @OA\Property(property="senha", type="string", example="senhaSegura"),
     *             @OA\Property(property="status_kyc", type="string", example="pendente"),
     *             @OA\Property(property="carteira_blockchain", type="string", example="0xABC123")
     *         )
     *     ),

     *     @OA\Response(response=201, description="Criado")
     * )
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:investors,email',
            'document' => 'required|string|max:50',
            'phone' => 'nullable|string|max:30',
            'password' => 'required_without:participants|string|min:6',
            'status_kyc' => 'in:pending,approved,rejected',
            'participants' => 'nullable|array',
            'participants.*.name' => 'required_with:participants|string|max:255',
            'participants.*.email' => 'required_with:participants|email|unique:users,email',
            'participants.*.password' => 'required_with:participants|string',
            'participants.*.document' => 'required_with:participants|string',
        ]);

        $type = !empty($data['participants']) ? 'pj' : 'pf';

        $wallet = WalletHelper::generatePolygonWallet();


        $investor = Investor::create([
            'name' => $data['name'],
            'email' => isset($data['email']) ? $data['email'] : null,
            'document' => $data['document'],
            'phone' => $data['phone'],
            'password' => isset($data['password']) ? $data['password'] : null,
            'status_kyc' => $data['status_kyc'] ?? 'pending',
            'wallet_blockchain' => $wallet['address'],
            'wallet_private_key' => Crypt::encryptString($wallet['private_key']),
            'type' => $type,
        ]);

        CarteiraInterna::create([
            'id_investidor' => $investor->id,
            'endereco_wallet' => $wallet['address'],
            'saldo_disponivel' => 0,
            'saldo_bloqueado' => 0,
            'saldo_tokenizado' => [],
        ]);

        if (!empty($data['participants'])) {
            foreach ($data['participants'] as $p) {
                Participant::create([
                    'investor_id' => $investor->id,
                    'name' => $p['name'],
                    'email' => $p['email'],
                    'document' => $p['document'],
                    'password' => $p['password'],
                ]);
            }
        }
        return response()->json($investor, 201);
    }
}
