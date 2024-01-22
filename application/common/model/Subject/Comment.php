<?php

namespace app\common\model\Subject;

use think\Model;

class Comment extends Model
{
    //查询评论列表
    protected $name="subject_comment";
    // 设置外键关联查询，把关联的用户信息查出来
    public function correlate(){
        return $this->belongsTo('app\common\model\Business\Business','busid','id',[],'LEFT')->setEagerlyType(0);
    }
}
