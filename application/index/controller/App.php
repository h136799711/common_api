<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-15
 * Time: 17:25
 */

namespace app\index\controller;
use app\src\apicenter\action\AppAddAction;
use app\src\apicenter\enum\AppStatus;
use app\src\base\utils\CodeGenerateUtils;
use app\src\session\helper\SessionHelper;


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
            $uid = SessionHelper::getUserId();
            $codeUtils = new CodeGenerateUtils();
            $appKey     = $codeUtils->getAppKey($uid);
            $appSecret  = $codeUtils->getAppSecret($uid);

            //调用添加操作
            $action = new AppAddAction();
            $app = new \app\src\apicenter\model\App();
            $app->setAppKey($appKey);
            $app->setAppSecret($appSecret);
            $app->setTitle("测试");
            $app->setCreateTime(time());
            $app->setStatus(AppStatus::NORMAL);
            $app->setUrl("");
            $app->setUid($uid);
            $result = $action->add($app);

            if($result['status']){
                //添加成功
                $this->success('操作成功',url('App/index'),$result['info']);
            }else{
                $this->error($result['info'],url('App/add'),$result['info']);
            }
        }else{
            return $this->fetch();
        }

    }

}