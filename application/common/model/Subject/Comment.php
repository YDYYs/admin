<?php

namespace app\common\model\Subject;

use think\Model;

class Comment extends Model
{
    // 这个数据库表没有deletetime字段，不能设置软删除，如果设置了就会报错
    //查询评论列表
    protected $name="subject_comment";
    // 设置外键关联查询，把关联的用户信息查出来
     // 开启自动写入
    protected $autoWriteTimestamp=true;
    // 设置字段的名字
    protected $createTime='createtime';
    protected $updateTime=false;//设置更新的时候不写入
    // 忽略数据表不存在的字段
    protected $field=true;

    // 链表查询用户表
    public function correlate(){
        return $this->belongsTo('app\common\model\Business\Business','busid','id',[],'LEFT')->setEagerlyType(0);
    }
}
