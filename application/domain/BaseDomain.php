<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/12/3
 * Time: 18:01
 * editor : rainbow
 */
namespace app\domain;

use app\src\apicenter\logic\ApiHistoryLogic;
use app\src\base\enum\ErrorCode;
use app\src\base\helper\PageHelper;
use app\src\base\helper\ValidateHelper;
use app\src\encrypt\algorithm\AlgFactory;
use think\Db;
use think\Response;

/**
 * 基础领域模型
 * Class BaseDomain
 * @package app\src\domain
 */
class BaseDomain {

    protected $notify_id;//请求id
    protected $client_id;//key
    protected $client_secret;//密钥
    protected $time;//时间
    protected $api_ver       = 100;//默认接口版本
    protected $allowType     = ["json", "rss", "html"];
    protected $business_code = '';//业务码
    protected $cur_api_ver;  //服务端当前api_ver
    protected $domain_class; //领域模型类
    protected $origin_data;//原始数据
    protected $request_api_ver;//请求的api_ver
    protected $lang;//语言版本


    public function __construct($data) {

        debug('begin');
        $this->origin_data = $data;

        if(!isset($this->origin_data['client_secret'])){
            $this->apiReturnErr(L('param-need', ['client_secret']), ErrorCode::Lack_Parameter);
        }
        $this->client_secret =  $this->origin_data['client_secret'];

        if(!isset($this->origin_data['notify_id'])){
            $this->apiReturnErr(L('param-need', ['notify_id']), ErrorCode::Lack_Parameter);
        }
        $this->notify_id = $this->origin_data['notify_id'];

        if(!isset($this->origin_data['time'])){
            $this->apiReturnErr(L('param-need', ['time']), ErrorCode::Lack_Parameter);
        }
        $this->time = $this->origin_data['time'];

        if(!isset($this->origin_data['client_id'])){
            $this->apiReturnErr(L('param-need', ['client_id']), ErrorCode::Lack_Parameter);
        }
        $this->client_id = $this->origin_data['client_id'];

        if(!isset($this->origin_data['domain_class'])){
            $this->apiReturnErr(L('param-need', ['domain_class']), ErrorCode::Lack_Parameter);
        }
        $this->domain_class = $this->origin_data['domain_class'];
        if(!isset($this->origin_data['api_ver'])){
            $this->apiReturnErr(L('param-need', ['api_ver']), ErrorCode::Lack_Parameter);
        }

        $this->request_api_ver = $this->origin_data['api_ver'];

        if(!isset($this->origin_data['lang'])){
            $this->apiReturnErr(L('param-need', ['lang']), ErrorCode::Lack_Parameter);
        }
        $this->lang      = $this->origin_data['lang'];
    }

    /**
     * 服务端允许的api版本/列表
     * @param string $version
     * @internal param $ [int|array]     $version
     */
    protected function checkVersion($version = '') {
        if (!$version) $version = $this->api_ver;
        if (is_array($version)) {
            $legal = false;

            foreach ($version as $item) {
                if ($item == intval($this->request_api_ver)) {
                    $legal = true;
                    break;
                }
            }
            
            if ($legal == false) {
                $msg = lang('tip_update_api_version',['version'=>'-1']);
                if(count($version) > 0){
                    $msg = lang('tip_update_api_version',['version'=>$version[0]]);
                }
                $this->apiReturnErr($msg, ErrorCode::Api_Need_Update);
            }

        } else {

            if ($version != $this->request_api_ver) {
                $msg = lang('tip_update_api_version',['version'=>$version]);
                $this->apiReturnErr($msg, ErrorCode::Api_Need_Update);
            }
        }

    }

    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @return array
     * @throws \Exception
     * @throws \app\src\base\exception\ApiException
     * @internal param String $type AJAX返回数据格式
     * @internal param int $json_option 传递给json_encode的option参数
     */
    protected function ajaxReturn($data) {
        //接口         $this->domain_class
        //创建时间     START_TIME
        //请求开始时间 app_send APP传过来
        //网络传输时间 START_TIME - app_send
        //接口执行耗时 debug('begin','end',4).'s'
        //param
        //内存占用     debug('begin','end','m').'kb';
        //请求头       $_SERVER['HTTP_USER_AGENT']

        $api_end = microtime(true);
        $app_time = $this->time;
        $alg = $this->origin_data['alg'];
        $algTool = (new AlgFactory())->getAlg($alg);

        if(!empty($this->domain_class)){
            $entity = [
                'api_uri'      => $this->domain_class,
                'create_time'  => START_TIME,
                'start_time'   => $app_time,
                'request_time' => (float)START_TIME - (float)$app_time,
                'cost_time'    => (float)($api_end - START_TIME),
                'usemem'       => debug('begin', 'end', 'm')
            ];
            
            $entity['request_time'] = $entity['request_time'] > 0 ? $entity['request_time'] : 0.0;
            
            $logic  = new ApiHistoryLogic();
            $result = $logic->add($entity);

            if ($result['status']) {
                $data['code'] = -1;
                $data['data'] = $result['info'];
            };
        }
        
        $code = $data['code'];
        if ($code == 0) {
            $type = "T";
        } else {
            $type = "F";
        }
        
        $data = $algTool->encryptData($data);
        $now = time();

        $param = array(
            'client_secret' => $this->client_secret,
            'data'          => $data,
            'notify_id'     => $this->notify_id,
            'time'          => strval($now),
            'type'          => $type,
            'alg'           => $alg,
        );

        $param['sign'] = $algTool->sign($param);

        Response::create($param, 'json')->send();
        exit();
    }

