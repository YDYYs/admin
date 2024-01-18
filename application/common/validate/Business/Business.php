<?php 
namespace app\common\validate\Business;

use think\Validate;

class Business extends  Validate{
    // 验证规则,单词rule
    protected $rule =[
        'mobile' => ['require', 'number', 'unique:business', 'regex:/(^1[3|4|5|7|8][0-9]{9}$)/'],
        "password"=>['require'],
        "email"=>['email'],//使用内置的验证规则
        "salt"=>['require'],
        "auth"=>['number','in:0,1'],
        "nickname"=>['require'],
        'money'=>['number','>=:0'],
        'deal'=>['number','in:0,1'],
    ];

    // 触发验证规则后的提醒信息,message
    protected $message=[
        'mobile.require' => '手机号码必填',
        'mobile.number' => '手机号码必须是数字',
        'mobile.unique' => '手机号码必须是唯一的',
        'mobile.regex' => '手机号码格式不正确',
        'money.number'      => '余额必须是数字类型',
        'money.>='      => '余额必须大于等于0元',
        'auth.number'      => '认证状态的类型有误',
        'auth.in'      => '认证状态的值有误',
        'deal.number'      => '成交状态的类型有误',
        'deal.in'      => '成交状态的值有误',
        'nickname.require' => '昵称必填',
        'email.require' => '邮箱必填',
        'email.email' => '邮箱格式错误',
        'password.require'  => '密码必填',
        'salt.require'      => '密码盐必填',
    ];
    // 自定义场景
    protected $scene=[
        'profile'=>['mobile','auth','nickname','email'],
    ];
}

?>