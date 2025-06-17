<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (!$token = auth('api')->attempt([
            'email' => $credentials['email'],
            'senha_hash' => hash('sha256', $credentials['password'])
        ])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'token' => $token,
            'user' => auth('api')->user()
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = \App\Models\User::create([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'senha_hash' => hash('sha256', $data['password']),
        ]);

        return response()->json($user, 201);
    }
}
