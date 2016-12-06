<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-12
 * Time: 9:54
 */

namespace app\src\base\facade;


/**
 * 用户信息相关统一调用
 * Class AccountFacade
 * @package app\src\base\interfaces
 */
class AccountFacade Implements IAccount{

    private  $IAccount;

    function __construct(IAccount $account){
        $this->IAccount = $account;
    }

    /**
     * @param $uid
     * @param $auth_code
     * @return mixed
     */
    function autoLogin($uid, $auth_code)
    {
        return $this->IAccount->autoLogin($uid,$auth_code);
    }


    /**
     * 删除
     * @param $entity
     * @return mixed
     */
    function delete($entity)
    {
        return $this->IAccount->delete($entity);
    }

    /**
     * @param $mobile
     * @param $code
     * @param $country
     * @return mixed
     */
    function loginByCode($mobile, $code, $country)
    {
        return $this->IAccount->loginByCode($mobile,$code,$country);
    }


    /**
     * 登录
     * @param $username
     * @param $password
     * @param $type
     * @param $country
     * @return mixed
     */
    function login($username, $password, $type ,$country)
    {
        return $this->IAccount->login($username,$password,$type,$country);
    }

    /**
     * 注册
     * @param $entity
     * @return mixed
     */
    function register($entity)
    {
        return $this->IAccount->register($entity);
    }

    /**
     * 获取用户信息
     * @param $id
     * @return mixed
     */
    function getInfo($id)
    {
        return $this->IAccount->getInfo($id);
    }

    /**
     * 更新
     * @param $uid
     * @param $entity
     * @return mixed
     */
    function update($uid, $entity)
    {
        return $this->IAccount->update($uid,$entity);
    }

    /**
     * 更新密码
     * @param $map
     * @param $newPwd
     * @return mixed
     * @internal param $uid
     * @internal param $oldPwd
     */
    function updatePwd($map, $newPwd)
    {
        return $this->IAccount->updatePwd($map,$newPwd);
    }


}