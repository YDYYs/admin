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
          index_url: `business/source/index`,
          edit_url:'business/source/edit',
          add_url:'business/source/add',
          del_url: "business/source/del",
          table: "business_source",
        },
      });
      var table = $("#sourcetable");
      // 发送请求
      table.bootstrapTable({
        url: $.fn.bootstrapTable.defaults.extend.index_url,
        pk: "id",
        sortName: "id",
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
              field: "name", //数据库字段
              title: '来源名称',
              sortable: false, //是否允许排序
              operate: "LIKE", //模糊搜索
            },
            {
              field: "operate",
              title: __("Operate"),
              table,
              events: Table.api.events.operate,
              formatter: Table.api.formatter.operate,
            },
          ],
        ],
      });
      Table.api.bindevent(table);
    },
    add:function(){
        Controller.api.bindevent();
    },
    edit:function(){
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
