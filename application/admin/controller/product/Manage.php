<?php

namespace app\admin\controller\product;

use app\common\controller\Backend;

class Manage extends Backend
{
    public function __construct()
    {  
        parent::__construct();
        $this->Model=model('Product.Product');
        $this->SortModel=model('Product.Sort');
        $this->UnitsModel=model('Product.Units');
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if($this->request->isAjax()){
            $this->request->filter(['strip_tags', 'trim']);
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            // 查询商品信息，需要链表查询分类表和单位表
            $data=$this->Model->with('prodsort,produnit')->where($where)->order($sort,$order)->limit($offset,$limit)->select();
            $count=$this->Model->count();
            $resu=['total'=>$count,'rows'=>$data];
            return json($resu);
        }
        return $this->view->fetch();
    }
    public function add(){
        if($this->request->isAjax()){
            $this->request->filter(['strip_tags', 'trim']);
            $row=$this->request->param('row/a');
            $state = $this->Model->save($row);
            if($state==false){
                $this->error($this->Model->getError());
            }else{
                $this->success();
                exit;
            }
        }
        $this->assign('label',build_select('row[label]',[0=>'热卖',1=>'新品',2=>'推荐',3=>'置顶'],[1],['class'=>'selectpicker','required'=>'']));
        $this->assign('state',build_select('row[state]',[0=>'下架',1=>'上架',2=>'补货中'],[1],['class'=>'selectpicker','required'=>'']));
        $this->assign('sort',build_select('row[sortid]',$this->SortModel->column('id,name'),[1],['class'=>'selectpicker','required'=>'']));
        $this->assign('units',build_select('row[unitsid]',$this->UnitsModel->column('id,unitname'),[1],['class'=>'selectpicker','required'=>'']));
        return $this->view->fetch();
    }
    public function sedit($ids=null){
        $data=$this->Model->find($ids);
        if($this->request->isAjax()){
            $this->request->filter(['strip_tags', 'trim']);
            $list=$this->request->param('row/a');
            $list['id']=$ids;
            $state=$this->Model->isUpdate(true)->save($list);
            if($state==false){
                $this->error($this->Model->getError());
            }else{
                $this->success();
                exit;
            }
        }
        $this->assign('data',$data);
        $this->assign('label',build_select('row[label]',[0=>'热卖',1=>'新品',2=>'推荐',3=>'置顶'],[$data['label']],['class'=>'selectpicker','required'=>'']));
        $this->assign('state',build_select('row[state]',[0=>'下架',1=>'上架',2=>'补货中'],[$data['state']],['class'=>'selectpicker','required'=>'']));
        $this->assign('sort',build_select('row[sortid]',$this->SortModel->column('id,name'),[$data['sortid']],['class'=>'selectpicker','required'=>'']));
        $this->assign('units',build_select('row[unitsid]',$this->UnitsModel->column('id,unitname'),[$data['unitsid']],['class'=>'selectpicker','required'=>'']));
        return $this->view->fetch();
    }
   

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
