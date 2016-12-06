<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/3
 * Time: 20:22
 */

namespace app\api\controller;

use app\src\base\enum\ErrorCode;

use app\src\base\exception\ApiException;
use app\src\base\helper\PageHelper;
use app\src\encrypt\algorithm\AlgFactory;
use app\src\encrypt\algorithm\IAlgorithm;
use think\controller\Rest;
use think\Exception;
use think\Request;
use think\Response;


/**
 * 接口基类
 * Class Base
 *
 * @author 老胖子-何必都 <hebiduhebi@126.com>
 * @package app\index\Controller
 */
abstract class Base extends Rest{

    protected $lang;//当前请求的语言版本
    protected $alg;//当前请求通信算法
    protected $encrypt_key = "";
    protected $client_id = "";
    protected $client_secret = "";
    protected $notify_id = "";

    protected $allow_controller = array( );

    protected $decodeData = [];
    protected $algTool;//通讯工具

    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();


        if(strtolower(request()->controller()) != "index"  && !in_array(strtolower(request()->controller()),$this->allow_controller)){
            $this->apiReturnErr( ErrorCode::Not_Found_Resource,"请求资源不存在!");
        }

        if(method_exists($this,"_initialize")){
            $this->decodePost();
            $this->lang = Request::instance()->get("lang","zh-cn");
            $this->lang = strtolower($this->lang);
            $this->_initialize();
        }
    }


    protected function decodePost(){

        $this->client_id =  $this->_param("client_id","", lang('lack_parameter',['param'=>'client_id']));

        $api = new OauthClientsLogic();

        $result = $api->getInfo(array('client_id'=>$this->client_id));

        if($result['status'] && !empty($result['info']) && is_array($result['info'])){
            $this->client_secret = $result['info']['client_secret'];
        }else{
            $this->apiReturnErr(lang('invalid_parameter',['param'=>'client_id']),ErrorCode::Invalid_Parameter);
        }

        //读取传输过来的加密参数
        $post = $this->_post('itboye','');
        $this->alg  = $this->_param('alg','');

        $this->algTool = (new AlgFactory())->getAlg($this->alg);

        if($this->algTool === false){
            throw  new ApiException(lang('alg_not_support'));
        }

        $data = $this->algTool->decryptTransmissionData($post,$this->client_secret);

        $data = $this->filter_post($data);
        $this->decodeData = json_decode($data,JSON_OBJECT_AS_ARRAY);
    }

    /**
     * 过滤末尾多余空白符 ASCII码等于7的奇怪符号
     * @param $post
     * @return string
     */
    protected function filter_post($post){
        $post = trim($post);
        for ($i=strlen($post)-1;$i>=0;$i--) {
            $ord = ord($post[$i]);
            if($ord > 31 && $ord != 127){
                $post = substr($post,0,$i+1);
                return $post;
            }
        }
        return $post;
    }

    /**
     * 返回加密后的数据
     * @access protected
     * @param mixed $data 要返回的数据，未加密
     * @return array
     */
    protected function ajaxReturn($data) {

        $code = $data['code'];
        if ($code == 0) {
            $type = "T";
        } else {
            $type = "F";
        }

        if(!($this->algTool instanceof IAlgorithm)){
            throw new ApiException(lang("alg_not_support"));
        }

        $data = $this->algTool->encryptData($data);
        $now = time();

        if (empty($this->notify_id)) {
            $this->notify_id = $now;
        }

        $param = array(
            'client_secret' => $this->client_secret,
            'data'          => $data,
            'notify_id'     => $this->notify_id,
            'time'          => strval($now),
            'type'          => $type,
            'alg'           => $this->alg,
        );

        $param['sign'] = $this->algTool->sign($param);

        unset($param['client_secret']);
        unset($param['type']);
        unset($param['time']);

        $response = $this->response($param, "json",200);
        $response->header("X-Powered-By","WWW.ITBOYE.COM")->send();
        exit(0);
    }

    /**
     * ajax返回
     * @param $data
     * @internal param $i
     * @return array
     */
    protected function apiReturnSuc($data){
        $this->ajaxReturn(array('code'=>0,'data'=>$data,'notify_id'=>$this->notify_id));
    }

    /**
     * ajax返回，并自动写入token返回
     * @param $data
     * @param int $code
     * @internal param $i
     * @return array
     */
    protected function apiReturnErr($data,$code=-1){
        $this->ajaxReturn(array('code'=>$code,'data'=>$data,'notify_id'=>$this->notify_id));
    }

    public function _param($key,$default='',$emptyErrMsg=''){
        $value = request()->post($key,$default);

        if($value == $default || empty($value)){
            $value =  request()->get($key,$default);
        }

        if($default == $value && !empty($emptyErrMsg)){
            $this->apiReturnErr($emptyErrMsg);
        }
        return $value;
    }

    /**
     * @param $key
     * @param string $default
     * @param string $emptyErrMsg  为空时的报错
     * @return mixed
     */
    public function _post($key,$default='',$emptyErrMsg=''){

        $value = request()->post($key,$default);

        if($default == $value && !empty($emptyErrMsg)){
            $this->apiReturnErr($emptyErrMsg);
        }
        return $value;
    }

    /**
     * @param $key
     * @param string $default
     * @param string $emptyErrMsg  为空时的报错
     * @return mixed
     */
    public function _get($key,$default='',$emptyErrMsg=''){
        $value = request()->get($key,$default);

        if($default == $value && !empty($emptyErrMsg)){
            $this->apiReturnErr($emptyErrMsg);
        }
        return $value;
    }

    /**
     * 从请求头部获取参数
     * @param $key
     * @param string $default
     * @param string $emptyErrMsg
     * @return string
     */
    public function _header($key,$default='',$emptyErrMsg = ''){

        $value = Request::instance()->header($key);

        if($default == $value && !empty($emptyErrMsg)){
            $this->apiReturnErr($emptyErrMsg);
        }
        return $value;
    }



}