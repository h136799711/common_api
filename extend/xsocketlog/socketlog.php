<?php
/**
 * Created by PhpStorm.
 * User: Zhoujinda
 * Date: 2016/7/11
 * Time: 9:49
 */

namespace xsocketlog;

class socketlog {

    private $user = '';
    private $url  = '';

    function __construct($user, $url = 'http://lolili.cc/socketlog/send') {
        $this->user = $user;
        $this->url = $url;
    }

    public function send($log, $type = '') {
        $data = [
            'user' => $this->user,
            'log'  => $this->dump($log),
            'type' => $type
        ];

        $url = $this->url;
        $ch = curl_init();
        $data = http_build_query($data);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_exec($ch);
        curl_close($ch);
    }

    private function dump($varVal, $isExit = FALSE) {
        ob_start();
        var_dump($varVal);
        $varVal = ob_get_clean();
        $varVal = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $varVal);
        return '<pre>' . $varVal . '</pre>';
    }

}
