<?php

namespace app\common\model\Subject;

use think\Model;

class Chapter extends Model
{
    //章节表
    protected $name='subject_chapter';
      // 开启自动写入
    protected $autoWriteTimestamp=true;
    // 设置字段的名字，添加的时候自动写入时间戳
    protected $createTime='createtime';
    protected $updateTime=false;//设置更新的时候不写入
    // 忽略数据表不存在的字段
    protected $field = true;
}
