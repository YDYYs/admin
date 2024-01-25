<?php

namespace app\admin\controller\subject;


//继承后台框架公共控制器,会自动执行相同的js方法
use app\common\controller\Backend;

class Chapter extends Backend
{
    public $model = null;
    protected $searchFields = 'id,name';//配置快速搜索的字段
    public function __construct()
    {
        parent::__construct();
        $this->model=model('Subject.Chapter');
        $this->CateModel=model('Subject.Subject');
    }
    // 查看页数据获取
    public function index($ids=null)
    {  
        // 过滤请求过来的一些参数
        $this->request->filter(['strip_tags', 'trim']);

        // 获取所有参数
         if($this->request->isAjax()){
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            // 拿到id
            $datawhere=['subid'=>$ids];
            // 获取数量和列表
            $count=$this->model->where($where)->where($datawhere)->order($sort,$order)->limit($offset,$limit)->count();
            $list=$this->model->where($where)->where($datawhere)->order($sort,$order)->limit($offset,$limit)->select();
            $list=collection($list)->toArray();
            // var_dump($list);
            // exit;
            // 拿到参数返回,key必须是这两个
            $resu=['total'=>$count,'rows'=>$list];
            return json($resu);
        }
       return $this->view->fetch();
    }
    // 添加课程
    public function add($ids=null){
        // 在js中向地址上放了参数id，这里接收到的ids是课程id，并不是章节id
        // 获取课程的信息id
        $data=$this->CateModel->find($ids);
        if(!$data){$this->error('课程不存在');}
        // ids是课程的id，向某个章节添加课程
        if($this->request->isPost()){
            // 拿到所有数据
            // 过滤请求的一些参数
            $this->request->filter(['strip_tags', 'trim']);
            // 获取所有参数
            $list=$this->request->param('row/a');
            $list['subid']=$ids;
            // 直接把数据放到数据库中
            $state=$this->model->save($list);
            if($state===false){
                $this->error($this->model->getError());
                exit;
            }        
            $this->success();  
            exit;
        }
        return $this->view->fetch();
    }
    // 编辑章节
    public function edit($ids=null){
        // 这里在js中并没有穿id过来，使用的是系统自带的传参，也就是条数据的主键id值
        // 获取课程章节的id
        $data=$this->model->find($ids);
        if($this->request->isPost()){
            // 过滤请求过来的一些参数
            $this->request->filter(['strip_tags', 'trim']);
            $row=$this->request->param('row/a');
            if($row){
                // 拿到之前的url地址
                $url=$data->toArray()['url'];
                // 删除之前的文件.先查看图片是否被更改
                if(!empty($row['url']) && $url!=$row['url']){
                    // 改变了执行删除加上@错误抑制符
                    is_file('.'.$url) && @unlink('.'.$url);
                }
                // 前端提交表单没有id，需要添加一个
                $row['id']=$ids;
                if(empty($row['url'])){
                    unset($row['url']);
                }
                $state= $this->model->isUpdate(true)->save($row);
                if($state===false){
                    $this->error($this->model->getError());
                    exit;
                }
                $this->success();
            }
            exit;
        }
        $this->assign('data',$data);
        return $this->view->fetch();
    }
    // 删除
    public function del($ids=null){
        // 获取id的信息
        $data=$this->model->find($ids);
        // 判断是否存在
        if(!$data){
            $this->error(__('No results were found'));
            exit;
        }
        // destroy可删除多个也可删除单个
        $state=$this->model->destroy($ids);
        // 如果有视频要把视频也删除了
        $vurl=$data['url'];
        if(!empty($vurl)){
            // 判断地址是否存在,如果在就删除
            is_file('.'.$vurl) && @unlink('.'.$vurl);
        }
        if($state===false){
            $this->error();
            exit;
        }
        $this->success();
        exit;
    }
}
