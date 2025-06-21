<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    public function index()
    {
        return response()->json(Investor::all());
    }

    public function show($id)
    {
        $investor = Investor::findOrFail($id);

        return response()->json($investor);
    }

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

    public function destroy($id)
    {
        $investor = Investor::findOrFail($id);
        $investor->delete();

        return response()->json(['deleted' => true]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:investors,email',
            'documento' => 'required|string|max:50',
            'telefone' => 'nullable|string|max:30',
            'status_kyc' => 'in:pendente,aprovado,rejeitado',
            'carteira_blockchain' => 'nullable|string|max:255',
        ]);

        $investor = Investor::create($data);

        return response()->json($investor, 201);
    }
}
