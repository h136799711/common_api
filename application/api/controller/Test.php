<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-15
 * Time: 9:22
 */

namespace app\index\controller;


use app\src\encrypt\rsa\Rsa;
use think\Controller;

class Test extends Controller
{
    /**
     *
     * RSA 加解密
     * @return string
     * @throws \app\src\encrypt\exception\CryptException
     */
    public function index(){
        $data = "12321453opkokwe中文;;2390--==;=23日哦多撒dasdfiweofoaewfdsa";

        var_dump("明文: ".$data);
        $privateKey = file_get_contents(APP_PATH."src/encrypt/pem/rsa_private_key.pem");
        $publicKey = file_get_contents(APP_PATH."src/encrypt/pem/rsa_public_key.pem");
        $encrypt = Rsa::encrypt($data,$privateKey);

        var_dump("rsa私钥加密:".$encrypt);
        $decrypt = Rsa::decryptByPublicKey($encrypt,$publicKey);
        var_dump("rsa公钥解密:".$decrypt);

        $encrypt = Rsa::encryptPublicKey($data,$publicKey);

        var_dump("rsa公钥加密:".$encrypt);
        $decrypt = Rsa::decrypt($encrypt,$privateKey);
        var_dump("rsa私钥解密:".$decrypt);


        return "";
    }
}