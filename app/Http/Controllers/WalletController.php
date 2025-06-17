<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function show()
    {
        return response()->json(['balance' => 0]);
    }

    public function addFunds(Request $request)
    {
        return response()->json(['message' => 'Funds added']);
    }

    public function withdraw(Request $request)
    {
        return response()->json(['message' => 'Withdraw processed']);
    }
}
