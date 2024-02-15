<?php

namespace app\admin\controller\depot;

use app\common\controller\Backend;

class Storage extends Backend
{
    public function __construct()
    {
        parent::__construct();
        $this->Model=model('Depot.Storage');
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
            $data=$this->Model->where($where)->order($sort,$order)->limit($offset,$limit)->select();
            $count=$this->Model->count();
            $resu=['total'=>$count,'rows'=>$data];
            return json($resu);
        }
        return $this->view->fetch();
    }
    public function add(){
        if($this->request->isPost())
        {
            $params = $this->request->post();
            
            if($params)
            {
                $ModelProduct = model('Product.Product');

                //入库插入
                $ModelStorage = model('Depot.Storage');
                $ModelStorageProduct = model('Depot.Storage.Product');
                // 开启事务
                $ModelStorage->startTrans();
                $ModelStorageProduct->startTrans();

                //循环入库商品
                $prolist = isset($params['prolist']) ? $params['prolist'] : [];
                $nums = isset($params['nums']) ? $params['nums'] : [];
                $price = isset($params['price']) ? $params['price'] : [];
                $total = isset($params['total']) ? $params['total'] : [];

                if(count($prolist) <= 0)
                {
                    $this->error(__('未选择入库商品，请重新选择'));
                    exit;
                }

                if(count($nums) <= 0)
                {
                    $this->error(__('未填写入库商品数量，请重新填写'));
                    exit;
                }

                if(count($price) <= 0)
                {
                    $this->error(__('未填写入库商品单价，请重新填写'));
                    exit;
                }

                if(count($total) <= 0)
                {
                    $this->error(__('未填写入库商品总价，请重新填写'));
                    exit;
                }

                //商品与数量的个数不相等
                if(count($prolist) != count($nums))
                {
                    $this->error(__('入库商品的个数与数量不相等，请重新填写'));
                    exit;
                }

                //商品与价格的个数不相等
                if(count($prolist) != count($price))
                {
                    $this->error(__('入库商品的个数与商品价格个数不相等，请重新填写'));
                    exit;
                }

                if(count($prolist) != count($total))
                {
                    $this->error(__('入库商品的个数与商品总价个数不相等，请重新填写'));
                    exit;
                }

                //查找供应商
                $supplierid = isset($params['supplierid']) ? trim($params['supplierid']) : 0;
                $suppliername = model('Depot.Supplier')->where(['id' => $supplierid])->value('name');

                $params['code'] = build_code("ST");
                $params['status'] = 0;
                $params['adminid'] = $this->auth->id;

                //插入入库记录
                $StorageStatus = $ModelStorage->validate("Common/Depot/Storage/Storage")->save($params);

                if($StorageStatus === FALSE)
                {
                    $this->error($ModelStorage->getError());
                    exit;
                }

                //插入入库商品
                $StorageProduct = [];
                foreach($prolist as $key=>$item)
                {
                    $StorageProduct[] = [
                        "storageid"=>$ModelStorage->id,
                        "proid"=>$item,
                        "nums"=>$nums[$key] > 0 ? $nums[$key] : 1,
                        "price"=>$price[$key] >= 0 ? $price[$key] : 0,
                        "total"=>$total[$key] >= 0 ? $total[$key] : 0,
                    ];
                }

                //插入入库商品关系表
                $StorageProductStatus = $ModelStorageProduct->validate("Common/Depot/Storage/Product")->insertAll($StorageProduct);

                if($StorageProductStatus === FALSE)
                {
                    $ModelStorage->rollBack();
                    $this->error($ModelStorageProduct->getError());
                    exit;
                }

                //提交事务
                if($StorageStatus === FALSE || $StorageProductStatus === FALSE)
                {
                    $ModelStorage->rollBack();
                    $ModelStorageProduct->rollBack();
                    $this->error(__('添加入库失败'));
                    exit;
                }else
                {
                    $ModelStorage->commit();
                    $ModelStorageProduct->commit();
                    $this->success();
                    exit;
                }
            }
        }
        $this->view->assign('typelist', build_select('type', model('Depot.Storage.Storage')->typelist(),[], ['class' => 'form-control selectpicker']));
      return $this->view->fetch();
    }
}
