<?php

namespace app\common\model\Product;

use think\Model;
use traits\model\SoftDelete;

// 订单

class Order extends Model
{
    //
    protected $name='product_order';
    // 使用软删除功能
    use SoftDelete;
    protected $deleteTime='deletetime';
    // 开启自动写入
    protected $autoWriteTimestamp=true;

    // 设置字段的名字
    protected $createTime='createtime';
    protected $updateTime=false;//设置更新的时候不写入

    // 忽略数据表不存在的字段
    protected $field=true;
}
