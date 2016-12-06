<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-15
 * Time: 17:25
 */

namespace app\index\controller;
use app\src\apicenter\action\AppAddAction;


/**
 * Class App
 * 应用控制器
 * @author hebidu <email:346551990@qq.com>
 * @package app\index\controller
 */
class App extends LoggedIn
{

    /**
     * 应用管理
     * @return mixed
     */
    public function index(){

        return $this->fetch();
    }

    /**
     * 应用添加
     */
    public function add(){

        if(IS_POST){

            //TODO: 调用接口 
            $action = new AppAddAction();
            $app = new \app\src\apicenter\model\App();
            $app->setKey("1");

            return $this->returnResult($action->add($app));
        }else{
            return $this->fetch();
        }

    }

}