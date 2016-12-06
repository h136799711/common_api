<?php
/**
 * 公共函数库
 * Created by PhpStorm.
 * User: hebidu
 * Date: 16/4/17
 * Time: 16:12
 */

// 或者进行自动检测语言
\think\Lang::detect();

/**
 * 记录日志，系统运行过程中可能产生的日志
 * Level取值如下：
 * EMERG 严重错误，导致系统崩溃无法使用
 * ALERT 警戒性错误， 必须被立即修改的错误
 * CRIT 临界值错误， 超过临界值的错误
 * WARN 警告性错误， 需要发出警告的错误
 * ERR 一般性错误
 * NOTICE 通知，程序可以运行但是还不够完美的错误
 * INFO 信息，程序输出信息
 * DEBUG 调试，用于调试信息
 * SQL SQL语句，该级别只在调试模式开启时有效
 */
function LogRecord($msg, $location, $level = 'ERR') {
    \think\Log::write($location . $msg, $level);
}

/**
 * 接口日志记录
 * @param $api_uri
 * @param $get
 * @param $post
 * @param $notes
 * @param bool $onlyDebug
 * @throws \think\Exception
 */
function addLog($api_uri,$get,$post,$notes,$onlyDebug=false){

    if($onlyDebug && config('app_debug') == false){
        return ;
    }

    $model = db('ApiCallHis');

    if(is_array($get)){
        $get = json_encode($get);
    }
    if(is_array($post)){
        $post = json_encode($post);
    }

    $post    = is_null($post)?"null":$post;
    $get     = is_null($get)?"null":$get;
    $api_uri = empty($api_uri)?"":$api_uri;


    $model->insert(array(
        'api_uri'=>$api_uri,
        'call_get_args'=>$get,
        'call_post_args'=>$post,
        'notes'=>$notes,
        'call_time'=>NOW_TIME,
    ));

}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}


if(!function_exists('think_ucenter_md5')){

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function think_ucenter_md5($str, $key = 'ThinkUCenter'){
    return '' === $str ? '' : md5(sha1($str) . $key);
}
}

/**
 * 密码加密
 * @param $str
 * @param string $key
 * @return string
 */
function itboye_ucenter_md5($str, $key = 'ITBOYE'){
    return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 (单位:秒)
 * @return string
 */
function think_ucenter_encrypt($data, $key, $expire = 0) {
    $key  = md5($key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char =  '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x=0;
        $char  .= substr($key, $x, 1);
        $x++;
    }
    $str = sprintf('%010d', $expire ? $expire + time() : 0);
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data,$i,1)) + (ord(substr($char,$i,1)))%256);
    }
    return str_replace('=', '', base64_encode($str));
}

/**
 * 系统解密方法
 * @param string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param string $key  加密密钥
 * @return string
 */
function think_ucenter_decrypt($data, $key){
    $key    = md5($key);
    $x      = 0;
    $data   = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data   = substr($data, 10);
    if($expire > 0 && $expire < time()) {
        return '';
    }
    $len  = strlen($data);
    $l    = strlen($key);
    $char = $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char  .= substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}


/**
 * @desc  im:十进制数转换成三十六机制数
 * @param (int)$num 十进制数
 * @return bool|string
 */
function get_36HEX($num) {
    $num = intval($num);
    if ($num <= 0)
        return 0;
    $charArr = array("0","1","2","3","4","5","6","7","8","9",'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $char = '';
    do {
        $key = ($num - 1) % 36;
        $char= $charArr[$key] . $char;
        $num = floor(($num - $key) / 36);
    } while ($num > 0);
    return $char;
}

/**
 * 自定义语言变量
 * @param $str  字符串
 * @param $dif  分割符
 * @param $add  链接符
 * @return string is8n字符串
 * add by zhouhou
 */
function LL($str='',$dif=' ',$add = ''){
    return implode($add,array_map('lang',explode($dif, trim($str))));
}
/**
 * lang() alias 方法别名
 * @param [type] $name [description]
 * @param array  $vars [description]
 * @param string $lang [description]
 */
function L($name, $vars = [], $lang = '')
{
    return \think\Lang::get($name, $vars, $lang);
}

/**
 * 小周的socketLog
 * @param $log
 * @param string $type
 * @param string $user
 */
function slog($log, $type = '', $user = false){
    if(config('XSOCKET_LOG')===false) return;
    if(!$user) $user = config('XSOCKET_LOG_USER');
    $socketlog = new xsocketlog\socketlog($user);
    $socketlog->send($log, $type);
}