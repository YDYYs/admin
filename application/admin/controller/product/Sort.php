<?php

namespace app\admin\controller\product;

use app\common\controller\Backend;

class Sort extends Backend
{
    public function __construct()
    {
        parent::__construct();
        $this->model=model('Product.Sort');
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if($this->request->isAjax()){
            // 过滤请求参数
            $this->request->filter(['strip_tags', 'trim']);
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            // 获取
            $data=$this->model->where($where)->order($sort,$order)->limit($offset,$limit)->select();
            $count=$this->model->count();
            $resu=['total'=>$count,'rows'=>$data];
            return json($resu);
        }
        return $this->view->fetch();
        exit;
        //
    }
    public function add(){
        if($this->request->isAjax()){
            $this->request->filter(['strip_tags','trim']);
            $req=$this->request->param('row/a');
            $state=$this->model->where(['weight'=>$req['weight']])->find();
            if($state){
                $this->error('权重值不可用，请重新输入');//这里只做提示，并不会关闭窗口所以还是要渲染视图，不能exit
            }else{
                if(empty($req['imageurl'])){
                    unset($req['imageurl']);
                }
                $state=$this->model->save($req);
                if($state==false){
                    $this->error($this->model->getError());
                    exit;
                }
                $this->success('添加成功');
                exit;
            }
           
        }
        return $this->view->fetch();
    }
    public function sedit($ids){
        $data=$this->model->find($ids)->toArray();
        if($this->request->isAjax()){
            $this->request->filter(['strip_tags','trim']);
            $req=$this->request->param('row/a');
            
            if(empty($req['imageurl'])){
                unset($req['imageurl']);
            }else if($req['imageurl']!==$data['imageurl']){
                // 删除本地之前的图片
                $imgurl='.'.$data['imageurl'];
                // 文件是否存在,在就删除
                if(file_exists($imgurl)){
                    unlink($imgurl);
                }
            }
            $req['id']=$ids;
            $state=$this->model->isUpdate(true)->save($req);
            if($state==false){
                $this->error($this->model->getError());
                exit;
            }
            $this->success('更新成功');
            exit;
            
        }
        //通过id查数据返回给前端
        $this->assign('data',$data);
        return $this->view->fetch();
    }


    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function detele($ids)
    {
        // 先拿到这个图片
        $imageurl=$this->model->find($ids)['imageurl'];
        if(!empty($imageurl)){
            // 删除图片
            $imageurl='.'.$imageurl;
            if(file_exists($imageurl)){
                unlink($imageurl);
            }
        }
        $state= $this->model->destroy($ids);
        if($state==false){
            $this->error($this->model->getError());
            exit;
        }
        $this->success('删除成功');
        exit;
    }
}
