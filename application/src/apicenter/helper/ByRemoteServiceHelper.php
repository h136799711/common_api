<?php

namespace  app\src\apicenter\helper;
use app\src\base\utils\BoyeService;

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-15
 * Time: 17:32
 */
class ByRemoteServiceHelper
{
    private $client_id;
    private $client_secret;
    private $api_url;
    public function __construct()
    {
        $common_api_config = config('common_api_config');
        $this->client_id = $common_api_config['client_id'];
        $this->client_secret = $common_api_config['client_secret'];
        $this->api_url = $common_api_config['api_url'];
        $this->service = new BoyeService($this->api_url,$this->client_id,$this->client_secret);
    }

    private static $instance;
    private $service;

    public static function getInstance(){
        if(empty(self::$instance)){
            self::$instance = new ServiceHelper();
        }
        return self::$instance;
    }

    public function call($data){
        return $this->service->callRemote($data,false);
    }
}