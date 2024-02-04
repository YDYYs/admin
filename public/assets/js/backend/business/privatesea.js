define(["jquery", "bootstrap", "backend", "table", "form"], function (
  $,
  bootstrap,
  Backend,
  Table,
  Form
) {
  var Controller = {
    // 列表
    index: function () {
      Table.api.init({
        // 初始化表格参数
        extend: {
          index_url: `business/privatesea/index`,
          del_url: "business/privatesea/del",
          edit_url: "business/privatesea/edit",
          detail_url: "business/privatesea/detail",
          recycle_url: "business/privatesea/recycle",
          table: "business",
        },
      });
      var table = $("#table");
      // 发送请求
      table.bootstrapTable({
        url: $.fn.bootstrapTable.defaults.extend.index_url,
        pk: "id",
        sortName: "createtime",
        sortOrder: "asc", //排列方式，升序，默认为降序
        search: false, //配置关闭快速搜索input框
        //配置列
        columns: [
          [
            { checkbox: true },
            {
              field: "id", //数据库的字段名称
              title: __("ID"), //设置表头的名字
              operate: false,
            },
            {
              field: "nickname", //数据库字段
              title: __("Bussinessname"),
              sortable: false, //是否允许排序
              operate: "LIKE", //模糊搜索
            },
            {
              field: "gender",
              title: "性别",
              searchList: { 0: "保密", 1: "男", 2: "女" },
              operate: false,
              formatter: Table.api.formatter.label,
            },
            {
              field: "mobile", //数据库字段
              title: "手机号",
              sortable: false, //是否允许排序
              operate: "=", //模糊搜索
              visible: false, //隐藏不显示
            },
            {
              field: "sourcetab.name", //数据库字段
              title: __("Bussinesssoure"),
              sortable: false, //是否允许排序
              operate: "LIKE", //模糊搜索
            },
            {
              field: "deal", //数据库字段
              title: __("State"),
              sortable: false, //是否允许排序
              operate: "FIND_IN_SET", //搜索方式
              searchList: { 0: "未成交", 1: "成交" },
              custom: { 0: "info", 1: "success" },
              formatter: Table.api.formatter.label,
            },
            {
              field: "auth", //数据库字段
              title: __("Authstate"),
              sortable: false, //是否允许排序
              operate: "FIND_IN_SET", //模糊搜索
              searchList: { 0: "未认证", 1: "已认证" },
              custom: { 0: "danger", 1: "success" },
              formatter: Table.api.formatter.label,
            },
            {
              field: "createtime",
              title: __("Createtime"), //使用系统自带的语言包
              sortable: true, //是否允许排序
              operate: "RANGE",
              addclass: "datetimerange",
              formatter: Table.api.formatter.datetime,
            },
            {
              field: "operate",
              title: __("Operate"),
              table,
              events: Table.api.events.operate,
              formatter: Table.api.formatter.operate,
              buttons: [
                {
                  name: "detail",
                  title: "详情",
                  icon: "fa fa-drivers-license-o",
                  classname: "btn btn-xs btn-primary btn-dialog", //弹出新窗口
                  extend:
                    "data-toggle='tooltip' data-area= '[\"90%\", \"90%\"]'", //重点是这一句
                  url: $.fn.bootstrapTable.defaults.extend.detail_url, //发请求的地址
                },
                {
                  name: "recycle",
                  title: "回收",
                  icon: "fa fa-recycle",
                  classname: "btn btn-xs btn-success btn-magic btn-ajax",
                  confirm: "是否将该用户返回公海",
                  extend:
                    "data-toggle='tooltip' data-area= '[\"90%\", \"90%\"]'", //重点是这一句
                  url: $.fn.bootstrapTable.defaults.extend.recycle_url, //发请求的地址
                  success: function (data, ret) {
                    //成功后刷新表格
                    table.bootstrapTable("refresh");
                  },
                },
              ],
            },
          ],
        ],
      });
      Table.api.bindevent(table);
    },
    detail: function () {
      $("a[data-toggle='tab']").on("shown.bs.tab", function () {
        var aid = $(this).attr("href");
        aid=aid.slice(1);
        var panel = $($(this).attr("href")); //首先拿到切换的li中a标签的链接参数，再去查找下面div中的id值为这个的，
        // 如果li中的a标签和下面div的不一致则获取到的长度就为0，将无法进行下一步操作
        
        // console.log($($(this).attr('href')));
        // if (panel.length > 0) {
        if (aid) {
          // 调用自带的方法，并把id值传过去
          // Controller.table[panel.attr("id")].call(this);
          Controller.table[aid].call(this);
          // 切换后重新刷新一次,也就是点击一次左上角的刷新按钮
          $(this).on("click", function () {
            $($(this).attr("href")).find(".btn-refresh").trigger("click");
          });
        }
        // 关闭绑定的点击事件，避免被多次注册事假，增加发送请求的次数
        $(this).unbind("shown.bs.tab");
      });
      // 设置默认的效果
      $('ul.nav-tabs li.active a[data-toggle="tab"]').trigger("shown.bs.tab");
    },
    interviewadd:function(){
      Controller.api.bindevent();
    },
    interviewedit:function (){
      Controller.api.bindevent();
    },
    // 在table对象写每个选项的方法
    table: {
      userdetail: function () {
        // 拿到当前课程的id值，
        var ids = Fast.api.query("ids");
        //初始化Table表格插件参数
        Table.api.init({
          extend: {
            index_url: `business/privatesea/userdetail?ids=${ids}`, //列表页面请求的地址
            table: "business", //表名
          },
        });
        var Tabledetail = $("#Tabledetail"); //这个必须要能在页面中找到，找不到就不会执行下面的操作
        // 初始化表格
        // $.ajax({})
        Tabledetail.bootstrapTable({
          url: $.fn.bootstrapTable.defaults.extend.index_url, //发起请求地址，得到的是Table.api.init中的index_url的地址
          pk: "id", //主键
          sortName: "createtime", //排序字段
          toolbar: "#Toolbardetail",
          //设置表格的表头部分
          columns: [
            [
              {
                field: "nickname", //数据库字段
                title: "客户名称",
                operate: false, //不查询
              },
              {
                field: "mobile", //数据库字段
                title: "手机号",
                sortable: false, //是否允许排序
                operate: false,
              },
              {
                field: "email", //数据库字段
                title: "邮箱",
                operate: false,
              },
              {
                field: "money", //数据库字段
                title: "余额",
                operate: false,
              },
              {
                field: "sourcetab.name", //数据库字段
                title: "客户来源",
                operate: false,
              },
              {
                field: "deal", //数据库字段
                title: "成交状态",
                searchList: { 0: "未成交", 1: "已成交" }, //书写了这个就需要把格式配置
                formatter: Table.api.formatter.label,
                operate: false,
              },
              {
                field: "auth", //数据库字段
                title: "邮箱验证",
                searchList: { 0: "未认证", 1: "已认证" },
                formatter: Table.api.formatter.label,
                operate: false,
              },
              {
                field: "admintab.username", //数据库字段
                title: "申请人",
                operate: false,
              },
              {
                field: "createtime",
                title: __("OrderCreatetime"),
                sortable: true, //是否允许排序
                operate: "RANGE",
                addclass: "datetimerange",
                formatter: Table.api.formatter.datetime,
              },
            ],
          ],
        });
        // 为表格绑定事件
        Table.api.bindevent(Tabledetail);
      },
      record: function () {
        // 拿到当前课程的id值，
        var ids = Fast.api.query("ids");
        //初始化Table表格插件参数
        Table.api.init({
          extend: {
            index_url: `business/privatesea/recode?ids=${ids}`, //列表页面请求的地址
            table: "business_receive", //表名
          },
        });
        var TableRecord = $("#TableRecord"); //这个必须要能在页面中找到，找不到就不会执行下面的操作
        // 初始化表格
        // $.ajax({})
        TableRecord.bootstrapTable({
          url: $.fn.bootstrapTable.defaults.extend.index_url, //发起请求地址，得到的是Table.api.init中的index_url的地址
          pk: "id", //主键
          sortName: "applytime", //排序字段
          toolbar: "#ToolbarRecord",
          //设置表格的表头部分
          columns: [
            [
              {
                field: "admintab.username", //数据库字段
                title: "申请人",
                operate: "LIKE", //模糊搜索
              },
              {
                field: "status", //数据库字段
                title: "状态",
                custom: {
                  recovery: "danger",
                  allot: "success",
                  apply: "info",
                  reject: "red",
                },
                searchList: {
                  recovery: "回收",
                  apply: "申请",
                  allot: "分配",
                  reject: "拒绝",
                },
                formatter: Table.api.formatter.status,
                operate: "LIKE", //模糊搜索
              },
              {
                field: "applytime",
                title: "申请时间",
                sortable: true, //是否允许排序
                operate: "RANGE",
                addclass: "datetimerange",
                formatter: Table.api.formatter.datetime,
              },
            ],
          ],
        });
        // 为表格绑定事件
        Table.api.bindevent(TableRecord);
      },
      interview: function () {
        // 拿到当前课程的id值，
        var ids = Fast.api.query("ids");
        //初始化Table表格插件参数
        Table.api.init({
          extend: {
            index_url: `business/privatesea/interview?ids=${ids}`, //列表页面请求的地址
            add_url: `business/privatesea/interviewadd?ids=${ids}`, //回访添加
            del_url: `business/privatesea/del?ids=${ids}`, //回访添加
            edit_url: `business/privatesea/interviewedit`, //回访添加
            table: "business_visit", //表名
          },
        });
        var TableInterview = $("#TableInterview"); //这个必须要能在页面中找到，找不到就不会执行下面的操作
        // 初始化表格
        // $.ajax({})
        TableInterview.bootstrapTable({
          url: $.fn.bootstrapTable.defaults.extend.index_url, //发起请求地址，得到的是Table.api.init中的index_url的地址
          pk: "id", //主键
          sortName: "createtime", //排序字段
          toolbar: "#ToolbarInterview",
          //设置表格的表头部分
          columns: [
            [
              {
                checkbox:true
              },
              {
                field: "id", //数据库的字段名称
                title: __("ID"), //设置表头的名字
                operate: false,
              },
              {
                field: "admintab.nickname", //数据库字段
                title: "负责人",
                operate: "LIKE", //模糊搜索
              },

              {
                field: "content", //数据库字段
                title: "回访内容",
                operate: "LIKE", //模糊搜索
              },
              {
                field: "createtime",
                title: "回访时间",
                sortable: true, //是否允许排序
                operate: "RANGE",
                addclass: "datetimerange",
                formatter: Table.api.formatter.datetime,
              },
            ],
          ],
        });
        // 为表格绑定事件
        Table.api.bindevent(TableInterview);
      },
      consume: function () {
        // 拿到当前课程的id值，
        var ids = Fast.api.query("ids");
        //初始化Table表格插件参数
        Table.api.init({
          extend: {
            index_url: `business/privatesea/consume?ids=${ids}`, //列表页面请求的地址
            table: "business_recode", //表名
          },
        });
        var TableConsume = $("#TableConsume"); //这个必须要能在页面中找到，找不到就不会执行下面的操作
        // 初始化表格
        // $.ajax({})
        TableConsume.bootstrapTable({
          url: $.fn.bootstrapTable.defaults.extend.index_url, //发起请求地址，得到的是Table.api.init中的index_url的地址
          pk: "id", //主键
          sortName: "createtime", //排序字段
          toolbar: "#ToolbarConsume",
          //设置表格的表头部分
          columns: [
            [
              {
                field: "createtime",
                title: "消费时间",
                sortable: true, //是否允许排序
                operate: "RANGE",
                addclass: "datetimerange",
                formatter: Table.api.formatter.datetime,
              },
              {
                field: "total", //数据库字段
                title: "消费金额",
                operate: "LIKE", //模糊搜索
              },
             
              {
                field: "content", //数据库字段
                title: "描述",
                operate: "LIKE", //模糊搜索
              },
            ],
          ],
        });
        // 为表格绑定事件
        Table.api.bindevent(TableConsume);
      },
      subject: function () {
        // 拿到当前课程的id值，
        var ids = Fast.api.query("ids");
        //初始化Table表格插件参数
        Table.api.init({
          extend: {
            index_url: `business/privatesea/subject?ids=${ids}`, //列表页面请求的地址
            table: "subject_order", //表名
          },
        });
        var Tabledetail = $("#TableSubject"); //这个必须要能在页面中找到，找不到就不会执行下面的操作
        // 初始化表格
        // $.ajax({})
        Tabledetail.bootstrapTable({
          url: $.fn.bootstrapTable.defaults.extend.index_url, //发起请求地址，得到的是Table.api.init中的index_url的地址
          pk: "id", //主键
          sortName: "createtime", //排序字段
          toolbar: "#ToolbarSubject",
          //设置表格的表头部分
          columns: [
            [
              {
                field: "id", //数据库的字段名称
                title: __("ID"), //设置表头的名字
                operate: false,
              },
              {
                field: "code", //数据库字段
                title: __("OrderCode"),
                operate: "LIKE", //模糊搜索
              },
              {
                field: "total", //数据库字段
                title: __("OrderPrice"),
                sortable: true, //是否允许排序
                operate: false, //模糊搜索
              },
              {
                field: "record.title", //数据库字段
                title: "课程名称",
                operate: "LIKE", //模糊搜索
              },
              {
                field: "createtime",
                title: __("OrderCreatetime"),
                sortable: true, //是否允许排序
                operate: "RANGE",
                addclass: "datetimerange",
                formatter: Table.api.formatter.datetime,
              },
            ],
          ],
        });
        // 为表格绑定事件
        Table.api.bindevent(Tabledetail);
      },
    },
    // 自带的操作表单弹框和提交事件
    api: {
      bindevent: function () {
        Form.api.bindevent($("form[role=form]"));
      },
    },
  };
  return Controller;
});
