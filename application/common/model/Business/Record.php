<?php

namespace app\common\model\Business;

use think\Model;

class Record extends Model
{
    //交易记录表插入数据自动生成时间
    protected $name='business_record';
     // 开启自动写入
    protected $autoWriteTimestamp=true;
    // 设置字段的名字
    protected $createTime='createtime';
    protected $updateTime=false;//设置更新的时候不写入
    // 忽略数据表不存在的字段
    protected $field=true;
    protected $append=['createtime_text'];
    // 创建伪字段的获取器方法  时间
    public function getCreatetimeTextAttr($val,$data){
        $createtime=isset($data['createtime'])?$data['createtime']:'';
        return empty($createtime) ? '':date('Y-m-d',$createtime);
    }
}
