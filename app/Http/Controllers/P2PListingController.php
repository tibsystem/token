<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\P2PListing;
use App\Models\Investment;

class P2PListingController extends Controller
{
    public function index()
    {
        return response()->json(P2PListing::where('status', 'ativa')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vendedor_id' => 'required|integer|exists:investors,id',
            'id_imovel' => 'required|integer|exists:properties,id',
            'qtd_tokens' => 'required|integer|min:1',
            'valor_unitario' => 'required|numeric',
        ]);

        $investment = Investment::where('id_investidor', $data['vendedor_id'])
            ->where('id_imovel', $data['id_imovel'])
            ->first();

        if (!$investment || $investment->qtd_tokens < $data['qtd_tokens']) {
            return response()->json(['message' => 'Tokens insuficientes'], 400);
        }

        $listing = P2PListing::create($data);
        return response()->json($listing, 201);
    }

    public function destroy($id)
    {
        $listing = P2PListing::findOrFail($id);
        $listing->status = 'cancelada';
        $listing->save();
        return response()->json($listing);
    }
}
