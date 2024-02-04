<?php

namespace app\common\model\Admin;

use think\Model;

class Receive extends Model
{
    //分配记录表
    protected $name='business_receive';
    // 链表查询申请人
    public function admintab(){
        return $this->belongsTo('app\common\model\Admin\Admin','applyid','id',[],'LEFT')->setEagerlyType(0);
    }
}
