<?php

namespace app\admin\controller\product;

use app\common\controller\Backend;
use think\Controller;
use think\Request;

class Units extends Backend
{
    public function __construct()
    {
        parent::__construct();
        $this->model=model('Product.Units');
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
            $resu=['total'=>$count,'rows'=>$data];
            return json($resu);
        }
        //
        return $this->view->fetch();
    }

    public function add(){
        if($this->request->isAjax()){
            $this->request->filter(['strip_tags', 'trim']);
            $req=$this->request->param('row/a');
            $state=$this->model->save($req);
            if($state===false){
                $this->error($this->model->getError());
            }else{
                $this->success();
                exit;
            }
        }
        return $this->view->fetch();
    }

    public function sedit($ids){
        $unitname= $this->model->find($ids);
        if($this->request->isAjax()){
            $this->request->filter(['strip_tags', 'trim']);
            $req=$this->request->param('row/a');
            $req['id']=$ids;
            $state=$this->model->isUpdate(true)->save($req);
            if($state===false){
                $this->error($this->model->getError());
            }else{
                $this->success();
            }
        }
        $this->assign('unitname',$unitname['unitname']);
        return $this->view->fetch();
    }
    
}
