<?php
namespace app\home\controller;

//继承TP的默认的控制器
use app\common\controller\Home;

class Index extends Home {
	// 这个在父类中可以拿到
	// protected $nologinarr=['register','user','login'];
	protected $nologinarr=["*"];

	public function __construct() {
		parent::__construct();
		// 公共模型
		$this->BusinessModel = model('Business.Business');
		$this->SubjectModel=model('Subject.Subject');
		$this->ChapterModel=model('Subject.Chapter');
		$this->CommentModel=model('Subject.Comment');
		$this->OrderModel=model('Subject.Order');
		$this->RecordModel=model('Business.Record');
	}

	// 操作方法名（默认为index）
	public function index() {
		// 查询top的课程5
		$top=$this->SubjectModel->where(['flag'=>"top"])->limit(5)->select();
		// 查询推荐课程8
		$hot=$this->SubjectModel->where(['flag'=>"hot"])->limit(8)->select();
		// 将数据推送到模板
		$this->assign([
			'top'=>$top,
			'hot'=>$hot
		]);
		//加载页面
		return $this->view->fetch();
	}
	// 搜索页面
	public function search(){
		if($this->request->isAjax()){
			$search=$this->request->param('search','','trim');
			$page=$this->request->param('page',1,'trim');
			$limit=$this->request->param('limit',10,'trim');
			$where=[];
			if(!empty($search)){
				$where['title']=["LIKE","%$search%"];
			}
			// 设置偏移量
			$start=($page-1)*$limit;
			$count=$this->SubjectModel->where($where)->count();//获得总数
			// 查询所有符号条件的链表查询
			$list=$this->SubjectModel->with(['category'])->where($where)->limit($start,$limit)->select();
		
			$data=[
				'count'=>$count,
				'list'=>$list
			];
			if($list){
				$this->success('返回数据',null,$data);
				exit;
			}else{
				$this->error('暂无更多数据');
				exit;
			}
		}
		return $this->view->fetch();
	}
	// 点赞
	public function like(){
		if($this->request->isAjax()){
			// 传入的是一个课程id，获取cookie中的登录的id信息
			$id=$this->request->param('sid');
			$business=$this->islogin(false);
			$businessid=$business['id'];//用户id
			if(!$businessid){
				$this->error('登录后才能点赞');
				exit;
			}
			// 判断课程是否存在
			$subject=$this->SubjectModel->find($id);
			$subject=$subject->toArray();
			if(!$subject){
				// 查询结果为空，课程不存在
				$this->error('课程不存在');
				exit;
			}
			// 拿到课程的点赞数组
			$sublikearr=$subject['likes'];
			$sublikearr=explode(',',$sublikearr);
			if(in_array($businessid,$sublikearr)){
				// 如果存在那就是已经点赞了，该取消
				$key=array_search($businessid,$sublikearr);//找对应的下标
				array_splice($sublikearr,$key,1);//删除数组中指定一个
				// 更新数据
				$data=[
					'id'=>$id,
					'likes'=>implode(',',$sublikearr)
				];
				$resu=$this->SubjectModel->isUpdate(true)->save($data);
				if($resu === false){
					$this->error('取消点赞失败');
					exit;
				}else{
					$this->success('取消点赞成功');
					exit;
				}
			}else{
				// 添加点赞
				$sublikearr[]=$businessid;
				$data=[
					'id'=>$id,
					'likes'=>implode(',',$sublikearr),
				];
				// 更新
				$resu=$this->SubjectModel->isUpdate(true)->save($data);
				if($resu===false){
					$this->error('点赞失败');
					exit;
				}else{
					$this->success('点赞成功');
					exit;
				}
			}
		}
	}

	// 详情页面
	public function detail(){
		$id=$this->request->param('ids',0,"trim");
		// 判断id
		if(empty($id)){
			$this->error('暂无该详情');
			exit;
		}
		
		// 通过文章id获取数据
		$subject=$this->SubjectModel->find($id);
		// 判断是否登录
		$business=$this->islogin(false);
		$islike=false;
		if($business){
			// 登录过了
			// 显示是否点赞通过id去判断在不在点赞的数组中
			$bid=$business['id'];
			$likes=explode(",",$subject['likes']) ;
			if(in_array($bid,$likes)){
				$islike=true;
			}
		}
		// 查询所有章节 id是文章id
		$chapter=$this->ChapterModel->where(['subid'=>$id])->order('id','asc')->select();
		
		// 查询评论列表
		$comments=$this->CommentModel->with('correlate')->where(['subid'=>$id])->order('createtime','desc')->select();
		// 列表转换
		$comments=collection($comments)->toArray();
	
		$this->assign([
			'subject'=>$subject,
			'chapter'=>$chapter,
			'islike'=>$islike,
			'comment'=>$comments
		]);
		return $this->view->fetch('details');
	}

