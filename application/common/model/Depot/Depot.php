<?php

namespace app\common\model\Depot;

use think\Model;
use traits\model\SoftDelete;

class Depot extends Model
{
     //
    protected $name='depot';
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
