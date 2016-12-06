<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-15
 * Time: 9:45
 */

namespace app\src\encrypt\algorithm;

use app\src\encrypt\des\Des;
use app\src\encrypt\exception\CryptException;

/**
 * Class Md5V2Alg
 * 目前使用于 php
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\encrypt\algorithm
 */
class Md5V2Alg extends IAlgorithm
{

    function decryptTransmissionData($transmissionData,$desKey)
    {
        $data = Des::decode(json_decode($transmissionData) , $desKey);
        
        return ($data);
    }

    function encryptTransmissionData($param,$desKey)
    {
        $encrypt_data = Des::encode(json_encode($param) , $desKey);
        
        return ($encrypt_data);
    }

    function verify_sign($data,$sign)
    {
        $tmp_sign = Md5V2Alg::sign($data);
        if($sign == $tmp_sign){
            return true;
        }

        return false;
    }

    function sign($param)
    {

        if(!isset($param['time']) || empty($param['time'])){ 
            throw new CryptException("time invalid");
        }

        if(!isset($param['type']) || empty($param['type'])){
            throw new CryptException("type invalid");
        }

        if(!isset($param['notify_id']) || empty($param['notify_id'])){
            throw new CryptException("notify_id invalid");
        }

        if(!isset($param['client_secret']) || empty($param['client_secret'])){
            throw new CryptException("client_secret invalid");
        }

        if(!isset($param['data']) || empty($param['data'])){
            throw new CryptException("data invalid");
        }

        $time = $param['time'];
        $type = $param['type'];
        $notify_id = $param['notify_id'];
        $client_secret = $param['client_secret'];
        $data = $param['data'];
        
        $text = $time.$type.$data.$client_secret.$notify_id;

        return md5($text);
    }

    function decryptData($encryptData)
    {
        return json_decode(base64_decode(base64_decode($encryptData)),JSON_OBJECT_AS_ARRAY);
    }

    function encryptData($data)
    {
        $str = json_encode($data,0, 512);
        return base64_encode(base64_encode($str));
    }

}