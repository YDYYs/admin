<?php

namespace app\admin\controller\subject;


//继承后台框架公共控制器,会自动执行相同的js方法
use app\common\controller\Backend;

class Order extends Backend
{
    public $model = null;
    protected $searchFields = 'id,name';//配置快速搜索的字段
    public function __construct()
    {
        parent::__construct();
        $this->model=model('Subject.Order');
        $this->CateModel=model('Subject.Subject');
    }
    // 查看页数据获取
    public function index($ids=null)
    {  
        // 过滤请求过来的一些参数
        $this->request->filter(['strip_tags', 'trim']);

        // 获取所有参数
         if($this->request->isAjax()){
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            // 拿到id
            // 获取数量和列表
            $count=$this->model->order($sort,$order)->limit($offset,$limit)->count();
            $list=$this->model->with('record')->order($sort,$order)->limit($offset,$limit)->select();
            $list=collection($list)->toArray();
            // var_dump($list);
            // exit;
            // 拿到参数返回,key必须是这两个
            $resu=['total'=>$count,'rows'=>$list];
            return json($resu);
        }
       return $this->view->fetch();
    }
}
