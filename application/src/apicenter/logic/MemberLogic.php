<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-15
 * Time: 14:20
 */

namespace app\src\apicenter\logic;


use app\src\apicenter\model\Member;
use app\src\base\logic\BaseLogic;

class MemberLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new Member());
    }
    
}