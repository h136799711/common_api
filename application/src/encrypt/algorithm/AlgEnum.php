<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-15
 * Time: 9:41
 */

namespace app\src\encrypt\algorithm;

/**
 * 算法规则
 * Class AlgEnum
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\encrypt\algorithm
 */
class AlgEnum
{
    /**
     * md5 version 1 传输算法
     * des    作为传输数据加解密
     * md5    作为签名验证
     * base64 作为传输真实内容数据加解密
     */
    const MD5_V1 = "md5";

    /**
     * md5 version 2 传输算法 | 差别在于 des 加密 对java处理的时候增加了padding
     * des    作为传输数据加解密
     * md5    作为签名验证
     * base64 作为传输真实内容数据加解密
     */
    const MD5_V2 = "md5_v2";

    /**
     * RSA_V1 传输算法
     *
     * des 作为传输数据加解密
     * md5 作为签名验证
     * rsa 作为传输真实内容数据加解密
     **/
    const RSA_V1 = "rsa_v1";
}