<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-02
 * Time: 15:00
 */

namespace app\src\rfpay\utils;

/**
 * Class AesUtils
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\rfpay\utils
 */
class AesUtils
{

    /**
     * key 必须是16位
     *
     * @param $input
     * @param $key
     * @return string
     */
    public static function encrypt($input, $key) {

        if(strlen($key) > 16){
            $key = substr($key,0,16);
        }

        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_ECB);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $input = AesUtils::pkcs5_pad($input, $size);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return AesUtils::toHexStr($data);
    }

    private static function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    public static function decrypt($sStr, $sKey) {

        if(strlen($sKey) > 16){
            $sKey = substr($sKey,0,16);
        }

        $sStr =  AesUtils::toBinStr($sStr);
        $decrypted = mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            $sKey,
            $sStr,
            MCRYPT_MODE_ECB
        );

        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s-1]);

        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }


    public static function toHexStr($str){
        $sb = "";
        for ($i = 0; $i < strlen($str); $i++) {

            $ord = ord($str[$i]);

            $hex = dechex($ord);

            if (strlen(strval($hex)) == 1) {
                $hex = '0'.$hex;
            }

            $sb .= strtoupper($hex);
        }

        return $sb;
    }

    public static function toBinStr($str){

        $sb = "";

        for ($i = 0; $i < strlen($str); $i+=2) {

            $dec = hexdec(substr($str,$i,2));

            $chr = chr($dec);

            $sb .= $chr;
        }
        return $sb;
    }

}