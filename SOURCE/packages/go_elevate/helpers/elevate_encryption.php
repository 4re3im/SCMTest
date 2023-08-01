<?php

defined('C5_EXECUTE') or die(_('Access Denied.'));

/**
 * This encryption uses AES-256;
 *
 * Note for Devs: MCRYPT_RIJNDAEL_256 is not equivalent to AES_256.
 * The way to make RIJNDAEL be decrypted from AES is to use
 * MCRYPT_RIJNDAEL_128 and pad the string to encrypt before encrypting
 * AES-256 has BlockSize=128bit and KeySize=256bit Rijndael-256 has
 * BlockSize=256bit and KeySize=256bit. Just AES/Rijndael 128bit are
 * identical. Rijndael-192 and Rijndael-256 are not identical to AES-192
 * and AES-256 (block sizes and number of rounds differ).
 */

class ElevateEncryptionHelper
{

    public static function encrypt_secure($data)
    {
        try {

            if (!defined('ELEVATE_SECRET_KEY') && !ELEVATE_SECRET_KEY) {
                throw new Exception('Please define ELEVATE_SECRET_KEY in config/site.php');
            }

            $key = ELEVATE_SECRET_KEY;
            $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

            $ciphertext = @mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
            $ciphertext = $iv . $ciphertext;
            $ciphertext_base64 = base64_encode($ciphertext);

            return $ciphertext_base64;

        } catch (Exception $e) {
            return false;
        }
    }


    public static function decrypt_secure($data)
    {
        try {

            if (!defined('ELEVATE_SECRET_KEY') && !ELEVATE_SECRET_KEY) {
                throw new Exception('Please define ELEVATE_SECRET_KEY in config/site.php');
            }

            $key = ELEVATE_SECRET_KEY;
            $ciphertext_dec = base64_decode($data);
            $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $iv_dec = substr($ciphertext_dec, 0, $iv_size);
            $ciphertext_dec = substr($ciphertext_dec, $iv_size);
            $decoded = @mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

            return trim($decoded);

        } catch (Exception $e) {
            return false;
        }
    }

    public static function generate_token($size=10)
    {
        return bin2hex(openssl_random_pseudo_bytes($size));
    }

    public static function encrypt($data)
    {
        $saltBytes = array(1, 2, 3, 4, 5, 6, 7, 8);
        $saltBytesstring = "";
        $password = hash('sha256', ELEVATE_SECRET_KEY, true);

        for ($i=0; $i < count($saltBytes); $i++){
            $saltBytesstring .= chr($saltBytes[$i]);
        }

        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $padding = $block - (strlen($data) % $block);
        $data .= str_repeat(chr($padding), $padding);

        $AESKeyLength = 256/8;
        $AESIVLength = 128/8;

        $key = hash_pbkdf2("sha1", $password, $saltBytesstring, 1000, $AESKeyLength + $AESIVLength, true);
        $aeskey = substr($key, 0, $AESKeyLength);
        $aesiv =  substr($key, $AESKeyLength, $AESIVLength);

        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $aeskey, $data, MCRYPT_MODE_CBC, $aesiv);

        return base64_encode($encrypted);
    }

    public static function decrypt($data)
    {
        $saltBytes = array(1, 2, 3, 4, 5, 6, 7, 8);
        $saltBytesstring = "";
        $password = hash('sha256', ELEVATE_SECRET_KEY, true);
        $data = base64_decode($data);

        for ($i=0; $i < count($saltBytes); $i++){
            $saltBytesstring .= chr($saltBytes[$i]);
        }

        $AESKeyLength = 256/8;
        $AESIVLength = 128/8;

        $key = hash_pbkdf2("sha1", $password, $saltBytesstring, 1000, $AESKeyLength + $AESIVLength, true);

        $aeskey = substr($key, 0, $AESKeyLength);
        $aesiv =  substr($key, $AESKeyLength, $AESIVLength);

        $decrypted = @mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $aeskey, $data, MCRYPT_MODE_CBC, $aesiv);
        $padding_char = ord($decrypted[strlen($decrypted) - 1]);

        return substr($decrypted, 0, -$padding_char);
    }
}
