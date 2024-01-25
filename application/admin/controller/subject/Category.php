<?php

namespace app\admin\controller\subject;


//继承后台框架公共控制器,会自动执行相同的js方法
use app\common\controller\Backend;

class Category extends Backend
{
    public $model = null;
    protected $searchFields = 'id,name';//配置快速搜索的字段
    public function __construct()
    {
        parent::__construct();
        $this->model=model('Subject.Category');
    }
    // 查看页数据获取
    public function index()
    {
        if($this->request->isAjax()){
            // 获取所有参数
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            // 获取数量和列表
            $count=$this->model->where($where)->order($sort,$order)->limit($offset,$limit)->count();
            $list=$this->model->where($where)->order($sort,$order)->limit($offset,$limit)->select();
            // 拿到参数返回
            $resu=['total'=>$count,'rows'=>$list];
            return json($resu);
        }
       return $this->view->fetch();
    }
    // 添加课程
    public function add(){
       
        if($this->request->isPost()){
            $data=$this->request->param('row/a');
            // 向数据库插入一条数据
            $resu=$this->model->validate('common/Subject/Category')->save($data);
            if($resu===false){
                $this->error($this->model->getError());
                exit;
            }
            exit;
        };

        // 一开始需要查询最大权重值
        $max=$this->model->max('weight');
        $this->assign('max',$max);
        
        return $this->view->fetch();
    }
    // 编辑
    public function edit($ids=null){
        // 获取id的信息
        $data=$this->model->find($ids);
        // 判断是否存在
        if(!$data){
            $this->error(__('No results were found'));
            exit;
        }
        if($this->request->isPost()){
            $data=$this->request->param('row/a');
            $data['id']=$ids;
            $this->model->isUpdate(true)->save($data);
            if($data===false){
                $this->error($this->model->getError());
                exit;
            }
            $this->success();
            exit;
        }
        $this->assign('data',$data);
        return $this->view->fetch();
    }
    // 删除
    public function del($ids=null){
        // 获取id的信息
        $data=$this->model->find($ids);
        // 判断是否存在
        if(!$data){
            $this->error(__('No results were found'));
            exit;
        }
        // destroy可删除多个也可删除单个
        $state=$this->model->destroy($ids);
        if($state===false){
            $this->error();
            exit;
        }
        $this->success();
        exit;
    }
}
