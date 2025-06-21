<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Investor;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{

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
