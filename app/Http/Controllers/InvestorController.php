<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\CarteiraInterna;
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
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:investors,email',
            'documento' => 'required|string|max:50',
            'telefone' => 'nullable|string|max:30',
            'senha' => 'required|string|min:6',
            'status_kyc' => 'in:pendente,aprovado,rejeitado',
        ]);

        $wallet = $this->generatePolygonWallet();

        $investor = Investor::create([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'documento' => $data['documento'],
            'telefone' => $data['telefone'],
            'senha_hash' => Hash::make($data['senha']),
            'status_kyc' => $data['status_kyc'] ?? 'pendente',
            'carteira_blockchain' => $wallet['address'],
            'carteira_private_key' => Crypt::encryptString($wallet['private_key']),
        ]);

        CarteiraInterna::create([
            'id_investidor' => $investor->id,
            'endereco_wallet' => $wallet['address'],
            'saldo_disponivel' => 0,
            'saldo_bloqueado' => 0,
            'saldo_tokenizado' => [],
        ]);

        return response()->json($investor, 201);
    }

    private function generatePolygonWallet(): array
    {
        $config = [
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'secp256k1',
        ];
        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privPem);
        $details = openssl_pkey_get_details($res);

        $d = $details['ec']['d'] ?? '';
        $x = $details['ec']['x'] ?? '';
        $y = $details['ec']['y'] ?? '';
        $privKey = bin2hex($d);
        $pubKey = '04' . bin2hex($x) . bin2hex($y);
        $hash = hash('sha3-256', hex2bin($pubKey));
        $address = '0x' . substr($hash, -40);

        return [
            'address' => $address,
            'private_key' => $privKey,
        ];
    }
}
