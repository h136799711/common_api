<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-10
 * Time: 17:17
 */

namespace app\src\config\helper;


class ConfigHelper
{
    /**
     * 获取头像地址
     * @author hebidu <email:346551990@qq.com>
     * @param $uid
     * @param int $size
     * @return string
     */
    public static function getAvatarUrl($uid,$size=180){
        return config('avatar_url').'?uid='.$uid.'&size='.$size;
    }

    public static function __callStatic($name,$arguments){
        return config($name);
    }

    /**
     * 获取语言列表
     * @author hebidu <email:346551990@qq.com>
     */
    public static function getLangSupport(){
        return config("lang_support");
    }

    /**
     * 获取图片上传缩略图路径
     * @author hebidu <email:346551990@qq.com>
     * @return mixed
     */
    public static function getFileThumbnailPath(){
        return config("file_cfg.thumbnail_path");
    }

    /**
     * 获取图片支持裁剪大小
     * @return mixed
     */
    public static function getFilePictureCropSize(){
        return config("file_cfg.picture_crop_size");
    }

    public static function isDebug(){
        return config('app_debug');
    }

    public static function getPasswordSalt(){
        return config("security_salt.password");
    }

    public static function getSecuritySalt(){
        return config("security_salt");
    }

}