<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/11/2
 * Time: 08:38
 */
namespace app\src\base\utils;

use app\src\base\exception\ApiException;
use app\src\encrypt\algorithm\AlgFactory;


/**
 * 以接口方式调用博也PHP接口.
 * Class BoyeServiceApi
 * @package Common\Api
 */
class BoyeService {
    
    private $apiUrl;//接口地址，根地址
    private $client_id;
    private $client_secret;

    function __construct($api_url,$client_id,$client_secret){
        $this->apiUrl        = $api_url;
        $this->client_id     = $client_id;
        $this->client_secret = $client_secret;
    }

    /**
     * @param $data
     * @param bool $is_debug
     * @return array
     * @throws \Exception
     */
    public function callRemote($data,$is_debug=false){

        if(empty($this->client_id))
         return ['status' => false, 'info' => lang('param-need',['client_id'])];

        if(!isset($data['type']) || empty($data['type']))
         return ['status' => false, 'info' => lang('param-need',['type'])];
        $type = $data['type'];
        unset($data['type']);

        if(!isset($data['api_ver']) || empty($data['api_ver']))
         return ['status' => false, 'info' => lang('param-need',['api_ver'])];
        $api_ver = $data['api_ver'];
        unset($data['api_ver']);

        if(!isset($data['notify_id']) || empty($data['notify_id'])){
            $notify_id = time();
        }else{
            $notify_id = $data['notify_id'];
            unset($data['notify_id']);
        }

        $alg = "";
        if(isset($data['alg'])){
            $alg = $data['alg'];
            unset($data['alg']);
        }
        
        $algTool = AlgFactory::getAlg($alg);

        if($algTool === false){
            throw  new ApiException("not support algorithm");
        }

        $encrypt_data = $algTool->encryptData($data);

        $param = [
            'client_secret' =>$this->client_secret,
            'api_ver'       => empty($api_ver)?"100":$api_ver,
            'notify_id'     =>$notify_id,
            'time'          =>strval(intval(microtime(true))), //add-request-time
            'data'          =>$encrypt_data,
            'type'          =>$type,
            'alg'           =>$alg,
        ];

        $param['sign']      = $algTool->sign($param);

        $param['client_id'] = $this->getAccessToken();
        unset($param['client_secret']);

        $encrypt_data = $algTool->encryptTransmissionData($param,$this->client_secret);

        $param = [
            'itboye'    => base64_encode($encrypt_data),
            'alg'       => $alg,
            'client_id' => $this->client_id
        ];

        $r = $this->curlPost($param);
        // 同一进程 - 出错全部终止
        if($r['status']){
            //curl请求成功 - 不代表服务器执行结果
            $info = $r['info'];
            if(isset($info['data']) && isset($info['sign'])){
                $decrypt_data = $algTool->decryptData($info['data']);

                //返回666，则不校验签名
//                if((!isset($info['client_id']) || $info['client_id'] != '666') && !$algTool->verify_sign($info,$info['sign'])){
//                    $r = ['status'=>false,'info'=>LL('request data sign verify fail !')];
//                }

                if($decrypt_data['code'] != 0){
                    $r = ['status'=>false,'info'=>$decrypt_data['data']];
                }else{
                    $r = ['status'=>true,'info'=>$decrypt_data['data'],'notify_id'=>$info['notify_id']];
                }

            }else{
                $r = ['status'=>false,'info'=>$info];
            }

        }

        //TODO: 对data加密
        return $r;
    }

    /**
     * 取得accessToken
     * @return mixed
     */
    public function getAccessToken(){
        return $this->client_id;
    }


    /**
     * 
     * @param $url
     * @param $data  array
     * @return array
     */
    protected function curlPost($data) {

        $this->apiUrl = rtrim($this->apiUrl,"/");
        //对data进行加密
        $ch     = curl_init();
        $header = ['Accept-Charset'=>"utf-8"];
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.64 Safari/537.36'); //chrome46/mac
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        $errorno = curl_errno($ch);
        if($errorno){
            return ['status' => false, 'info' => $errorno];
        }else{

            $js = json_decode($tmpInfo,true);

            if(is_null($js)){
                $js = "$tmpInfo";
            }
            return ['status' => true, 'info' => $js];
        }
    }



}