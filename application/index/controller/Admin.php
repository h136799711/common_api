<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-10
 * Time: 17:00
 */

namespace app\index\controller;

/**
 * Class Admin
 * 管理后台控制器
 * @author hebidu <email:346551990@qq.com>
 * @package app\index\controller
 */
class Admin extends LoggedIn
{
    protected function _initialize()
    {
        parent::_initialize();
    }

    public function index(){
        
        return $this->fetch();
    }
}