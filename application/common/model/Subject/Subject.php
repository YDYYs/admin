<?php

namespace app\common\model\Subject;

use think\Model;

// 引入软删除
use traits\model\SoftDelete;

// 首页查询课程
class Subject extends Model
{
    
    //继承软删除的功能
    use SoftDelete;
    //表
    protected $name='subject';
      // 开启自动写入
    protected $autoWriteTimestamp=true;
    // 设置字段的名字，添加的时候自动写入时间戳
    protected $createTime='createtime';
    protected $updateTime=false;//设置更新的时候不写入
    
    // 设置软删除的时间戳添加的字段
    protected $deleteTime = 'deletetime';


    // 设置关联查询
    public function category(){
        return $this->belongsTo('app\common\model\Subject\Category',"cateid",'id',[],'LEFT')->setEagerlyType(0);
    }
    // 处理查询到的数据,时间转换,内容去除标签,使用获取器的伪字段选项
    protected $append=[
        'createtime_text',
        'content_text',
        'likes_text',
        'likes_count'
    ];

    // 创建伪字段的获取器方法  时间
    public function getCreatetimeTextAttr($val,$data){
        $createtime=isset($data['createtime'])?$data['createtime']:'';
        return empty($createtime) ? '':date('Y-m-d',$createtime);
    }

    // 去标签,第一个参数是数据库字段对应的值，第二个是每一条的数据
    public function getContentTextAttr($val,$data){
        $content=isset($data['content'])?trim($data['content']):'';
        return strip_tags($content);
    }
    // 字符串转化数组
    public function getLikesTextAttr($val,$data){
        $likes=isset($data['likes'])?trim($data['likes']):'';
        $list=array_filter(explode(',',$likes));
        return $list;
    }
    // 计算点赞数量
    public function getLikesCountAttr($val,$data){
        $likes=isset($data['likes'])?trim($data['likes']):'';
        // 去空
        $list=array_filter(explode(',',$likes));
        return count($list);
    }

    public function flaglist(){
        return ['hot'=>'热门','top'=>'置顶','news'=>'最新'];
    }
}
