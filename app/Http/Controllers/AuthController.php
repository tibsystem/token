<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Investor;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;


/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Autenticação de usuários"
 * )
 */
class AuthController extends Controller
{

    /**
     * Autenticar usuário e obter token JWT.
     *
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Auth"},
     *     summary="Login de usuário",
     *     @OA\RequestBody(required=true, @OA\JsonContent()),
     *     @OA\Response(response=200, description="Sucesso"),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function login(Request $request)
{
    $credentials = $request->only(['email', 'password']);

    // Tenta autenticar diretamente
    if (!$token = Auth::guard('api')->attempt($credentials)) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    return response()->json([
        'token' => $token,
        'user' => Auth::guard('api')->user()
    ]);
}

    /**
     * Registrar novo usuário.
     *
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"Auth"},
     *     summary="Registrar usuário",
     *     @OA\RequestBody(required=true, @OA\JsonContent()),
     *     @OA\Response(response=201, description="Criado")
     * )
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'tipo' => 'sometimes|string|in:investidor,admin,compliance,suporte',
            'telefone' => 'sometimes|nullable|string|max:30',
        ]);

        $user = User::create([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']), // ← CORRETO
            'tipo' => $data['tipo'] ?? 'investidor',
            'telefone' => $data['telefone'] ?? null,
        ]);

        return response()->json($user, 201);
    }

    /**
     * Login específico para investidores.
     *
     * @OA\Post(
     *     path="/api/auth/investor-login",
     *     tags={"Auth"},
     *     summary="Login de investidor",
     *     @OA\RequestBody(required=true, @OA\JsonContent()),
     *     @OA\Response(response=200, description="Sucesso"),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function loginInvestidor(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'senha' => 'required|string'
        ]);

        $investidor = Investor::where('email', $data['email'])->first();

        if (!$investidor || !Hash::check($data['senha'], $investidor->senha_hash)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $token = $investidor->createToken('investidor_token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso',
            'token' => $token,
            'investidor' => $investidor
        ]);
    }
}
