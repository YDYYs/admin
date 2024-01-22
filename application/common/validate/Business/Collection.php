<?php

namespace app\common\validate\Business;

// 引入 tp验证器
use think\Validate;

class Collection extends Validate
{
    /**
     * 验证规则
    */
    protected $rule = [
        'busid' => ['require'],
        'proid' => ['require'],
        'cateid' => ['require']
    ];
    

    /**
     * 错误信息
    */
    protected $message = [
        'busid.require' => '未知用户id',
        'proid.require' => '未知商品id',
        'cateid.require' => '未知文章id',
    ];

    /**
     * 验证场景
    */
    protected $scene = [
        'product' => ['busid','proid'],
        'cate' => ['busid','cateid'],
    ];
}