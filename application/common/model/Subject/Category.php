<?php

namespace app\common\model\Subject;

use think\Model;

class Category extends Model
{
    //课程表
    protected $name='subject_category';
    // 忽略数据表不存在的字段
    protected $field = true;
}
