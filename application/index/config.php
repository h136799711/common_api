<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-09
 * Time: 22:07
 */


return [
    // 默认输出类型
    'default_return_type' => 'html',
    'view_replace_str'    => [
        '__STATIC__' => __ROOT__ . '/public/static/',
        '__THEME__' => __ROOT__ . '/public/static/' . request()->module() . '/theme',
        '__CSS__' => __ROOT__ . '/public/static/' . request()->module() . '/css',
        '__JS__' => __ROOT__ . '/public/static/' . request()->module() . '/js',
        '__IMG__' => __ROOT__ . '/public/static/' . request()->module() . '/image',
        '__CDN__'    => ITBOYE_CDN,
        '__APP_VERSION__' => time(),
    ],
    
    //验证码配置
    'captcha'  => [
        // 验证码字符集合
        'codeSet'  => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',
        // 验证码字体大小(px)
        'fontSize' => 25,
        // 是否画混淆曲线
        'useCurve' => true,
        // 验证码图片高度
        'imageH'   => 40,
        // 验证码图片宽度
        'imageW'   => 240,
        // 验证码位数
        'length'   => 4,
        // 验证成功后是否重置
        'reset'    => true
    ],
];