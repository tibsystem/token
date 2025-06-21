<?php

namespace App\Http\Controllers;

use App\Models\TransacaoFinanceira;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransacaoFinanceiraController extends Controller
{
    public function index()
    {
        return response()->json(TransacaoFinanceira::all());
    }

    public function show($id)
    {
        $transacao = TransacaoFinanceira::findOrFail($id);
        return response()->json($transacao);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_investidor' => 'required|integer|exists:investors,id',
            'tipo' => 'required|in:deposito,saque,rendimento,taxa',
            'valor' => 'required|numeric',
            'status' => 'in:pendente,concluido,falhou',
            'referencia' => 'nullable|string',
            'data_transacao' => 'required|date',
        ]);
        $data['id'] = (string) Str::uuid();
        $transacao = TransacaoFinanceira::create($data);
        return response()->json($transacao, 201);
    }

    public function update(Request $request, $id)
    {
        $transacao = TransacaoFinanceira::findOrFail($id);
        $data = $request->validate([
            'id_investidor' => 'sometimes|integer|exists:investors,id',
            'tipo' => 'sometimes|in:deposito,saque,rendimento,taxa',
            'valor' => 'sometimes|numeric',
            'status' => 'sometimes|in:pendente,concluido,falhou',
            'referencia' => 'nullable|string',
            'data_transacao' => 'sometimes|date',
        ]);
        $transacao->update($data);
        return response()->json($transacao);
    }

    public function destroy($id)
    {
        $transacao = TransacaoFinanceira::findOrFail($id);
        $transacao->delete();
        return response()->json(['deleted' => true]);
    }
}
