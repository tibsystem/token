<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\CarteiraInterna;
use Illuminate\Support\Facades\Hash;
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
     *     @OA\RequestBody(required=true, @OA\JsonContent()),
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
     *     @OA\RequestBody(required=true, @OA\JsonContent()),
     *     @OA\Response(response=201, description="Criado")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:investors,email',
            'documento' => 'required|string|max:50',
            'telefone' => 'nullable|string|max:30',
            'senha' => 'required|string|min:6',
            'status_kyc' => 'in:pendente,aprovado,rejeitado',
            'carteira_blockchain' => 'nullable|string|max:255',
        ]);

        $investor = Investor::create([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'documento' => $data['documento'],
            'telefone' => $data['telefone'],
            'senha_hash' => Hash::make($data['senha']),
            'status_kyc' => $data['status_kyc'] ?? 'pendente',
            'carteira_blockchain' => $data['carteira_blockchain'] ?? null,
        ]);

        CarteiraInterna::create([
            'id_investidor' => $investor->id,
            'endereco_wallet' => $investor->carteira_blockchain,
            'saldo_disponivel' => 0,
            'saldo_bloqueado' => 0,
            'saldo_tokenizado' => [],
        ]);

        return response()->json($investor, 201);
    }
}
