<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-10
 * Time: 16:40
 */

namespace app\index\controller;


use app\src\session\helper\SessionHelper;
use think\Controller;

/**
 * Class LoggedIn
 * 检查是否已经登录
 * @author hebidu <email:346551990@qq.com>
 * @package app\index\controller
 */
class  LoggedIn extends Controller
{

    public function _empty(){
        $this->error(lang("ERR_EMPTY_RESOURCE"),url("index/index"),'',3);
    }
    
    // 初始化
    protected function _initialize()
    {
        self::check_login();
    }

    protected function check_login(){

        if (!SessionHelper::isLogin()) {
            $this->error("请登录",url("index/login"),'',3);
        }
        
        $this->assign("user",SessionHelper::getUserInfo());
    }

    protected function returnResult($result){
        if($result['status']){
            $this->success($result['info']);
        }else{
            $this->error($result['info']);
        }
    }
}