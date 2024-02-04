define(["jquery", "bootstrap", "backend", "table", "form"], function (
  $,
  bootstrap,
  Backend,
  Table,
  Form
) {
  var Controller = {
    // 回收列表
    index: function () {
      Table.api.init({
        // 初始化表格参数
        extend: {
          index_url: `business/highsea/recyclebin`,
          del_url: "business/highsea/del",
          recover_url: "business/highsea/restore",
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
              title: "客户名称",
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
              title: "客户来源",
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
              field: "deletetime",
              title: "删除时间", //使用系统自带的语言包
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
                  name: "recover",
                  title: "恢复",
                  icon: "fa fa-repeat",
                  classname: "btn btn-xs btn-success btn-magic btn-ajax",
                  confirm: "是否回复",
                  extend:
                    "data-toggle='tooltip' data-area= '[\"100%\", \"100%\"]'", //重点是这一句
                  url: $.fn.bootstrapTable.defaults.extend.recover_url, //发请求的地址
                  success: function (data, ret) {
                    //成功后刷新表格
                    table.bootstrapTable("refresh"); //如果表名写错会没有提示词，也无法刷新
                  },
                },
              ],
            },
          ],
        ],
      });
      $(".btn-restore").on("click", function () {
        // 拿到勾选的行的id值,使用自带的Table的api拿到TableSubject的
        let ids = Table.api.selectedids(table);
        // 确认弹出框
        layer.confirm(
          "是否确认恢复数据",
          {
            title: "恢复提醒",
            btn: ["是", "否"],
          },
          function (index) {
            // 使用backend的api发请求
            Backend.api.ajax(
              {
                url:
                  $.fn.bootstrapTable.defaults.extend.recover_url +
                  `?ids=${ids}`,
              },
              () => {
                layer.close(index);
                table.bootstrapTable("refresh");//刷新
              }
            );
          }
        );
      });
      Table.api.bindevent(table);
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
