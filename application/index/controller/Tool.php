<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-07
 * Time: 8:53
 */

namespace app\index\controller;

use think\Controller;

/**
 * Class Tool
 * @author hebidu <email:346551990@qq.com>
 * @package app\front\controller
 */
class Tool extends Controller
{
    /**
     * 工具库
     * @return mixed
     */
    public function index(){

        return $this->fetch();
    }
}