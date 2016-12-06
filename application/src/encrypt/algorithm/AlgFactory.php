<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-15
 * Time: 9:41
 */

namespace app\src\encrypt\algorithm;

/**
 * 传输算法工厂
 * Class AlgFactory
 * @author hebidu <email:346551990@qq.com>
 * @package app\src\encrypt\algorithm
 */
class AlgFactory
{
    /**
     * 获取传输算法
     * @param string $enum
     * @return IAlgorithm | bool
     */
    public static function getAlg($enum){
        
        switch ($enum){
            case AlgEnum::MD5_V1:
                return new Md5V1Alg();
            case AlgEnum::MD5_V2:
                return new Md5V2Alg();
            default:
                return false;
        }
    } 
}