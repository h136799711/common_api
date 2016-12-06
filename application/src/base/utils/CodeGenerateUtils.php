<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-10
 * Time: 19:04
 */

namespace app\src\base\utils;


class CodeGenerateUtils
{
    /**
     * 获取订单编号 uid
     * @author hebidu <email:346551990@qq.com>
     * @param $uid
     * @return string
     */
    public function getOrderCode($uid){
        $rand = mt_rand(1000000, 9999999);
        $orderID = date("yzHis",time());
        return "T".$orderID.$rand.get_36HEX($uid);
    }

    /**
     * 获取支付编号
     * @author hebidu <email:346551990@qq.com>
     * @param $uid
     * @return string
     */
    public function getAppPayCode($uid){
        $rand = mt_rand(1000000, 9999999);
        $orderID = date("yzHis",time());

        return "PA".$orderID.$rand.get_36HEX($uid);
    }

    /**
     * 获取app key
     * @author hebidu <email:346551990@qq.com>
     * @param $uid
     * @return string
     */
    public function getAppKey($uid){
        return "by".uniqid().$uid;
    }

    /**
     * 获取app secret
     * @author hebidu <email:346551990@qq.com>
     * 
     */
    public function getAppSecret($uid){
        return md5(uniqid().$uid);
    }
}