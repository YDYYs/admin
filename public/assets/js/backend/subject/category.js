define([
    'jquery',
    'bootstrap',
    'backend',
    'table',
    'form'
], function($, bootstrap,Backend,Table,Form) {
    var Controller = {
      // 课程列表
      index: function () {
        Table.api.init({
          // 初始化表格参数
          extend: {
            index_url: "subject/category/index",
            add_url: "subject/category/add",
            edit_url: "subject/category/edit",
            del_url: "subject/category/del",
            table: "subject_category",
          },
        });
        var table = $("#table");
        // 发送请求
        table.bootstrapTable({
          url: $.fn.bootstrapTable.defaults.extend.index_url,
          pk: "id",
          sortName: "weight",
          search: false, //配置关闭快速搜索input框
          //配置列
          columns: [
            [
              { checkbox: true }, //复选框
              {
                field: "id", //要显示的数据库字段
                title: __("ID"), //表头显示
                operate: false, //关闭通用搜索
              },
              {
                field: "name", //数据库字段
                title: __("CateName"), //自定义语言包定义的
                operate: "LIKE", //配置通用搜索的查询操作符，默认是'='精确搜索
              },
              {
                field: "weight",
                title: __("Weigh"), //使用公共语言包配置好的
                operate: false,
                sortable: true, //是否允许排序
              },
              {
                field: "operate",
                title: __("Operate"),
                table: table,
                events: Table.api.events.operate,
                formatter: Table.api.formatter.operate,
              },
            ],
          ],
        });
        Table.api.bindevent(table);
      },
      add: function () {
        Controller.api.bindevent();
      },
      edit: function () {
        Controller.api.bindevent();
      },
      del: function () {
        Controller.api.bindevent();
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