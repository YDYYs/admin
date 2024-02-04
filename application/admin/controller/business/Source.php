<?php

namespace app\admin\controller\business;

use app\common\controller\Backend;

class Source extends Backend
{
    public function __construct(){
        parent::__construct();
        $this->model=model('Business.Source');
        // 管理员模型
        $this->AdminModel=model('Admin.Admin');
    }
    
    public function index(){
        if($this->request->isAjax()){
            // 过滤请求的一些参数
            $this->request->filter(['strip_tags', 'trim']);
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $data=$this->model->where($where)->order($sort,$order)->limit($offset,$limit)->select();
            $count=$this->model->count();
            $resu=['total'=>$count,'rows'=>$data];
            return json($resu);
        }
        return $this->view->fetch();
    }
    public function add($ids=null){
        if($this->request->isAjax()){
            $this->request->filter(['strip_tags', 'trim']);
           $data= $this->request->param('row/a');
           if(empty($data['name'])){
                $this->error('名称不能为空');
                exit;
           }
           $state=$this->model->save($data);
           if($state==false){
             $this->error($this->model->getError());
             exit;
           }else{
            $this->success();
           }
        };
        return $this->view->fetch();
    }
    public function edit($ids=null){
        if($this->request->isAjax()){
            $data=$this->request->param('row/a');
            $data['id']=$ids;
            $state= $this->model->isUpdate(true)->save($data);
            if($state==false){
                $this->error($this->model->getError());
                exit;
            }else{
                $this->success();
            }
        }


        $data=$this->model->where(['id'=>$ids])->value('name');
        $this->assign('name',$data);
        return $this->view->fetch();
    }
    
}
