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
        // 二级，需要拿id
        var ids = Fast.api.query("ids");
        Table.api.init({
          // 初始化表格参数
          extend: {
            index_url: `subject/chapter/index?ids=${ids}`,
            add_url: `subject/chapter/add?ids=${ids}`,
            edit_url: "subject/chapter/edit",
            del_url: "subject/chapter/del",
            table: "subject_chapter",
          },
        });
        var table = $("#table");
        // 发送请求
        table.bootstrapTable({
          url: $.fn.bootstrapTable.defaults.extend.index_url,
          pk: "id",
          sortName: "createtime",
          sortOrder:'asc',//排列方式，升序，默认为降序
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
                field: "title", //数据库字段
                title: __("ChapterTitle"), //自定义语言包定义的
                operate: "LIKE", //配置通用搜索的查询操作符，默认是'='精确搜索
              },
              {
                field: "createtime",
                title: __("Createtime"), //使用公共语言包配置好的
                operate: "RANGE",
                sortable: true,
                addclass: "datetimerange",
                formatter: Table.api.formatter.datetime,
              },
              // 操作按钮
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