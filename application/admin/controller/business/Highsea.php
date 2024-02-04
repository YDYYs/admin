<?php

namespace app\admin\controller\business;

use app\common\controller\Backend;

class Highsea extends Backend
{
    public function __construct(){
        parent::__construct();
        $this->model=model('Business.Business');
        // 管理员模型
        $this->AdminModel=model('Admin.Admin');
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // exit;
        //公海链表查询所有()
        if($this->request->isAjax()){
            $this->request->filter(['strip_tags', 'trim']);

            // 拿到所有条件
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $data=$this->model->with('sourcetab,admintab')->where($where)->order($sort,$order)->limit($offset,$limit)->select();
            $count=$this->model->with('sourcetab')->where($where)->order($sort,$order)->count();
            $resu=['total'=>$count,'rows'=>$data];
            return json($resu);
            exit;
        }
        return $this->view->fetch();
    }
    public function get($ids=null){
        $this->request->filter(['strip_tags', 'trim']);

        // 单条领取
        if($this->request->isAjax()){
            $array = explode(",", $ids);
            $num=0;
            foreach ($array as $key => $item) {
                 // 查看用户是否被领取
                $state=$this->model->where(['id'=>$item])->find();
                if($state['adminid'] !==null){
                    $msg=$state['nickname']."用户已被人领取<br/>";
                    $this->error($msg);
                    exit;
                }
                // 拿到登录的用户id
                $uid=$this->auth->id;
                // 修改到当前用户
                $data=['id'=>$ids,'adminid'=>$uid];
                $state=$this->model->isUpdate(true)->save($data);
                if($state===false){
                    $this->error($this->model->getError());
                    exit;
                }
                ++$num;
            }
            $this->success($num.'个领取成功');
            exit;
        }
    }
    public function allot($ids=null){ 
        $this->request->filter(['strip_tags', 'trim']);
       
        if($this->request->isAjax()){
            $toadmin=$this->request->param('toadmin','','trim');
            // 判断这个人有没有被领取
            $statr=$this->model->find($ids);
            $user=$statr->toArray();
            if($statr['adminid']){
                $this->error('该用户已被领取');
                exit;
            }
            $data=['id'=>$ids,'adminid'=>$toadmin];
            // 修改
            $resu=$this->model->isUpdate(true)->save($data);
            if($resu===false){
                $this->error($this->model->getError());
                exit;
            }
            $this->success();
            exit;
        }
        // 查出用户信息把Nickname给模版
        $user= $this->model->find($ids);
        // 查询出所有的业务员
        $adminusser=$this->AdminModel->column('id,nickname');
        // 生成下拉列表,并且配上name
        $this->assign('adminlist',build_select('row[adminids]',$adminusser,[],['class'=>'selectpicker','name'=>'toadmin','required'=>'']));
        $this->assign('user',$user);
        return $this->view->fetch();
    }
    // 回收站
    public function recyclebin($ids=null){
        $this->request->filter(['strip_tags', 'trim']);
        $this->model=model('Business.Business');
        if($this->request->isAjax()){
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $data=$this->model->onlyTrashed()->with('sourcetab')->where($where)->order($sort,$order)->limit($offset,$limit)->select();
            $count=$this->model->onlyTrashed()->count();
            $resu=['total'=>$count,'rows'=>$data];
            return json($resu);
        }
    }
    // 回收站删除
    public function del($ids=null){
        $this->request->filter(['strip_tags', 'trim']);
        if($this->request->isAjax()){
            if(!$ids){
                $this->error('未传入id');
            };
            $state=$this->model->destroy($ids,true);
            if($state){
                $this->success();
                exit;
            }
            $this->error($this->model->getError());
            exit;
        }
        
    }
}
