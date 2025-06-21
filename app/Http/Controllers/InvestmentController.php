<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Investment;
use App\Models\TransacaoFinanceira;
use App\Models\CarteiraInterna;
use Illuminate\Support\Str;

class InvestmentController extends Controller
{
    public function purchase(Request $request)
    {
        $data = $request->validate([
            'id_investidor' => 'required|integer|exists:investors,id',
            'id_imovel' => 'required|integer|exists:properties,id',
            'qtd_tokens' => 'required|integer|min:1',
            'valor_unitario' => 'required|numeric',
            'data_compra' => 'required|date',
            'origem' => 'required|in:plataforma,p2p',
            'status' => 'in:ativo,inativo',
        ]);

        $investment = Investment::create($data);

        if ($data['origem'] === 'plataforma') {
            $valorTotal = $data['qtd_tokens'] * $data['valor_unitario'];


            TransacaoFinanceira::create([
                'id' => (string) Str::uuid(),
                'id_investidor' => $data['id_investidor'],
                'tipo' => 'compra_token',

                'valor' => $data['qtd_tokens'] * $data['valor_unitario'],
                'status' => 'concluido',
                'referencia' => 'investimento:' . $investment->id,
                'data_transacao' => $data['data_compra'],
            ]);

            $carteira = CarteiraInterna::where('id_investidor', $data['id_investidor'])->first();
            if ($carteira) {
                $carteira->saldo_disponivel -= $valorTotal;
                $carteira->save();
            }

        }

        return response()->json($investment);
    }

    public function history()
    {
        return response()->json([]);
    }
}
