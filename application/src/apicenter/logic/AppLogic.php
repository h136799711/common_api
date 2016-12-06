<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-16
 * Time: 9:49
 */

namespace app\src\apicenter\logic;


use app\src\apicenter\model\App;
use app\src\base\logic\BaseLogic;

class AppLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new App());
    }
    
}