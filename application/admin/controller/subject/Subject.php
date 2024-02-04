<?php

namespace app\admin\controller\subject;


//继承后台框架公共控制器,会自动执行相同的js方法
use app\common\controller\Backend;

class Subject extends Backend
{
    public $model = null;
    protected $relationSearch=true;
    protected $searchFields = 'id,name';//配置快速搜索的字段
    public function __construct()
    {
        parent::__construct();
        $this->model=model('Subject.Subject');
        $this->CateModel=model('Subject.Category');
        $this->ChapterModel=model('Subject.Chapter');
    }
    // 查看页数据获取
    public function index()
    {  
        // 过滤请求过来的一些参数
        $this->request->filter(['strip_tags', 'trim']);

        // 获取所有参数
         if($this->request->isAjax()){
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            // 获取数量和列表
            $count=$this->model->with('category')->where($where)->order($sort,$order)->limit($offset,$limit)->count();
            $list=$this->model->with('category')->where($where)->order($sort,$order)->limit($offset,$limit)->select();
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
    public function add(){
        if($this->request->isPost()){
            // 拿到所有数据
            // 过滤请求的一些参数
            $this->request->filter(['strip_tags', 'trim']);
            // 获取所有参数
            $list=$this->request->param('row/a');
            // 直接把数据放到数据库中
            $state=$this->model->save($list);
            if($state===false){
                $this->error($this->model->getError());
                exit;
            }        
            $this->success();  
            exit;
        }
        // 生成下拉列表
        $this->assign('catelist',build_select('row[cateid]',$this->CateModel->column('id,name'),[],['class'=>'selectpicker','required'=>'']));
        // 拿到模型中定义的list数组
        $this->assign('flaglist',build_select('row[flag]',$this->model->flaglist(),[],['class'=>'selectpicker','required'=>'']));
        return $this->view->fetch();
    }
    // 编辑课程
    public function edit($ids=null){
        // 获取课程的信息
        $data=$this->model->find($ids);
        if($this->request->isPost()){
            // 过滤请求过来的一些参数
            $this->request->filter(['strip_tags', 'trim']);
            $row=$this->request->param('row/a');
            if($row){
                // 拿到之前的url地址
                $url=$data->toArray()['thumbs'];
                // 删除之前的文件.先查看图片是否被更改
                if(!empty($row['thumbs']) && $url!=$row['thumbs']){
                    // 改变了执行删除
                    is_file('.'.$url) && unlink('.'.$url);
                }
                // 前端提交表单没有id，需要添加一个
                $row['id']=$ids;
                if(empty($row['thumbs'])){
                    unset($row['thumbs']);
                }
                $state= $this->model->validate('common/Subject/Subject')->isUpdate(true)->save($row);
                if($state===false){
                    $this->error($this->model->getError());
                    exit;
                }
                $this->success();
            }
            exit;
        }
         // 生成下拉列表
        $this->assign('catelist',build_select('row[cateid]',$this->CateModel->column('id,name'),[$data['cateid']],['class'=>'selectpicker','required'=>'']));
        // 拿到模型中定义的list数组
        $this->assign('flaglist',build_select('row[flag]',$this->model->flaglist(),[$data['flag']],['class'=>'selectpicker','required'=>'']));
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
        if($state===false){
            $this->error();
            exit;
        }
        $this->success();
        exit;
        // 模型配置了软删除
    }
    // 彻底删除
    public function destroy($ids=null){
        echo $ids;
        // 查询id中的所有视频
        $where['subid']=array('in',$ids);//对同一字段等于多个id查询
        $vidourls=$this->ChapterModel->where($where)->column('url');//查询到所有符号条件的URL地址变成一个数组
        $state=$this->model->destroy($ids,true);
        if($state===false){
            $this->error($this->model->getError());
            exit;
        }
        // 删除本地的所有章节视频
        foreach ($vidourls as $key => $item) {
            // 判断这个是否存在
            $isfile=is_file('.'.$item);
            if($isfile){
                // 删除本地文件
                @unlink('.'.$item);
            }
        }
        $this->success();
        exit;
    }

    // 详情
    public function info($ids=null){
        return $this->view->fetch();
    }
    public function order($ids=null){
        // 传入的是课程的id
        $this->model=model('Subject.Order');
         //过滤掉请求中的一些参数结构
        $this->request->filter(['strip_tags', 'trim']);

        if($this->request->isAjax()){
            //从后台的控制器基类中获取到以下参数
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $DataWhere = ['subid' => $ids];
            //查询总数
            $total = $this->model
                ->with(['record'])
                ->where($where)
                ->where($DataWhere)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->count();
            //查询数据
            $list = $this->model
                ->with(['record'])
                ->where($where)
                ->where($DataWhere)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            // 返回值
            $list=collection($list)->toArray();

            $result = ['total'=>$total, 'rows'=>$list];

            //返回json
            return json($result);
        }
       
    }
    // 评论列表
    public function comment($ids=null){
        $this->model=model('Subject.Comment');
         //过滤掉请求中的一些参数结构
        $this->request->filter(['strip_tags', 'trim']);
        if($this->request->isAjax()){
            //从后台的控制器基类中获取到以下参数
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            // var_dump(collection($$where)->toArray());
            $DataWhere = ['subid' => $ids];
            // exit;
            $count =$this->model->where($where)->where($DataWhere)->order($sort,$order)->limit($offset,$limit)->count();
            $list=$this->model->with('correlate')->where($where)->where($DataWhere)->order($sort,$order)->limit($offset,$limit)->select();
            $list=collection($list)->toArray();
            
            // var_dump($list);
            $result = ['total'=>$count, 'rows'=>$list];
            return json($result);
        }
        exit;
    }
    // 删除评论
    public function commentdel($ids=null){
        $this->model=model('Subject.Comment');
        $state=$this->model->destroy($ids);
        if($state ===false){
            $this->error($this->model->getError());
            exit;
        }
        $this->success();
        exit;
    }

}
