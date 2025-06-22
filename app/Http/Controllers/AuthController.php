<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="secret")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="abc123"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),

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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome","email","password"},
     *             @OA\Property(property="nome", type="string", example="João"),
     *             @OA\Property(property="email", type="string", example="joao@example.com"),
     *             @OA\Property(property="password", type="string", example="secret"),
     *             @OA\Property(property="tipo", type="string", example="investidor"),
     *             @OA\Property(property="telefone", type="string", example="11999998888")
     *         )
     *     ),


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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","senha"},
     *             @OA\Property(property="email", type="string", example="invest@example.com"),
     *             @OA\Property(property="senha", type="string", example="senhaSegura")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="abc123"),
     *             @OA\Property(property="investidor", type="object")
     *         )
     *     ),

     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function loginInvestidor(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'senha' => 'required|string'
        ]);

        $credentials = ['email' => $data['email'], 'password' => $data['senha']];

        if (! $token = Auth::guard('investor')->attempt($credentials)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        return response()->json([
            'message' => 'Login realizado com sucesso',
            'token' => $token,
            'investidor' => Auth::guard('investor')->user(),
        ]);
    }
}
