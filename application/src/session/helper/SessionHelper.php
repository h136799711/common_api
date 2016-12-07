<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-10
 * Time: 17:20
 */

namespace app\src\session\helper;


use app\src\session\enum\SessionKeys;
use think\Session;

class SessionHelper
{

    /**
     * 是否登录
     * @author hebidu <email:346551990@qq.com>
     */
    public static function isLogin(){
        return Session::has(SessionKeys::USER) && !empty(self::getUserInfo());
    }

    /**
     * 用户登出
     * TODO: 单点用户注销
     */
    public static function logout(){
        Session::clear();
    }

    /**
     * 设置用户信息
     * @author hebidu <email:346551990@qq.com>
     * @param $userinfo
     */
    public static function setUserInfo($userinfo){
        session(SessionKeys::USER,$userinfo);
    }

    /**
     * 获取用户信息
     * @author hebidu <email:346551990@qq.com>
     * @return Session
     */
    public static function getUserInfo(){
        return session(SessionKeys::USER);
    }

    /**
     * 获取用户id
     */
    public static function getUserId(){

        $user = self::getUserInfo();

        if(is_array($user) && isset($user['id'])){
            return $user['id'];
        }

        return -1;
    }

}