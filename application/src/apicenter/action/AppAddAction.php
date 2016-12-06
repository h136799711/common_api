<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-20
 * Time: 18:49
 */

namespace app\src\apicenter\action;

use app\src\apicenter\model\App;
use app\src\base\action\BaseAction;

class AppAddAction extends BaseAction
{
    public function add(App $app){
        //TODO: 创建应用
        return $this->result(['status'=>false,'info'=>'fail']);
    }
}