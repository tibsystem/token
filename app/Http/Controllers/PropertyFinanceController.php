<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Investment;
use App\Models\TransacaoToken;
use Illuminate\Support\Facades\DB;

class PropertyFinanceController extends Controller
{
    public function report($id)
    {
        $property = Property::select(
            'id',
            'titulo',
            'localizacao',
            'valor_total',
            'status',
            'qtd_tokens_original',
            'qtd_tokens'
        )->find($id);

        if (!$property) {
            return response()->json(['message' => 'Imóvel não encontrado'], 404);
        }

        $tokensVendidos = $property->qtd_tokens_original - $property->qtd_tokens;

        $investors = Investment::where('id_imovel', $id)
            ->select('id_investidor', DB::raw('SUM(qtd_tokens) as total_tokens'))
            ->groupBy('id_investidor')
            ->with('investor:id,nome,email,documento')
            ->get()
            ->map(function ($inv) {
                return [
                    'id_investidor' => $inv->id_investidor,
                    'nome' => $inv->investor->nome ?? null,
                    'email' => $inv->investor->email ?? null,
                    'documento' => $inv->investor->documento ?? null,
                    'qtd_tokens' => $inv->total_tokens,
                ];
            });

        $investments = Investment::where('id_imovel', $id)
            ->with('investor:id,nome')
            ->get()
            ->map(function ($inv) {
                return [
                    'data_compra' => $inv->data_compra,
                    'id_investidor' => $inv->id_investidor,
                    'nome_investidor' => $inv->investor->nome ?? null,
                    'qtd_tokens' => $inv->qtd_tokens,
                    'valor_unitario' => $inv->valor_unitario,
                    'origem' => $inv->origem,
                    'status' => $inv->status,
                ];
            });

        $p2p = TransacaoToken::where('id_imovel', $id)
            ->with([
                'vendedor:id,nome,email',
                'comprador:id,nome,email'
            ])
            ->get()
            ->map(function ($t) {
                return [
                    'vendedor' => [
                        'nome' => $t->vendedor->nome ?? null,
                        'email' => $t->vendedor->email ?? null,
                    ],
                    'comprador' => [
                        'nome' => $t->comprador->nome ?? null,
                        'email' => $t->comprador->email ?? null,
                    ],
                    'qtd_tokens' => $t->qtd_tokens,
                    'valor_unitario' => $t->valor_unitario,
                    'data_transacao' => $t->data_transacao,
                    'tx_hash' => $t->tx_hash,
                ];
            });

        return response()->json([
            'resumo' => [
                'tokens_vendidos' => $tokensVendidos,
                'investidores_unicos' => $investors->count(),
            ],
            'imovel' => [
                'titulo' => $property->titulo,
                'localizacao' => $property->localizacao,
                'valor_total' => $property->valor_total,
                'status' => $property->status,
                'qtd_tokens_original' => $property->qtd_tokens_original,
                'qtd_tokens' => $property->qtd_tokens,
            ],
            'investidores' => $investors,
            'investimentos' => $investments,
            'transacoes_p2p' => $p2p,
        ]);
    }
}

