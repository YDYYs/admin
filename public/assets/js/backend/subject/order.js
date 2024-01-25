define([
    'jquery',
    'bootstrap',
    'backend',
    'table',
    'form'
], function($, bootstrap,Backend,Table,Form) {
    var Controller = {
      // 列表
      index: function () {
        Table.api.init({
          // 初始化表格参数
          extend: {
            index_url: `subject/order/index`,
            table: "subject_order",
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
              {
                field: "id", //数据库的字段名称
                title: __("ID"), //设置表头的名字
                operate: false,
              },
              {
                field: "code", //数据库字段
                title: __("Ordernum"),
                sortable: false, //是否允许排序
                operate: "=", //模糊搜索
              },
              {
                field: "record.title",
                title: "课程名称",
                operate: false,
              },
              {
                field: "createtime",
                title: __("Createtime"), //使用系统自带的语言包
                sortable: true, //是否允许排序
                operate: "RANGE",
                addclass: "datetimerange",
                formatter: Table.api.formatter.datetime,
              },
            ],
          ],
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