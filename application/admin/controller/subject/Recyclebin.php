<?php

namespace app\admin\controller\subject;

use app\common\controller\Backend;


class Recyclebin extends Backend
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return $this->view->fetch();
    }

    
}
