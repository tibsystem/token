<?php

namespace App\Http\Controllers;

use App\Models\PlatformSetting;
use Illuminate\Http\Request;

class PlatformSettingsController extends Controller
{
    public function show()
    {
        $settings = PlatformSetting::first();
        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'taxa_compra_token' => 'numeric|min:0',
            'taxa_negociacao_p2p' => 'numeric|min:0',
        ]);

        $settings = PlatformSetting::first();
        if (!$settings) {
            $settings = PlatformSetting::create($data);
        } else {
            $settings->update($data);
        }

        return response()->json($settings);
    }
}
