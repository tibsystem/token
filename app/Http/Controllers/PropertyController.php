<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index()
    {
        return response()->json(Property::all());
    }

    public function show($id)
    {
        $property = Property::findOrFail($id);
        return response()->json($property);
    }

    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);
        $data = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'descricao' => 'nullable|string',
            'localizacao' => 'sometimes|required|string|max:255',
            'valor_total' => 'sometimes|required|numeric',
            'qtd_tokens' => 'sometimes|required|integer',
            'modelo_smart_id' => 'nullable|integer',
            'status' => 'sometimes|required|in:ativo,vendido,oculto',
            'data_tokenizacao' => 'nullable|date',
        ]);
        $property->update($data);

        return response()->json($property);
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();

        return response()->json(['deleted' => true]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'localizacao' => 'required|string|max:255',
            'valor_total' => 'required|numeric',
            'qtd_tokens' => 'required|integer',
            'modelo_smart_id' => 'nullable|integer',
            'status' => 'required|in:ativo,vendido,oculto',
            'data_tokenizacao' => 'nullable|date',
        ]);

        $property = $request->user()->properties()->create($data);

        return response()->json($property, 201);
    }

    public function tokens($id)
    {
        return response()->json([
            'property_id' => (int) $id,
            'tokens' => []
        ]);
    }
}
