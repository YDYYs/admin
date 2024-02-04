<?php

namespace app\common\model\Admin;

use think\Model;

class Visit extends Model
{
    //分配记录表
    protected $name='business_visit';
    // 开启自动写入
    protected $autoWriteTimestamp=true;

    // 设置字段的名字
    protected $createTime='createtime';
    protected $updateTime=false;//设置更新的时候不写入

    // 忽略数据表不存在的字段
    protected $field=true;
    // 链表查询负责人
    public function admintab(){
        return $this->belongsTo('app\common\model\Admin\Admin','adminid','id',[],'LEFT')->setEagerlyType(0);
    }
}
