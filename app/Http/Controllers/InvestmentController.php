<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    public function purchase(Request $request)
    {
        return response()->json(['message' => 'Purchase processed']);
    }

    public function history()
    {
        return response()->json([]);
    }
}
