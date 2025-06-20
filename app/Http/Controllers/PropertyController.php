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
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric',
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
