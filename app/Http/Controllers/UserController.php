<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="Perfil do usuário autenticado"
 * )
 */
class UserController extends Controller
{
    /**
     * Recuperar dados do usuário logado.
     *
     * @OA\Get(
     *     path="/api/user/profile",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     summary="Perfil do usuário",
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function profile(Request $request)
    {
        return response()->json($request->user() ?? []);
    }
}
