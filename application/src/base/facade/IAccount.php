<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-31
 * Time: 15:52
 */

namespace app\src\base\facade;

interface IAccount
{
    function loginByCode($mobile,$code,$country);

    function login($username, $password,$type,$country);

    function register($entity);

    function getInfo($id);

    function update($uid,$entity);

    function updatePwd($map,$newPwd);

    function delete($entity);

    function autoLogin($uid,$auth_code);

}