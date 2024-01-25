<?php

namespace app\common\model\Subject;

use think\Model;
// 引入软删除
use traits\model\SoftDelete;

class Order extends Model
{
    //继承软删除的功能
    use SoftDelete;

    //
    protected $name="subject_order";
     // 开启自动写入
    protected $autoWriteTimestamp=true;
    // 设置字段的名字
    protected $createTime='createtime';
    protected $updateTime=false;//设置更新的时候不写入
    // 忽略数据表不存在的字段
    protected $field=true;
    //软删除的时间戳的字段
    protected $deleteTime = 'deletetime';


    // 账单链表查询
    public function record(){
        return $this->belongsTo('app\common\model\Subject\Subject','subid','id',[],'LEFT')->setEagerlyType(0);
    }
}
