<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;


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
            'password' => hash('sha256', $data['password']),
            'tipo' => $data['tipo'] ?? 'investidor',
            'telefone' => $data['telefone'] ?? null,
        ]);

        return response()->json($user, 201);
    }
}
