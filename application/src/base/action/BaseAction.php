<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-15
 * Time: 16:49
 */

namespace app\src\base\action;


use app\src\base\exception\BusinessException;
use app\src\base\helper\ExceptionHelper;
use think\Exception;

class BaseAction
{
    protected function result($data){
        if($data['status']){
            return $this->success($data['info']);
        }else{
            return $this->error($data['info']);
        }
    }

    protected function success($data){
        return ['status'=>true,'info'=>$data];
    }
    
    protected function error($data){

        if($data instanceof  BusinessException){
            $data = "BY_".$data->getMessage();
        }
        elseif($data instanceof  Exception){
            $data = ExceptionHelper::getErrorString($data);
        }

        return ['status'=>false,'info'=>$data];
    }
}