<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-24
 * Time: 9:50
 */

namespace app\src\encrypt\rsa;


use app\src\encrypt\exception\CryptException;

class Rsa
{
    
    public static $privateKey;

    public static $publicKey;
    
    public static function test(){
        //以下是二进制pem转换过来的 BEGIN 与 END 部分是手动添加
        $pemKey = file_get_contents(dirname(__DIR__).'../pem/rsa_private_key.pem');
        
    }

    /**
     * 公钥加密
     * @author hebidu <email:346551990@qq.com>
     * @param $data
     * @param $public_key
     * @return string
     * @throws CryptException
     */
    public static function encryptPublicKey($data,$public_key){
        $pi_key = openssl_pkey_get_public($public_key);
        if($pi_key === false){
            throw  new CryptException("public key read fail");
        }

        $encryptData = "";
        $ret = openssl_public_encrypt($data,$encryptData,$pi_key);

        if($ret == false){
            throw  new CryptException("public encrypt fail");
        }

        return base64_encode($encryptData);
    }

    /**
     * 公钥解密
     * @param $data
     * @param $public_key
     * @return string
     * @throws CryptException
     */
    public static function decryptByPublicKey($data,$public_key){

        $pi_key = openssl_pkey_get_public($public_key);
        if($pi_key === false){
            throw  new CryptException("public key read fail");
        }

        $decryptData = "";
        $ret = openssl_public_decrypt(base64_decode($data),$decryptData,$pi_key);
        if($ret == false){
            throw  new CryptException("public decrypt fail");
        }

        return ($decryptData);
    }

    /**
     * 私钥加密
     * @param $data
     * @param $private_key
     * @return string  返回的是base64加密过的
     * @throws CryptException
     */
    public static function encrypt($data,$private_key){
        $pi_key = openssl_pkey_get_private($private_key);
        if($pi_key === false){
            throw  new CryptException("private key read fail");
        }
        
        $encrypt = "";
        $ret = openssl_private_encrypt($data,$encrypt,$private_key);

        if($ret == false){
            throw  new CryptException("private encrypt fail");
        }

        return base64_encode($encrypt);
    }

    /**
     * 私钥解密
     * @author hebidu <email:346551990@qq.com>
     * @param $data
     * @param $private_key
     * @return string
     * @throws CryptException
     */
    public static function decrypt($data,$private_key){
        $pi_key = openssl_pkey_get_private($private_key);

        if($pi_key === false){
            throw  new CryptException("private key get fail");
        }

        $decrypt = "";
        $ret = openssl_private_decrypt(base64_decode($data),$decrypt,$private_key);

        if($ret == false){
            throw  new CryptException("private key decrypt fail");
        }

        return $decrypt;
    }
}