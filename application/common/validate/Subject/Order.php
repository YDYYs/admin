<?php
namespace app\common\validate\Subject;

use think\Validate;

// 课程订单的验证器
class Order extends Validate
{
    protected $rule =   [
        'subid'  => 'require',
        'busid'   => 'require',
        'total' => ['require', 'number', '>=:0'], 
        'code' => ['require', 'unique:subject_order'], 
    ];

    protected $message  =   [
        'subid.require' => '课程必须填写',
        'busid.require'     => '用户必须填写',
        'total.require'   => '消费金额必须填写',
        'total.number'   => '消费金额必须是数字',
        'total.>=:0'   => '消费金额必须大于0元',
        'code.require'  => '订单号必须填写',   
        'code.unique'  => '订单号已重复',   
    ];
}