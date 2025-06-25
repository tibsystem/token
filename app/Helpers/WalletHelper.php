<?php

namespace App\Helpers;

use App\Helpers\Keccak;

class WalletHelper
{
    public static function generatePolygonWallet(): array
    {
        $config = [
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'secp256k1',
        ];
        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privPem);
        $details = openssl_pkey_get_details($res);

        $d = $details['ec']['d'] ?? '';
        $x = $details['ec']['x'] ?? '';
        $y = $details['ec']['y'] ?? '';
        $privKey = bin2hex($d);
        $pubKey = bin2hex($x) . bin2hex($y);
        $hash = Keccak::hash(hex2bin($pubKey), 256);
        $address = '0x' . substr($hash, -40);

        return [
            'address' => $address,
            'private_key' => $privKey,
        ];
    }
}
