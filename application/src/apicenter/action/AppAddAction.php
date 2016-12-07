<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-20
 * Time: 18:49
 */

namespace app\src\apicenter\action;

use app\src\apicenter\logic\AppLogic;
use app\src\apicenter\model\App;
use app\src\base\action\BaseAction;

/**
 * Class AppAddAction
 * 应用添加操作
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\apicenter\action
 */
class AppAddAction extends BaseAction
{
    public function add(App $app){
        return (new AppLogic())->add($app);
    }
}