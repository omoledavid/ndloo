<?php

namespace App\Support\Helpers\Payments;

class CardEncryption
{
    public static function flutterwaveEncrypt(array $payload): string
    {
        $encrypted = openssl_encrypt(
            json_encode($payload),
            'DES-EDE3',
            env('FLUTTERWAVE_ENCRYPTION_KEY'),
            OPENSSL_RAW_DATA
        );

        return base64_encode($encrypted);
    }

    public static function koraEncrypt(array $payload): string
    {
        $iv = openssl_random_pseudo_bytes(16);
        $tag = '';

        $cipherText = openssl_encrypt(
            json_encode($payload),
            'aes-256-gcm',
            env('KORA_ENCRYPTION_KEY'),
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
        );

        return bin2hex($iv).':'.bin2hex($cipherText).':'.bin2hex($tag);
    }
}
