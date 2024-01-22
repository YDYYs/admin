<?php

namespace app\common\model\Subject;

use think\Model;

// 首页查询课程
class Subject extends Model
{
    //表
    protected $name='subject';

    // 设置关联查询
    public function category(){
        return $this->belongsTo('app\common\model\Subject\Category',"cateid",'id',[],'LEFT')->setEagerlyType(0);
    }
    // 处理查询到的数据,时间转换,内容去除标签,使用获取器的伪字段选项
    protected $append=[
        'createtime_text',
        'content_text',
        'likes_text'
    ];

    // 创建伪字段的获取器方法  时间
    public function getCreatetimeTextAttr($val,$data){
        $createtime=isset($data['createtime'])?$data['createtime']:'';
        return empty($createtime) ? '':date('Y-m-d',$createtime);
    }

    // 去标签
    public function getContentTextAttr($val,$data){
        $content=isset($data['content'])?trim($data['content']):'';
        return strip_tags($content);
    }
    // 字符串转化数组
    public function getLikesTextAttr($val,$data){
        $likes=isset($data['likes'])?trim($data['likes']):'';
        return explode(',',$likes);
    }
}
