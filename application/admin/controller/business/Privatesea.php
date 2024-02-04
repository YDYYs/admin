<?php

namespace app\admin\controller\business;

use app\common\controller\Backend;
use Think\Db;

class Privatesea extends Backend
{
    protected $model=null;

    //开启关联查询
    protected $relationSearch = true;

    //数据限制
    protected $dataLimit = 'personal';  //只显示自己的数据
    protected $dataLimitField = 'adminid'; //限制的字段

    public function __construct(){
        parent::__construct();//继承父类方法
        $this->model=model('Business.Business');
        $this->SourceMode=model('Business.Source');
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //过滤掉请求中所有参数的标签和两边空白
        $this->request->filter(['strip_tags', 'trim']);

        // 获取adminid属于自己的
        if($this->request->isAjax()){
            //从后台的控制器基类中获取到以下参数
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

             // 是否超管
            $getGp=$this->auth->getGroupIds();
            if($getGp[0]===1){
                // echo "超管";
                // 查询所有被人领取的数据，使用了db需要引入是Think的方法
                $authwhere = ['adminid' => ['exp',Db::raw('is not null')]];
            }else{
                // echo "普通用户";
                $authwhere=['adminid'=>$getGp[0]];
            }
            // 查询
            $total=$this->model
            ->with(['sourcetab','admintab'])
            ->where($authwhere)
            ->count();
            
            $data=$this->model
            ->with(['sourcetab','admintab'])
            ->where($where)
            ->where($authwhere)
            ->order($sort,$order)
            ->limit($offset,$limit)
            ->select();
            $resu=['total'=>$total,'rows'=>$data];
            return json($resu);
        }
        return $this->view->fetch();
    }
    public function recycle($ids=null){
        //过滤掉请求中所有参数的标签和两边空白
        $this->request->filter(['strip_tags', 'trim']);
        // 去除adminid
        if($this->request->isAjax()){
            $data=['id'=>$ids,'adminid'=>null];
            $state= $this->model->isUpdate(true)->save($data);
            if($state===false){
                $this->error();
                exit;
            }
            $this->success();
            exit;
        }
    }
    // 详情选项页面
    public function detail($ids=null){
       return $this->view->fetch();
    }
    // 详情首页详情
    public function userdetail($ids=null){
        //过滤掉请求中所有参数的标签和两边空白
        $this->request->filter(['strip_tags', 'trim']);
        if($this->request->isAjax()){
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            // 查询用户信息
            $data=$this->model->with('admintab,sourcetab')->where($where)->order($sort,$order)->limit($offset,$limit)->select($ids);//表格渲染是需要返回一个二维数组，所以需要使用select，不能使用find
            $resu=['total'=>'1','rows'=>$data];
            return json($resu);
        }
        
    }
    // 申请记录
    public function recode($ids=null){
        $this->model=model('Admin.Receive');
        //过滤掉请求中所有参数的标签和两边空白
        $this->request->filter(['strip_tags', 'trim']);
        if($this->request->isAjax()){
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            if(empty($ids)){
                $this->error('id错误');
                exit;
            }
            $data=$this->model->with('admintab')->where($where)->where(['busid'=>$ids])->order($sort,$order)->limit($offset,$limit)->select();
            $count=$this->model->where(['busid'=>$ids])->count();
            $resu=['total'=>$count,'rows'=>$data];
            return json($resu);
        }
    }
    // 回访列表
    public function interview($ids=null){
        //过滤掉请求中所有参数的标签和两边空白
        $this->model=model('Admin.Visit');
        $supid=$this->auth->getGroupIds();
        if($this->request->isAjax()){
            $this->request->filter(['strip_tags', 'trim']);
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            // 查询是该用户和该超管的回访记录
            $data=$this->model->with('admintab')->where($where)->where(['busid'=>$ids,'adminid'=>$supid[0]])->order($sort,$order)->limit($offset,$limit)->select();
            $count=$this->model->where(['busid'=>$ids,'adminid'=>$supid[0]])->count();
            $resu=['total'=>$count,'rows'=>$data];
            return json($resu);
        }
    }

    // 消费记录
    public function consume($ids=null){
        $this->model=model('Business.Record');
        if($this->request->isAjax()){
            $this->request->filter(['strip_tags', 'trim']);
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $data=$this->model->where($where)->where(['busid'=>$ids])->order($sort,$order)->limit($offset,$limit)->select();
            $count=$this->model->where(['busid'=>$ids])->count();
            $resu=['total'=>$count,'rows'=>$data];
            return json($resu);
        }
    }

    // 课程订单
    public function subject($ids=null){
        $this->model=model('Subject.Order');
        if($this->request->isAjax()){
            $this->request->filter(['strip_tags', 'trim']);
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $data=$this->model->with('record')->where($where)->where(['busid'=>$ids])->order($sort,$order)->limit($offset,$limit)->select();
            $count=$this->model->where(['busid'=>$ids])->count();
            $resu=['total'=>$count,'rows'=>$data];
            return json($resu);
        }
    }

    // 回访添加
    public function interviewadd($ids=null){
        $this->model=model('Admin.Visit');
        if($this->request->isAjax()){
            // ids是用户的id，再拿到超管的id
            $id=$this->auth->getGroupIds()[0];
            $this->request->filter(['strip_tags', 'trim']);
            // 获取表单的数据
            $data=$this->request->param('row/a');
            $data['busid']=$ids;
            $data['adminid']=$id;
            $state=$this->model->save($data);
            if($state==false){
                $this->error($this->model->getError);
                exit;
            }
            $this->success();
            exit;
        }
        return $this->view->fetch();
    }

    // 编辑
    public function interviewedit($ids=null){
        $this->model=model('Admin.Visit');
        // $data=$this->model->find($ids);
        // if($this->request->isAjax()){
        //     // echo 'sjglksjdlg';
        //     $data=$this->request->param('row/a');
        //     if(empty($data['email'])){
        //         unset($data['email']);
        //     }
        //     if(empty($data['password'])){
        //         unset($data['password']);
        //     }else{
        //         // $a=build_ranstr('');
        //     }
        //     var_dump($data);
        //     exit;
        // }
        $data=$this->model->where(['id'=>$ids])->value('content');
        $this->assign('content',$data);
        if($this->request->isAjax()){
            //过滤掉请求中所有参数的标签和两边空白
            $this->request->filter(['strip_tags', 'trim']);
            $data=$this->request->param('row/a');
            $data['id']=$ids;
            $state= $this->model->isUpdate(true)->save($data);
            if($state==false){
                $this->error($this->model->getError());
                exit;
            }
            $this->success();
            // exit;
        }

        // var_dump($data->toArray());
        // $this->assign('row',$data);
        // // 生成性别列表
        // $this->assign('genderlist',build_select('row[sex]',[0=>'保密',1=>'男',2=>'女'],[],['class'=>'selectpicker','required'=>'']));
        // // 成交状态
        // $this->assign('deallist',build_select('row[deal]',[0=>'未成交',1=>'已成交'],$this->model->find($ids)->value('deal'),['class'=>'selectpicker','required'=>'']));
        // $this->assign('authlist',build_select('row[auth]',[0=>'未验证',1=>'已验证'],$this->model->find($ids)->value('auth'),['class'=>'selectpicker','required'=>'']));
        // $this->assign('sourcelist',build_select('row[soureceid]',$this->SourceMode->column('id,name'),$this->model->find($ids)->value('sourceid'),['class'=>'selectpicker','required'=>'']));
        // exit;
        return $this->view->fetch();
    }
}
