<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 16/4/22
 * Time: 14:08
 */

namespace app\src\base\utils;

/**
 * DES
 * Class DesCrypt
 * @package app\vendor\Crypt
 */
class DesCryptUtils{

    public  static function encryptDesEcbPKCS5($input, $key)
    {
        $size = mcrypt_get_block_size(MCRYPT_DES, 'ecb');
        $input = self::pkcs5_pad($input, $size);

        $td   = \mcrypt_module_open(MCRYPT_DES,'','ecb',''); //使用MCRYPT_DES算法,ecb模式
        $size = mcrypt_enc_get_iv_size($td);//设置初始向量的大小
        $iv   = mcrypt_create_iv($size, MCRYPT_RAND);//创建初始向量

        $ks = mcrypt_enc_get_key_size($td);//返回所支持的最大的密钥长度（以字节计算）
        // echo "<br/>".$ks;
        $key = substr($key,0,$ks);
        // echo "<br/>".$key;
        mcrypt_generic_init($td, $key, $iv); //初始处理

        //要保存到明文
        //加密
        $encode = mcrypt_generic($td, $input);

        //结束处理
        mcrypt_generic_deinit($td);
        return $encode;
    }

    /**
     * 对明文信息进行加密
     * @param 内容|$content
     * @param 密钥|$key
     * @return 内容
     * @internal param string $salt
     */
    static public function encode($content,$key) {

        $td   = \mcrypt_module_open(MCRYPT_DES,'','ecb',''); //使用MCRYPT_DES算法,ecb模式
        $size = mcrypt_enc_get_iv_size($td);//设置初始向量的大小
        $iv   = mcrypt_create_iv($size, MCRYPT_RAND);//创建初始向量
        
        $ks = mcrypt_enc_get_key_size($td);//返回所支持的最大的密钥长度（以字节计算）
        // echo "<br/>".$ks;
        $key = substr($key,0,$ks);
        // echo "<br/>".$key;
        mcrypt_generic_init($td, $key, $iv); //初始处理

        //要保存到明文
        //加密
        $encode = mcrypt_generic($td, $content);

        //结束处理
        mcrypt_generic_deinit($td);
        return $encode;
    }

    static  function pkcs5_pad ($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    static function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text))
            return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
            return false;
        return substr($text, 0, -1 * $pad);
    }


    /**
     * 对密文进行解密
     * @param $encode_content
     * @param $key string 密钥
     * @return string
     * @internal param $content
     */
    static public function decode($encode_content,$key) {

        $td   = mcrypt_module_open(MCRYPT_DES,'','ecb',''); //使用MCRYPT_DES算法,ecb模式
        $size =mcrypt_enc_get_iv_size($td);//设置初始向量的大小
        $iv   = mcrypt_create_iv($size, MCRYPT_RAND);//创建初始向量
        $ks   = mcrypt_enc_get_key_size($td);//返回所支持的最大的密钥长度（以字节计算）

        $key = substr($key,0,$ks);

        //初始解密处理
        mcrypt_generic_init($td, $key, $iv);
        //解密
        $decrypted = mdecrypt_generic($td, $encode_content);
        //解密后,可能会有后续的\0,需去掉
        $decrypted=trim($decrypted);
        //结束
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $decrypted;
    }


}