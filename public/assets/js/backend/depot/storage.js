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
          index_url: `depot/storage/index`,
          add_url: "depot/storage/add",
          edit_url: "depot/storage/sedit",
          del_url: "depot/storage/del",
          table: "depot_storage", //数据库表名
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
              field: "num", //数据库字段
              title: "编号",
              sortable: false, //是否允许排序
              operate: "LIKE", //模糊搜索
            },
            {
              field: "suppleid",
              title: "供应商",
              operate: false,
            },
            {
              field: "type",
              title: "类型",
              operate: false,
            },
            {
              field: "pricecount",
              title: "总价",
              operate: false,
            },
            {
              field: "createtime",
              title: "制单日期", //使用公共语言包配置好的
              operate: "RANGE",
              sortable: true,
              addclass: "datetimerange",
              formatter: Table.api.formatter.datetime,
            },
            {
              field: "state",
              title: "状态",
              operate: false,
              formatter: Table.api.formatter.status,
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
      Table.api.init({
        // 初始化表格参数
        extend: {
          index_url: `depot/storage/index`,
          add_url: "depot/storage/add",
          table: "depot_storage", //数据库表名
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
              field: "num", //数据库字段
              title: "编号",
              sortable: false, //是否允许排序
              operate: "LIKE", //模糊搜索
            },
            {
              field: "suppleid",
              title: "供应商",
              operate: false,
            },
            {
              field: "type",
              title: "类型",
              operate: false,
            },
            {
              field: "pricecount",
              title: "总价",
              operate: false,
            },
            {
              field: "createtime",
              title: "制单日期", //使用公共语言包配置好的
              operate: "RANGE",
              sortable: true,
              addclass: "datetimerange",
              formatter: Table.api.formatter.datetime,
            },
            {
              field: "state",
              title: "状态",
              operate: false,
              formatter: Table.api.formatter.status,
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
      //   Controller.api.bindevent(); //如果这个没写，上传也无法使用
    },
    sedit: function () {
      Controller.api.bindevent();
    },
    detele: function () {
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
