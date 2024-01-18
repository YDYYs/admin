<?php

namespace app\common\controller;

use think\Controller;

/**
 * 前台控制器基类
 */
class home extends Controller
{
    // 记录不需要登录的方法名，定义一个默认的空数组
    protected $nologinarr=[];

    public function __construct(){
        parent::__construct();
        // 模型
        $this->BusinessModel=model('Business.Business');
        // 拿到子类的nologinarr的数组，判断当前方法在不在里面
        
        // 如果数组中没有* 也没有地址方法，则跳过登录验证，如果有其中一个就验证
        $x=in_array("*",$this->nologinarr);
        $f=in_array($this->request->action(),$this->nologinarr);
        if($x || !$f){
            // 需要登录，进入登录方法中
            $this->islogin();
            // echo "需要登录";
        }

   }
   // 判断登录方法
   public function islogin(){
        // 获取
        $id=cookie('id')?trim(cookie('id')):0;
        $mobile=cookie('mobile')?trim(cookie('mobile')):'';
       
        // cookie清除
        if(!$id || empty($mobile)){
            cookie('id',null);
            cookie('mobile',null);
            $this->error('请重新登录',url('home/index/login'));
            exit;
        }
        // 通过手机号查询
        $where=['id'=>$id,'mobile'=>$mobile];
        $login=$this->BusinessModel->where($where)->find();
        // 判断用户的有效性
        if(!$login){
            cookie('id',null);
            cookie('mobile',null);
            $this->error('非法登录',url('home/index/login'));
            exit;
        }
        // 将值给模板使用,模板中使用AutoLogin这对象
        $this->view->assign('AutoLogin',$login);
    }
}
