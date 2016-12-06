<?php
namespace app\api\controller;

use app\src\base\enum\ErrorCode;
use app\src\config\helper\ConfigHelper;
use app\src\base\utils\CacheUtils;
use think\Exception;

class Index extends Base
{

    private $time;
    private $data;         //加密过的数据
    private $api_type;
    private $sign;
    private $api_ver;      //当前接口的版本，数字从100开始计数
    private $app_version;  //当前软件的版本
    private $app_type;     //当前软件的类型 ，ios，android，pc ,by_test

    private $decrypt_data; //解密过的数据


    protected function _initialize(){

        try{
            $this->_initParameter();

            $this->_check();

            CacheUtils::initAppConfig();
        }catch (Exception $ex){
            $this->apiReturnErr($ex->getMessage());
        }
    }

    public function index()
    {
        try{

            //1.
            addLog("index",$_GET,$_POST,"[进入index]",true);

            $api_type = preg_replace("/_/","/",substr(ltrim($this->api_type),3),1);
            $api_type = preg_split("/\//",$api_type);

            if(count($api_type) < 2){
                $this->apiReturnErr("type参数不正确!",ErrorCode::Invalid_Parameter);
            }

            $action_name = $api_type[1];
            $controller_name = $api_type[0];

            $domainClass = $controller_name.'Domain/'.$action_name;

            $this->decrypt_data['domain_class'] = $domainClass;
            
            $cls_name = "app\\domain\\".$controller_name.'Domain';
            if(!class_exists($cls_name,true)){
                $this->apiReturnErr(lang('err_404'),ErrorCode::Not_Found_Resource);
            }

            $this->decrypt_data['alg'] = $this->alg;
            $class = new  $cls_name($this->decrypt_data);
            if(!method_exists($class,$action_name)){
                $this->apiReturnErr(lang('err_404'),ErrorCode::Not_Found_Resource);
            }

            $class->$action_name();

            //1. 这一步不会走到
            $this->apiReturnErr("no return data",ErrorCode::Business_Error);

        }catch (Exception $ex) {
            $this->apiReturnErr($ex->getMessage(), ErrorCode::Business_Error);
        }
    }

    /**
     * 初始化公共参数
     */
    private function _initParameter(){

        //1. 获取time参数
        if(isset($_POST['time'])) {
            $this->time = floatval($_POST['time']);
            unset($_POST['time']);
            if (!is_float($this->time)) {
                $this->apiReturnErr(lang('invalid_parameter',['param'=>'time']), ErrorCode::Invalid_Parameter);
            }
        }else{
            $this->apiReturnErr(lang('lack_parameter',['param'=>'time']),ErrorCode::Lack_Parameter);
        }

        //2. 获取sign参数
        if(isset($_POST['sign'])){
            $this->sign = $_POST['sign'];
            unset($_POST['sign']);
        }else{
            $this->apiReturnErr(lang('lack_parameter',['param'=>'sign']),ErrorCode::Lack_Parameter);
        }


        //3. 获取data参数
        if(!isset($_POST['data'])) {
            $this->apiReturnErr(lang('lack_parameter',['param'=>'data']),ErrorCode::Lack_Parameter);
        }else{
            $this->data = $_POST['data'];
            unset($_POST['data']);
        }
        //4.获取api_type参数
        if(!isset($_POST['type'])) {
            $this->apiReturnErr(lang('lack_parameter',['param'=>'type']),ErrorCode::Lack_Parameter);
        }else {
            $this->api_type = $_POST['type'];
            unset($_POST['type']);
        }

        //5.获取notify_id参数
        if(!isset($_POST['notify_id'])) {
            $this->apiReturnErr(lang('lack_parameter',['param'=>'notify_id']),ErrorCode::Lack_Parameter);
        }
        $this->notify_id = $_POST['notify_id'];
        unset($_POST['notify_id']);

        //6.获取notify_id参数
        if(!isset($_POST['api_ver'])) {
            $this->apiReturnErr(lang('lack_parameter',['param'=>'api_ver']),ErrorCode::Lack_Parameter);
        }
        $this->api_ver = $_POST['api_ver'];
        unset($_POST['api_ver']);

        $this->app_version = isset($_POST['app_version'])?$_POST['app_version']:$this->app_version;
        $this->app_type = isset($_POST['app_type'])?$_POST['app_type']:$this->app_type;

        //检查语言是否支持
        $lang_support = ConfigHelper::getLangSupport();
        $is_support = false;

        foreach ($lang_support as $lang){
            if($lang['value'] == $this->lang){
                $is_support = true;
            }
        }

        if(!$is_support){
            //对于不支持的语言都使用英语
            $this->lang = "en";
        }
    }

    /**
     * 解密验证
     */
    private function _check(){

        //1. 请求时间戳校验
        $now = microtime(true);//time();
        //时间误差 +- 1分钟
        if($now - 60 > $this->time || $this->time > $now + 60){
            $this->apiReturnErr(lang('invalid_request'),ErrorCode::Invalid_Parameter);
        }

        //2. 签名校验
        $param = [
            'client_secret' =>$this->client_secret,
            'notify_id'     =>$this->notify_id,
            'time'          =>$this->time,
            'data'          =>$this->data,
            'type'          =>$this->api_type,
            'alg'           =>$this->alg,
        ];
        try{

            if(!CryptUtils::verify_sign($this->sign,$param)){
                $this->apiReturnErr(lang('err_sign'));
            }

            //3. 数据解密
            $this->decrypt_data = [];
            $this->decrypt_data = $param;
            $this->decrypt_data['api_ver']     = $this->api_ver;
            $this->decrypt_data['lang']     = $this->lang;
            $this->decrypt_data['client_id']   = $this->client_id;
            $this->decrypt_data['client_secret']   = $this->client_secret;
            $this->decrypt_data['app_version'] = $this->app_version;
            $this->decrypt_data['app_type']    = $this->app_type;

            $data = CryptUtils::decrypt($this->data);
            if(is_array($data)){
                foreach($data as $key=>$vo){
                    $this->decrypt_data['_data_'.$key] = $vo;
                }
            }
        }catch (Exception $e){
            $this->apiReturnErr($e->getMessage());
        }

    }

}
