<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 16/4/22
 * Time: 20:09
 */

namespace app\src\base\exception;

use think\Exception;

/**
 * 接口异常抛出基类
 * Class ApiException
 * @package app\src\base\exception
 */
class ApiException extends Exception {

    /**
     * 系统异常后发送给客户端的HTTP Status
     * @var integer
     */
    protected $httpStatus = 200;
}