<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-15
 * Time: 15:02
 */

namespace app\src\apicenter\logic;


use app\src\apicenter\model\ApiHistory;
use app\src\base\logic\BaseLogic;

class ApiHistoryLogic extends BaseLogic
{
    public function _init()
    {
        $this->setModel(new ApiHistory());
    }
}