<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:investors,email',
            'phone' => 'nullable|string|max:30',
        ]);

        $investor = Investor::create($data);

        return response()->json($investor, 201);
    }
}
