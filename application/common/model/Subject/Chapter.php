<?php

namespace app\common\model\Subject;

use think\Model;

class Chapter extends Model
{
    //章节表
    protected $name='subject_chapter';
    // 忽略数据表不存在的字段
    protected $field = true;
}
