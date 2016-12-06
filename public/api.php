<?php
// [ 应用入口文件 ]


// 绑定当前访问到index模块
define('BIND_MODULE','api');

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

// 定义运行时目录
define('PROJECT_NAME','itboye_common_api');

define('RUNTIME_PATH',__DIR__ . '/../../../Runtime/'.PROJECT_NAME.'/');


// 开始运行时间和内存使用
define('START_TIME', microtime(true));
define('START_MEM', memory_get_usage());
//环境变量
define('IS_CGI',(0 === strpos(PHP_SAPI,'cgi') || false !== strpos(PHP_SAPI,'fcgi')) ? 1 : 0 );
define('IS_CLI_', PHP_SAPI == 'cli' ? true : false);
define('NOW_TIME', $_SERVER['REQUEST_TIME']);
define('REQUEST_METHOD', IS_CLI_ ? 'GET' : $_SERVER['REQUEST_METHOD']);
define('IS_AJAX', (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false);
define('IS_GET', REQUEST_METHOD == 'GET' ? true : false);
define('IS_POST', REQUEST_METHOD == 'POST' ? true : false);

if(!IS_CLI_) {
    // 当前文件名
    if(!defined('_PHP_FILE_')) {
        if(IS_CGI) {
            //CGI/FASTCGI模式下
            $_temp  = explode('.php',$_SERVER['PHP_SELF']);
            define('_PHP_FILE_',    rtrim(str_replace($_SERVER['HTTP_HOST'],'',$_temp[0].'.php'),'/'));
        }else {
            define('_PHP_FILE_',    rtrim($_SERVER['SCRIPT_NAME'],'/'));
        }
    }
    if(!defined('__ROOT__')) {
        $_root  =   rtrim(dirname(_PHP_FILE_),'/');
        $_root  =   rtrim(dirname($_root),'/');
        define('__ROOT__',  (($_root=='/' || $_root=='\\')?'':$_root));
    }
}

// 加载框架引导文件
require __DIR__ . '/../../../thinkphp/think-5.0.3/start.php';