<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index()
    {
        return response()->json([]);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'Ticket created'], 201);
    }

    public function show($id)
    {
        return response()->json(['id' => (int) $id]);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'Ticket updated']);
    }

    public function destroy($id)
    {
        return response()->json(['message' => 'Ticket deleted']);
    }
}
