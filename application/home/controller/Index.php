<?php
namespace app\home\controller;

//继承TP的默认的控制器
use think\Controller;

class Index extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // 操作方法名（默认为index）
    public function index()
    {
        //加载页面
        return $this->view->fetch();
    }

    //注册
    public function register()
    {
        if($this->request->isPost())
        {
            // var_dump($_POST);
            // $mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
            $mobile = $this->request->param('mobile', '', 'trim');
            $password = $this->request->param('password', '', 'trim');

            //根据手机号码，判断手机号是否有重复注册过

            //加载模型 处理数据库
            $BusinessModel = model('common/Business/Business');
            
            //链式写法
            $business = $BusinessModel->where(['mobile' => $mobile])->find();
            //查询语句 select * from fa_business WHERE  mobile = '$mobile'

            //开始去调用模型
            // var_dump($mobile);

            //将数据对象转换为数组
            // var_dump($business->toArray());
            // exit;

            if($business)
            {
                $this->error('手机号码已存在，无法注册');
                exit;
            }else
            {
                $this->success('手机号可以注册');
                exit;
            }

            
            var_dump($business);
            exit;
        }
        return $this->view->fetch();
    }
    public function user(){
        // var_dump('user');
        // exit;
        return $this ->view->fetch();//显示页面
    }

    public function login()
    {
        return $this->view->fetch();
    }
}