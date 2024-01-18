<?php
namespace app\home\controller;

//继承TP的默认的控制器
use app\common\controller\Home;

// class名称要和文件名一样，开头字母大写
class Business extends Home {
	// 构造器
	public function __construct(){
		parent::__construct();//继承父类方法
		// 公共模型
		$this->BusinessModel=model('Business.Business');
		$this->EmsModel=model("Ems");
	}
	// 我的页面
	public function index(){
		return $this->view->fetch();
	}
	// 基本资料
	public function profile(){
		// 提交表单后进来
		if($this->request->isPost()){
		// 判断是否有提交表单
		$params=$this->request->param();//获得所有表单提交的内容
		// 如果有密码需要加密再保存
		$passwd=$this->request->param('password','','trim');
		if ($passwd) {
			// 加密
			// 生成一个随机字符串
			$salt=build_ranstr();
			$passwd=md5($passwd.$salt);
			$params['salt']=$salt;
			$params['password']=$passwd;
		}else{
			// 如果没有输入密码，则把数组中的密码删掉
			unset($params['password']);
		}
		// 邮箱必填
		$email=$this->request->param('email','','trim');

		if (empty($email)) {
			// 抛出错误
			$this->error('邮箱必填');
			exit;
		}else{
			// 判断是不是和之前的一样
			if (!$email==$this->view->AutoLogin['email']) {
				// 修改邮箱后就需要重新认证
				$params['auth']=0;
			}
		}
		// 城市信息
		$region=$this->request->param('region','','trim');
		if(!empty($region))
            {	//获取组件的地区码
                $parent = model('Region')->where(['code' => $region])->value('parentpath');

                //将字符串转换为数组
                $list = explode(',', $parent);
                
                // 判断每一项是不是都有
                if(isset($list[0]))
                {
                    $params['province'] = $list[0];
                }

                if(isset($list[1]))
                {
                    $params['city'] = $list[1];
                }

                if(isset($list[2]))
                {
                    $params['district'] = $list[2];
                }
            }

            // 图片处理判断是否有上传的参数有没有avatar，并且有没有错误
            if(isset( $_FILES['avatar']) && $_FILES['avatar']['error']==0){
				// 有新图片来
				// 判断是否有就图片有就删
				if (isset($this->view->AutoLogin['avatar'])) {
					// 避免错误报错
					@is_file('.'.$this->view->AutoLogin['avatar']) && @unlink('.'.$this->view->AutoLogin['avatar']);
				}

				$result= build_upload('avatar');//传入一个名称,返回一个图片地址并保存到upload目录中
				if ($result['result']) {
					// 成功
					$params['avatar']=$result['data'];
				}else{
					$this->error($result['msg']);
				}
			}
			// 添加id
			$params['id']=$this->view->AutoLogin['id'];
			// 更新数据
			$res = $this->BusinessModel->validate('common/Business/Business.profile')->isUpdate(true)->save($params);
			if($res===false){
				$this->error($this->BusinessModel->getError());
				exit;
			}
			$this->success('更新成功',url('home/business/index'));
			exit;
		}
		return $this->view->fetch();
	}

	// 邮箱认证
	public function email(){
		// 判断是否有Ajax
		if($this->request->isAjax()){
			$receiver=(isset($this->view->AutoLogin->email) && !empty($this->view->AutoLogin->email))?trim($this->view->AutoLogin->email):'';
			if(empty($receiver)){
				$this->error('邮箱为空');
				exit;
			}
			if($this->view->AutoLogin->auth){
				$this->error('已验证');
				exit;
			}
			// 开始生成验证码
			$code=build_ranstr(5);

			// start
			$this->EmsModel->startTrans();
			// del
			$Delete=$this->EmsModel->where(['email'=>$receiver])->delete();
			if($Delete===false){
				$this->error('删除验证码失败');
				exit;
			}
			$data=[
				'event'=>'auth',
				'email'=>$receiver,
				'code'=>$code,
				'time'=>0,
			];

		}
		return $this->view->fetch();
	}
}