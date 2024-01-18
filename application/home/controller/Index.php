<?php
namespace app\home\controller;

//继承TP的默认的控制器
use app\common\controller\Home;

class Index extends Home {
	// 这个在父类中可以拿到
	protected $nologinarr=['register','user','login'];

	public function __construct() {
		parent::__construct();
		// 公共模型
		$this->BusinessModel = model('Business.Business');
	}

	// 操作方法名（默认为index）
	public function index() {
		//加载页面
		return $this->view->fetch();
	}

	//注册方法
	public function register() {
		// 判断是否登录过
		$id=cookie('id');
		$mobile=cookie('mobile');
		// 如果登录了就不让进，直接跳到会员中
		if($id && ！empty($mobile)){
			// 重定向
			$this->redirect(url('home/index/center'));
			exit;
		}

		if ($this->request->isPost()) {

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
				$this->error($BusinessModel->getError());
				exit;
			} else {
				$this->success('注册成功', url('home/index/login'));
				exit;
			}
		}
		return $this->view->fetch();
	}
    // 用户
	public function user() {
		return $this->view->fetch(); //显示页面
	}

    // 登录方法
	public function login() {
		// 判断是否登录过
		$id=cookie('id');
		$mobile=cookie('mobile');
		
		// 如果有id且手机号不为空，就不让进，直接跳到会员中
		if($id && !empty($mobile)){
			$this->redirect(url('home/index/center'));
			exit;
		}

		if ($this->request->isPost()) {
			$mobile = $this->request->param('mobile', '', 'trim');
			$password = $this->request->param('password', '', 'trim');
			//加载模型 处理数据库
			$BusinessModel = model('common/Business/Business');
			// 查看手机号是否存在
			$business = $BusinessModel->where(['mobile' => $mobile])->find();
			// exit;
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

			$findres = $BusinessModel->where(['mobile' => $mobile, 'password' => $password])->find();
			if (!$findres) {
				// 查询不成功
				$this->error('密码错误');
				exit;
			}

			// 写入cookie
			cookie('id', $business['id']);
			cookie('mobile', $business['mobile']);

			$this->success('登录成功',url('home/business/index')); //跳转到个人中心页面
			exit;

		}
		return $this->view->fetch();
	}

    // 退出登录
    public function loginout(){
    	// echo "退出登录";
    	cookie('id',null);
    	cookie('mobile',null);
    	exit;
    }
}