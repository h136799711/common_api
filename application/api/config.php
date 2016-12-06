<?php

return [

    //支持裁减大小宽度
    'picture_crop_size'  => [50,60,120,150,160,180,200,240,360,400,480,640,720,960],

    // 默认输出类型
    'default_return_type'    => 'json',
    // 异常页面的模板文件
    // 'exception_tmpl'         => APP_PATH . 'index' . DS . 'view/exception.json',
    // 异常处理忽略的错误类型，支持PHP所有的错误级别常量，多个级别可以用|运算法
    // 参考：http://php.net/manual/en/errorfunc.constants.php
    'exception_ignore_type'  => 0,
    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 错误定向页面
    'error_page'             => '',
    // 显示错误信息
    'show_error_msg'         => false,


];