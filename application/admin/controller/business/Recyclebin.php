<?php

namespace app\admin\controller\business;

use app\common\controller\Backend;

class Recyclebin extends Backend
{
    public function __construct(){
        parent::__construct();
        $this->model=model('Business.Business');
        // 管理员模型
        $this->AdminModel=model('Admin.Admin');
    }
    
    public function index(){
        return $this->view->fetch();
    }
    public function recyclebin(){
        echo "sjgjsodigj";
    }
}
