<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-20
 * Time: 18:29
 */

namespace app\index\controller;

 
use app\src\config\helper\ConfigHelper;
use think\Controller;

class Js extends Controller
{
    public function itboye(){
        header('Content-type: application/javascript; charset=utf-8');
        $version = "1.0.0";
        $site_url = ConfigHelper::site_url();
        
        //普通模式	0,兼容模式	3
        if(config('URL_MODEL') == 0 || config('URL_MODEL') == 1) {
            $site_url .= "/index.php";
        }

        $config = "function initItboye(){
            window.itboye = window.itboye || {};
            window.itboye.version = '".$version."';
            window.itboye.api_url = '".$site_url."';

		    console.log('初始化成功!');
		}";

        echo ($config);
        echo "console.log('******(*^__^*) *********');";
        echo "console.log('Welcome to Itboye\'s World!');";
        echo "console.log('******(*^__^*) *************');";
        echo "initItboye();";


        exit();
    }
}