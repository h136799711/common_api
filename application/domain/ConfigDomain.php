<?php
/**
 * 配置领域模型
 * @author hebidu <email:346551990@qq.com>
 */

namespace app\domain;

use app\src\config\helper\ConfigHelper;
use app\src\base\utils\CacheUtils;

/**
 * Class ConfigDomain
 * @author hebidu <email:346551990@qq.com>
 * @package app\domain
 */
class ConfigDomain extends BaseDomain{

    /**
     * APP配置信息获取
     * @author hebidu <email:346551990@qq.com>
     */
    public function app(){

        $this->checkVersion(100);
        
        $result = CacheUtils::getAppConfig(600);

        if($result === false){
            $this->apiReturnErr('请重新获取');
        }

        $this->apiReturnSuc($result);
    }

    /**
     * 系统支持的app支付方式
     * @author hebidu <email:346551990@qq.com>
     */
    public function supportPayways(){
        $this->checkVersion(100);
        $config = ConfigHelper::app_support_payways();
        $this->apiReturnSuc($config);
    }

}