<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-09
 * Time: 21:15
 */

namespace app\index\controller;


use app\src\apicenter\logic\MemberLogic;
use app\src\base\utils\BoyeService;
use app\src\config\helper\ConfigHelper;
use app\src\session\helper\SessionHelper;
use think\Controller;

/**
 * 首页入口
 * Class Index
 * @author hebidu <email:346551990@qq.com>
 * @package app\index\controller
 */
class Index extends Controller
{

    public function read_xml(){
        $json_file_url = $this->request->post('file_url','');
        if(!empty($json_file_url)){
            $json_data = file_get_contents($json_file_url);
            return $json_data;
        }
        return "";
    }

    public function test(){

        $api_url = $this->request->post("api_url",'');
        $client_id = $this->request->post("client_id","");
        $client_secret = $this->request->post("client_secret","");
        $data = $_POST;
        if(isset($data['api_type'])){
            $data['type'] = $data['api_type'];
        }
        unset($data['client_id']);
        unset($data['client_secret']);
        unset($data['api_url']);
        $service = new BoyeService($api_url,$client_id,$client_secret);

        $result = $service->callRemote($data,false);

        return json_encode($result);
    }

    public function index(){
        $this->redirect(url("Admin/index"));
    }

    /**
     * 欢迎
     */
    public function wel(){
        return $this->fetch();
    }

    /**
     * about
     */
    public function about(){
        return $this->fetch();
    }

    /**
     * 登录
     */
    public function login(){
        
        return $this->fetch();
    }

    public function logout(){
        SessionHelper::logout();
        $this->redirect(url("index/login"));
    }
    
    /**
     * 注册
     */
    public function register(){
        if($this->request->isGet()){
            return $this->fetch();
        }elseif ($this->request->isPost() || $this->request->isAjax()){
            //TODO: 注册功能 用户名+密码+验证码 即可注册
        }
    }

    protected $demoAccount = array(
        array('username'=>'itboye','password'=>1)
    );
    
    /**
     * 登录检测
     */
    public function check_login(){
        $IS_DEBUG = false;
        if(config('app_debug') !== false){
            $IS_DEBUG = config('app_debug');
        }


        if(request()->isAjax()){
            
            //TODO: 防止暴力破解
            $code = $this->request->post("code");
            $username = $this->request->post("username");
            $password = $this->request->post("password");
            
            if($IS_DEBUG && isset($this->demoAccount[$username])){
                $password = $this->demoAccount[$username];
            }

            if(empty($username) || empty($password)){
                $this->error(lang("ERR_EMPTY_ACCOUNT_PWD"),url("index/login"));
            }

            $api = new MemberLogic();
            $map = ['username'=>$username];
            $result = $api->getInfo($map);
            $member = $result['info'];

            if(!$result['status']){
                $this->error($result['info'],url("index/login"));
            }

            if(empty($member)){
                $this->error(lang("ERR_LOGIN_FAIL"),url("index/login"));
            }
            
            $password_salt = ConfigHelper::getPasswordSalt();
            $encrypt_pwd   = think_ucenter_md5($password,$password_salt);

            if($member['password'] != $encrypt_pwd){
                $this->error(lang("ERR_LOGIN_FAIL"),url("index/login"));
            }

            //将用户信息放置在session
            SessionHelper::setUserInfo($member);

            $this->success(lang('SUC_LOGIN'),url('admin/index'));
        }
    }
}