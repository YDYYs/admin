<?php

namespace app\shop\controller;

use think\Controller;
use think\Request;

class Business extends Controller
{
    public function __construct(){
        parent::__construct();
        $this->BusinessModel=model('Business.Business');
    }
    // 登录
    public function login()
    {
        if($this->request->isPost()){
            $mobile=$this->request->param('mobile','','trim');
            $password=$this->request->param('password','','trim');
            // 查看手机号是否存在
			$business = $this->BusinessModel->where(['mobile' => $mobile])->find();
			if (!$business) {
				$this->error('该手机号用户不存在');
				exit;
			}
			if (empty($password)) {
				$this->error('密码不能为空');
				exit;
			}
			// 拿到数据库的密码盐
			// $salt=$BusinessModel->where(['mobile'=>$mobile])->find();
			$salt = $business['salt'];
			$password = md5($password . $salt);

			$findres = $this->BusinessModel->where(['mobile' => $mobile, 'password' => $password])->find();
			if (!$findres) {
				// 查询不成功
				$this->error('密码错误');
				exit;
			}
			$this->success('登录成功','/my'); //跳转到个人中心页面
			exit;
        }
    }
    // 注册
    public function register(){
        if ($this->request->isAjax()) {
			$mobile = $this->request->param('mobile', '', 'trim');
			$password = $this->request->param('password', '', 'trim');
            $this->request->param();
			//加载模型 处理数据库
			$BusinessModel = model('common/Business/Business');

			// 引入模型查找外键id
			$SourceModel = model('Business.Source');
			// var_dump($SourceModel);
			// 获得外键id,注册到数据库中记录注册来源于哪个平台
			$sourceid = $SourceModel->where(['name' => ['LIKE', "%云课堂%"]])->value('id');
			// 生成随机密码盐
			$salt = build_ranstr();
			// 加密
			$pasd = md5($password . $salt);

			// 组装
			$data = [
				'mobile' => $mobile,
				'nickname' => $mobile,
				'password' => $pasd,
				'salt' => $salt,
				'gender' => '0',
				'sourceid' => $sourceid,
				'deal' => '0',
				'money' => '0',
				'auth' => '0',
			];
			// 执行验证器并插入语句
			$resu = $BusinessModel->validate('common/Business/Business')->save($data);

			if ($resu === false) {
				$this->error('注册失败');
				exit;
			} else {
				$this->success('注册成功', '/login');
				exit;
			}
		}
    }
}
