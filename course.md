## 下载框架源码

 https://www.fastadmin.net/download.html

  下载完整的源代码

## 配置站点

配置站点：www.fast.com

指向到：public

访问站点：www.fast.com

### 框架结构

Thinkphp **MVC架构**的框架

- C：Controller 控制器：是用来写业务逻辑的
- M:  model 模型：专门用于数据库操作(增删查改)
- V：view 视图：显示页面的

## 初始化

第一次安装需要配置数据库，先新建一个数据库

数据名名称：fast，设置编码：utf8mb4_general_ci

## 框架的目录结构

- addons  安装的插件目录
- extend  框架的拓展目录
- runtime 放程序编译文件、缓存文件、日志文件
- thinkphp  框架的源代码目录
- vendor  框架的依赖库文件夹 放一个功能依赖包(邮箱发送/文件上传/验证码)
- bower.json bower工具 依赖配置文件 - 前端依赖包工具 
- bower工具下载jquery/citypicker/swiper
- composer.json composer工具 用来下载后端工具
- think 命令行工具(创建控制器、创建模块、创建模型)
- LICENSE 许可证书
- README 说明文件
- public  公共目录(放image/css/js/uploads) 放静态资源 + 程序单入口文件
-  index.php -> 项目的入口程序的文件
- application 模块的应用程序目录，我们绝大多数主要操作的目录就在这个里面，每一个模块 都是一个 MVC的小架构 控制器、模型、视图
-   admin  -> 后台模块
-   common -> 公共模块
-   api   -> 接口模块
-   index -> 参考模块
-   common.php === helpers.php 辅助函数
-   config.php  -> 系统配置文件
-   database.php -> 数据库配置文件

## 第一个前台模块

创建一个前台的模块(MVC) home模块

- php think build --module 模块名
- php think build --module home

## 访问新建的模块

www.**站点域名**.com/**index.php/(入口文件)**/模块/控制器文件/方法/参数

访问home模块 www.fast.com/index.php/home/index/index（没做任何配置）

## 配置默认的模块

在application/config.php配置文件中修改

'default_module'     => '**home**',  修改默认访问的模块名，这就是默认的

'default_controller'   => '**Index**', 配置默认的模块名

'default_action'     => '**index**', 默认操作名

## 地址栏参数解析

http://www.fast.com/index.php(入口文件)/home**模块**/Index**控制器**/index**操作方法**

`http://www.fast.com/home/index/index`

输入地址参数访问后，第一步会进入到根目录中的application文件夹中寻找**home**文件夹，在home文件夹中找到**controller**控制器的文件夹里面的**index.php**文件，在这个文件中定义的类中，找到方法为**index**的方法并执行该方法

## 将地址栏的默认入口文件隐藏

在public/.htaccess文件中设置

```php+HTML
<IfModule mod_rewrite.c>
    Options +FollowSymlinks -Multiviews
    RewriteEngine on
  
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php?/$1 [QSA,PT,L]
</IfModule>
```

开启错误调试：application/config.php

### 路由跳转

使用 url('模块/控制器/方法', [参数名=>'参数值', '参数名'=>'参数值'])

**带消息提示的页面跳转** 在文件上面使用 **use \think\Controller**  并且将当前类继承他，在类中的构造器中引入父类 （**\think\Controller**）中的构造方法  `parent::__construct();//继承父类方法`

继承完后，类中的所有方法都可以使用父类的构造方法

- **success**  成功的页面跳转
- **error**  失败的消息跳转
- 跳转的参数  `$this->success('提示的消息'，跳转的页面(使用url的方法跳转))`

### 模型的创建

模型 - 命令行创建模型  `php think make:model home/Business` (需要再跟目录中打开控制台操作)，这个所创建的模型是放在application/common/model/Business/Business.php，这个是操作数据库的模型，模型的内容如下所示：

```php+HTML
<?php

namespace app\common\model\Business;

use think\Model;

class Source extends Model
{
    //指定操作的表名,不需要加标前缀，默认有配置
    protected $name='business_source';
    //还可以配置一个忽略不存在的字段，可以在写入或修改的时候避免报错
    protected $field = true;
    //表关联查询
    belongsTo('关联模型名','外键名','关联表主键名',['模型别名定义'],'join类型');
    // 设置关联查询
    public function category(){
        return $this->belongsTo('app\common\model\Subject\Category',"cateid",'id',[],'LEFT')->setEagerlyType(0);
    }
    //调用表关联查询使用，在控制器中的类方法中使用，前提是需要在构造器中定义对应的公共模型
    $list=$this->SubjectModel->with(['category'])->where($where)->limit($start,$limit)->select();

}
```

然后再application/home中的控制器controller里的文件可以使用模型来操作数据库

在application/home/controller/business.php，文件中的类的构造器中可以设置公共的模型，

`$this->BusinessModel=model(Business.Business)`，只后在这个类中写其他方法的时候就可以直接使用构造器中的公共模型，例如：

```php
public function index(){
    //向数据库中添加一条数据
    $arr=['name'=>'小明'，'age'=>'20'];
    $res=$this->BussinessModel->(这里可以使用在验证器validate)->save(需要插入的字段一维数组);
    //这里会返回一个新增的id,有id则添加成功
}
```

公共模型中的验证器**validate**，这个是验证器，验证器需要手动添加文件夹和文件，位于application/common文件夹下，添加一个文件夹**validate**，再添加一个模型名称的文件夹，以及文件，最终的路径为validate/Business/Business.php在这个文件中添加验证器规则和消息提示，如下所示：

```php
<?php 
    //设置命名空间
    namespace app\common\validate\Business;

    use think\Validate;

    class Business extends  Validate{
        // 验证规则,单词rule,写错后会无法失效，无法使用
        protected $rule =[
            'mobile' => ['require', 'number', 'unique:business', 		'regex:/(^1[3|4|5|7|8][0-9]{9}$)/'],
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
        // 自定义场景，在这里可以定义特殊的使用场景，添加某个场景下使用哪些规则
        protected $scene=[
            'profile'=>['mobile','auth','nickname','email'],
        ];
    }
?>
```

写完规则后在使用数据库模型添加或修改的时候就可以，使用方法如下

```php
$res = $this->BusinessModel->validate('common/Business/Business.profile')->isUpdate(true)->save($params);
//在validate中填写从根目录中common开始到最后的场景名，isupdate是告诉他是否更新，更新的时候使用，save传入参数，一维数组
```

