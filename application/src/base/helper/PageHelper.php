<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-10-22
 * Time: 13:50
 */

namespace app\src\base\helper;


class PageHelper
{
    private $page_index;
    private $page_size;

    public function __construct($page)
    {
        if(!is_array($page)) return;

        if(isset($page['page_index'])){
            $this->page_index = max(intval($page['page_index'])-1,0);
        }

        if(isset($page['page_size'])){
            $this->page_size = max(intval($page['page_size']),0);
        }
    }

    public static function renew($page){

        $pageHelper = new PageHelper($page);

        return $pageHelper;
    }

    public function getPageIndex(){
        return $this->page_index;
    }   

    public function getOffset(){
        return $this->getPageIndex() * $this->getPageSize();
    }

    public function getPageSize(){
        return $this->page_size;
    }

}