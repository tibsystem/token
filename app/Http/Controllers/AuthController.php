<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
{
    $credentials = $request->only(['email', 'password']);
    if (!$token = auth('api')->attempt(['email' => $credentials['email'], 'senha_hash' => hash('sha256', $credentials['password'])])) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    return response()->json([
        'token' => $token,
        'user' => auth('api')->user()
    ]);
}

}
