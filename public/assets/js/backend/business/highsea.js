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
          index_url: `business/highsea/index`,
          del_url: "business/highsea/del",
          get_url: `business/highsea/get`,
          assign_url: "business/highsea/allot", //这个是弹窗页面的URL地址
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
              field: "admintab.nickname", //数据库字段
              title: "领取人",
              sortable: false, //是否允许排序
              operate: "LIKE", //模糊搜索
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
                  name: "get",
                  title: "领取",
                  icon: "fa fa-plus",
                  classname: "btn btn-xs btn-success btn-magic btn-ajax",
                  confirm: "是否领取该用户",
                  extend:
                    "data-toggle='tooltip' data-area= '[\"100%\", \"100%\"]'", //重点是这一句
                  url: $.fn.bootstrapTable.defaults.extend.get_url, //发请求的地址
                  success: function (data, ret) {
                    //成功后刷新表格
                    table.bootstrapTable("refresh"); //如果表名写错会没有提示词，也无法刷新
                  },
                },
                {
                  name: "assign",
                  title: "分配",
                  icon: "fa fa-mail-forward",
                  classname: "btn btn-xs btn-primary btn-dialog", //弹出新窗口
                  extend:
                    "data-toggle='tooltip' data-area= '[\"90%\", \"90%\"]'", //重点是这一句
                  url: $.fn.bootstrapTable.defaults.extend.assign_url, //发请求的地址
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
      // 监听分配点击事件，调出弹窗，监听class名为.btn-allot的元素
      $(document).on("click", ".btn-allot", function (event) {
        let ids = Table.api.selectedids(table); //会得到一个id的数组
        var url =
          $.fn.bootstrapTable.defaults.extend.assign_url + `?ids=${ids}`;
        if (!url) return false;
        var msg = $(this).attr("title");
        var width = $(this).attr("data-width");
        var height = $(this).attr("data-height");
        var area = [
          $(window).width() > 800 ? (width ? width : "800px") : "95%",
          $(window).height() > 600 ? (height ? height : "600px") : "95%",
        ];
        var options = {
          shadeClose: false,
          shade: [0.3, "#393D49"],
          area: area,
          callback: function (value) {
            CallBackFun(value.id, value.name); //在回调函数里可以调用你的业务代码实现前端的各种逻辑和效果
          },
        };
        Fast.api.open(url, msg, options);
      });
      // 给领取按钮添加点击事件
      $(document).on("click", ".btn-get", function () {
        let ids = Table.api.selectedids(table); //会得到一个id的数组
        var url =
          $.fn.bootstrapTable.defaults.extend.get_url + `?ids=${ids}`;
        // 发起请求
        Backend.api.ajax({
          url,
        },()=>{
          // 发请求后的回调函数
          TableSubject.bootstrapTable("refresh");
        });
      });
      Table.api.bindevent(table);
    },
    allot:function(){
      // 分配表单提交后到这了
      Form.api.bindevent(
        $("form[role=form]"),
        function (data, ret) {
          //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
          Fast.api.close(data); //这里是重点,成功后会关闭弹窗
          Toastr.success("成功"); //这个可有可无
        },
        function (data, ret) {
          Toastr.success("失败");
        }
      );
      Controller.api.bindevent();//这是一个表单提交页面，确定按钮必须写这个才不会被禁用
    },
    recyclebin:function(){
      console.log('sagj')
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
