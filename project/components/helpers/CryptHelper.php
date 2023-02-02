<?php

namespace app\components\helpers;

use Yii;

class CryptHelper extends yii\base\BaseObject
{    
    public static function encrypt($str, $secret)
    {
        $ivlen = openssl_cipher_iv_length($cipher = 'AES-128-CBC');
        $iv = openssl_random_pseudo_bytes($ivlen, $isSourceStrong);
        if (false === $isSourceStrong || false === $iv) {
            throw new \RuntimeException('IV generation failed');
        }
        $ciphertext_raw = openssl_encrypt($str, $cipher, $secret, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $secret, $as_binary = true);
        return base64_encode($iv . $hmac . $ciphertext_raw);
    }
    
    public static function decrypt($str, $secret)
    {
        $c = base64_decode($str);
        $ivlen = openssl_cipher_iv_length($cipher = 'AES-128-CBC');
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $plaintext = openssl_decrypt($ciphertext_raw, $cipher, $secret, $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $secret, $as_binary = true);
        if ( hash_equals($hmac, $calcmac) ) {
            return $plaintext;
        } else {
            return false;
        }
    }
}