    /**
     * ajax返回
     * @param $data
     * @param bool $cache
     * @internal param $i
     */
    protected function apiReturnSuc($data, $cache = false) {

        $data = $this->toStringData($data);

        $this->checkNullData($data);

        $this->ajaxReturn(['code' => 0, 'data' => $data, 'notify_id' => $this->notify_id, 'cache' => $cache]);
    }

    protected function toStringData($data){
        if(is_array($data)){
            foreach ($data as $key=>&$value){
                $data[$key] = $this->toStringData($value);
            }
        }elseif(!is_object($data) && !is_string($data)){
            return strval($data);
        }

        return $data;
    }

    /**
     * 检查返回是否有null的数据
     * @param $data
     */
    protected function checkNullData($data){
        if(is_null($data)){
            $this->apiReturnErr(lang('err_return_is_not_null'));
        }elseif(is_array($data)){
            foreach ($data as $value){
                $this->checkNullData($value);
            }
        }elseif(is_object($data) && method_exists($data,"toArray")){
            foreach ($data->toArray() as $key=>$value){
                $this->checkNullData($value);
            }
        }
    }


    protected function exitResult($result){
        $this->exitWhenError($result,true);
    }

    /**
     * 退出应用当发生错误的时候
     * @param $result
     * @param bool $retSuc
     */
    protected function exitWhenError($result,$retSuc=false) {

        if($result['status'] == false){
            $this->apiReturnErr($result['info']);
        }elseif ($retSuc){
            $info = $result['info'];

            if(!is_int($info) && !ValidateHelper::isNumberStr($info)){
                $this->apiReturnSuc($info);
            }
            $id = intval($info);
            //如果是数字，则应该是添加或修改操作
            //对于这种情况，如果大于0 则默认成功 否则 失败
            if($id > 0){
                $this->apiReturnSuc(lang("success"));
            }else{
                $this->apiReturnErr(lang("fail"));
            }

        }
    }
    /**
     * ajax返回，并自动写入token返回
     * @param $data
     * @param int $code
     * @internal param $i
     */
    protected function apiReturnErr($data, $code = -1) {
        $this->ajaxReturn(['code' => $code, 'data' => $data, 'notify_id' => $this->notify_id, 'cache' => false]);
    }

    /**
     * @param $key
     * @param string $default
     * @param string $emptyErrMsg 为空时的报错
     * @return mixed
     */
    public function _post($key, $default = '', $emptyErrMsg = '') {

        $value = isset($this->origin_data["_data_" . $key]) ? $this->origin_data["_data_" . $key]:$default;

        if ($default == $value && !empty($emptyErrMsg)) {
            $emptyErrMsg = lang('lack_parameter',['param'=>$key]);
            $this->apiReturnErr($emptyErrMsg, ErrorCode::Lack_Parameter);
        }

        $value = $this->escapeEmoji($value);

        if ($default == $value && !empty($emptyErrMsg)) {
            $emptyErrMsg = lang('lack_parameter',['param'=>$key]);
            $this->apiReturnErr($emptyErrMsg, ErrorCode::Lack_Parameter);
        }

        return $value;
    }

    /**
     * 获取分页参数信息
     * @return PageHelper
     */
    public function _getPageParams(){
        return new PageHelper([
            'page_index'=>$this->_post('page_index',1),
            'page_size'=>$this->_post('page_size',10)
        ]);
    }

    /**
     * @param $key
     * @param string $default
     * @param string $emptyErrMsg 为空时的报错
     * @return mixed
     */
    public function _get($key, $default = '', $emptyErrMsg = '') {
        $this->_post($key,$default,$emptyErrMsg);
    }

    /**
     * 放到utils中，作为工具类
     * @brief 干掉emoji
     * @autho chenjinya@baidu.com
     * @param {String} $strText
     * @param bool $bool
     * @return int|mixed|string {String} removeEmoji
     * removeEmoji
     */
    protected function escapeEmoji($strText, $bool = false) {
        $preg = '/\\\ud([8-9a-f][0-9a-z]{2})/i';
        if ($bool == true) {
            $boolPregRes = (preg_match($preg, json_encode($strText, true)));
            return $boolPregRes;
        } else {
            $strPregRes = (preg_replace($preg, '', json_encode($strText, true)));
            $strRet = json_decode($strPregRes, JSON_OBJECT_AS_ARRAY);

            if ( is_string($strRet) && strlen($strRet) == 0) {
                return "";
            }

            return $strRet;
        }
    }


    /**
     * 根据key数组来获取参数
     * @param $keys
     * @return array
     */
    protected function _getParams($keys){
        $params = [];
        foreach ($keys as $key){
            $params[$key] = $this->_post($key,'');
        }
        return $params;
    }
}