	//注册方法
	public function register() {
		// 判断是否登录过
		$id=cookie('id');
		$mobile=cookie('mobile');
		// 如果登录了就不让进，直接跳到会员中
		if($id && !empty($mobile)){
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
    	cookie('id',null);
    	cookie('mobile',null);
    	exit;
    }

	// 评论列表页
	public function commentlist(){
		if($this->request->isAjax()){
			$id=$this->request->param('id');
			$page=$this->request->param('page');
			$limit=$this->request->param('limit');
			// 设置偏移量
			$start=($page-1)*$limit;
			$count=$this->CommentModel->where(['subid'=>$id])->count();//获得总数
			// 获取该文章的评论列表
			// 分页获取
			$comments=$this->CommentModel->with('correlate')->where(['subid'=>$id])->limit($start,$limit)->order('createtime','desc')->select();
			$commentslist=collection($comments)->toArray();
			// 返回数据给前端
			$data=[
				'count'=>$count,
				'list'=>$commentslist
			];
			if($commentslist){
				// 有评论
				$this->success('获取成功',null,$data);
				exit;
			}else{
				$this->error("暂无更多数据");
				exit;
			}
			// var_dump($count);
			exit;
		}
		return $this->view->fetch();
	}

	// 播放页
	public function play(){
		// echo "playing";
		if($this->request->isAjax()){
			$sid=$this->request->param('sid',0,'trim');//详情id
			$cid=$this->request->param('cid',0,'trim');//章节
			// 查询是否有课程
			$subject=$this->SubjectModel->find($sid);
			// var_dump($subject->toArray());
			if(!$subject){
				$this->error('暂无课程');
				exit;
			}
			// 是否登录
			$business=$this->islogin(false);
			if(!$business){
				$this->error('未登录，请先登录');
				exit;
			}
			// exit;
			// echo '登录了';
			
			// 判断是否购买过课程
			$buystate=false;
			// 查询购买表
			$where=['subid'=>$sid,'busid'=>$business['id']];
			$buystate=$this->OrderModel->where($where)->find();
			// exit;
			if(!$buystate){
				$this->error('您还未购买课程，请先购买课程');
				exit;
			}
			// 改变购买状态
			$buystate=true;
			$where=["id"=>$cid,'subid'=>$sid];
			// var_dump($where);
			// exit;
			$capter=$this->ChapterModel->where($where)->find();//当前详情的章节信息
			// var_dump($capter);
			// exit;
			if(!$capter){
				$this->success('无章节信息');
				exit;
			}else{
				$capter['buystate']=$buystate;
				$this->success('查询成功',null,$capter);
				exit;
			}
			// var_dump($capter->toArray());
		}
	}
	// 购买
	public function buy(){
		if($this->request->isAjax()){
			$sid=$this->request->param('sid');
			// 查询是否有课程
			$subject=$this->SubjectModel->find($sid);
			if(!$subject){
				$this->error('暂无课程');
				exit;
			}
			// 是否登录
			$business=$this->islogin(false);
			if(!$business){
				$this->error('未登录，请先登录');
				exit;
			}

			// 判断之前是否购买过该课程避免重复购买
			// 判断是否购买过课程
			$buystate=false;
			// 查询购买表
			$where=['subid'=>$sid,'busid'=>$business['id']];
			$buystate=$this->OrderModel->where($where)->find();
			// var_dump($buystate,'jklsajgodsjogijoi');
			// 如果是空则是没有购买过
			if($buystate){
				$this->error('您已购买，无需重复购买');
				exit;
			}
			// 查询用户余额
			$bmoney=$business['money'];
			// 获取课程的价格
			$subprice=$subject['price'];
			// 计算余额是否足够
			$updatamoney=bcsub($bmoney,$subprice);
			if($updatamoney<0){
				$this->error('余额不足，请充值后在试');
				exit;
			}
			
			// 更新用户表business，用户消费记录表business_record，交易表order
			$this->OrderModel->startTrans();
			$this->RecordModel->startTrans();
			$this->BusinessModel->startTrans();
			$orderdata=[
				'subid'=>$sid,
				'busid'=>$business['id'],
				'total'=>$subprice,
				'code'=>build_code('SU'),
			];
			$Orderstatus=$this->OrderModel->validate('common/Subject/Order')->save($orderdata);//交易表
			if($Orderstatus===false){
				$this->error($this->OrderModel->getError());
				exit;
			}
			// 用户
			$businessdata=[
				'id'=>$business['id'],
				'money'=>$updatamoney
			];
			$businessstate=$this->BusinessModel->validate('common/Business/Business.money')->isUpdate(true)->save($businessdata);
			if($businessstate===false){
				$this->OrderModel->rollback();
				$this->error($this->BusinessModel->getError());
				exit;
			}

			$content="购买了【{$subject['title']}】课程，一共花费了￥$subprice 元";
			// 消费记录表
			$recorddata=[
				'total'=>"-$subprice",
				'busid'=>$business['id'],
				'content'=>$content,
			];
			$recordstate=$this->RecordModel->validate('common/Business/Record')->save($recorddata);
			if($recordstate===false){
				$this->BusinessModel->rollback();
				$this->OrderModel->rollback();
				$this->error($this->RecordModel->getError());
				exit;
			}
			// 如果三个有一个失败那么全部操作取消，回滚到之前的状态，就当一切都没发生过
			if($recordstate===false || $businessstate===false || $Orderstatus===false){
				$this->RecordModel->rollback();
				$this->BusinessModel->rollback();
				$this->OrderModel->rollback();
				$this->error("课程购买失败");
			}else{
				// 成功后提交所有操作并返回消息给前端
				$this->OrderModel->commit();
				$this->BusinessModel->commit();
				$this->RecordModel->commit();
				$this->success('课程购买成功',url('home/index/buy'));
				exit;
			}
		}
		
	}

	// 联系我们
	public function contact(){
		$address =config('site.address');
		$phone =config('site.phone');
		$email =config('site.email');
		$this->assign(['address'=>$address,'phone'=>$phone,'email'=>$email]);
		return $this->view->fetch();
	}
}