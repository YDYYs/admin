<?php

namespace app\admin\controller\depot;

use app\common\controller\Backend;
class Supplier extends Backend
{
    public function __construct()
    {
        parent::__construct();
        $this->model=model('Depot.Depot');
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if($this->request->isAjax()){
            $this->request->filter(['strip_tags', 'trim']);
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $data=$this->model->where($where)->order($sort,$order)->limit($offset,$limit)->select();
            $count=$this->model->count();
            $resu=['total'=>$count,"rows"=>$data];
            return json($resu);
        }
        return $this->view->fetch();
    }
    
    public function add()
    {
        //
        if($this->request->isAjax()){
            $this->request->filter(['strip_tags', 'trim']);
            $row=$this->request->param('row/a');
            $state = $this->model->save($row);
            if($state==false){
                $this->error($this->model->getError());
            }else{
                $this->success();
                exit;
            }
        }
        return $this->view->fetch();
    }
    public function sedit($ids=null)
    {
        //
        $data=$this->model->find($ids);
        if($this->request->isAjax()){
            $this->request->filter(['strip_tags', 'trim']);
            $list=$this->request->param('row/a');
            $list['id']=$ids;
            $state=$this->model->isUpdate(true)->save($list);
            if($state==false){
                $this->error($this->model->getError());
            }else{
                $this->success();
                exit;
            }
        }
        $this->assign('data',$data);
        return $this->view->fetch();
    }
    
}
