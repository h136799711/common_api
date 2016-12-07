<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-07
 * Time: 9:51
 */

namespace app\src\apicenter\model;


use think\Model;

abstract  class BaseModel extends Model
{

    abstract  function toPoArray();

}