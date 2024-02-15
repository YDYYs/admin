<?php

namespace app\common\model\Product;

use think\Model;
use traits\model\SoftDelete;

// 管理

class Product extends Model
{
    //
    protected $name='product_m';
    //继承软删除的功能
    use SoftDelete;
    //软删除的时间戳的字段
    protected $deleteTime = 'deletetime';
    // 自动写入一个时间字段,注册的时候需要使用

    // 开启自动写入
    protected $autoWriteTimestamp=true;

    // 设置字段的名字
    protected $createTime='createtime';
    protected $updateTime=false;//设置更新的时候不写入

    // 忽略数据表不存在的字段
    protected $field=true;

    // 链表查询分类表
    public function prodsort(){
        return $this->belongsTo('app\common\model\Product\Sort','sortid','id',[],'LEFT')->setEagerlyType(0);
    }
    public function produnit(){
        return $this->belongsTo('app\common\model\Product\Units','unitsid','id',[],'LEFT')->setEagerlyType(0);
    }
}
