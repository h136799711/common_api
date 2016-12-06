<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-11-16
 * Time: 9:49
 */

namespace app\src\apicenter\model;


use think\Model;

class App extends Model
{
    protected $id;
    protected $key;

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

        
}