<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Support Tickets",
 *     description="Atendimento de suporte"
 * )
 */
class SupportTicketController extends Controller
{
    /**
     * Listar tickets de suporte.
     *
     * @OA\Get(
     *     path="/api/support-tickets",
     *     tags={"Support Tickets"},
     *     security={{"sanctum":{}}},
     *     summary="Listar tickets",
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function index()
    {
        return response()->json([]);
    }

    /**
     * Abrir novo ticket de suporte.
     *
     * @OA\Post(
     *     path="/api/support-tickets",
     *     tags={"Support Tickets"},
     *     security={{"sanctum":{}}},
     *     summary="Criar ticket",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"assunto","mensagem"},
     *             @OA\Property(property="assunto", type="string", example="Problema no pagamento"),
     *             @OA\Property(property="mensagem", type="string", example="Detalhes do problema...")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Criado")
     * )
     */
    public function store(Request $request)
    {
        return response()->json(['message' => 'Ticket created'], 201);
    }

    /**
     * Exibir ticket.
     *
     * @OA\Get(
     *     path="/api/support-tickets/{id}",
     *     tags={"Support Tickets"},
     *     security={{"sanctum":{}}},
     *     summary="Mostrar ticket",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function show($id)
    {
        return response()->json(['id' => (int) $id]);
    }

    /**
     * Atualizar ticket.
     *
     * @OA\Put(
     *     path="/api/support-tickets/{id}",
     *     tags={"Support Tickets"},
     *     security={{"sanctum":{}}},
     *     summary="Atualizar ticket",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent()),
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'Ticket updated']);
    }

    /**
     * Excluir ticket.
     *
     * @OA\Delete(
     *     path="/api/support-tickets/{id}",
     *     tags={"Support Tickets"},
     *     security={{"sanctum":{}}},
     *     summary="Excluir ticket",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function destroy($id)
    {
        return response()->json(['message' => 'Ticket deleted']);
    }
}
