<?php

namespace app\common\model\Business;

use think\Model;
// 数据库连接操作
class Business extends Model
{
    //指明这个模型操作的表名,不需要添加表前缀
    protected $name = 'business';

    // 自动写入一个时间字段,注册的时候需要使用
    // 开启自动写入
    protected $autoWriteTimestamp=true;
    // 设置字段的名字
    protected $createTime='createtime';
    protected $updateTime=false;//设置更新的时候不写入
    // 忽略数据表不存在的字段
    protected $field=true;
}