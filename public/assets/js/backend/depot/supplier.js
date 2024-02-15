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
          index_url: `depot/supplier/index`,
          add_url: "depot/supplier/add",
          edit_url: "depot/supplier/sedit",
          del_url: "depot/supplier/del",
          table: "depot", //数据库表名
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
              field: "name", //数据库字段
              title: "供应商名称",
              sortable: false, //是否允许排序
              operate: "LIKE", //模糊搜索
            },
            {
              field: "mobile",
              title: "供应商手机号",
              operate: "LIKE",
            },
            {
              field: "createtime",
              title: "添加时间", //使用公共语言包配置好的
              operate: "RANGE",
              sortable: true,
              addclass: "datetimerange",
              formatter: Table.api.formatter.datetime,
            },
            {
              field: "province",
              title: "省份",
              operate: "LIKE",
            },
            {
              field: "city",
              title: "城市",
              operate: "LIKE",
            },
            {
              field: "district",
              title: "区域",
              operate: "LIKE",
            },
            {
              field: "address",
              title: "详细地址",
              operate: "LIKE",
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
      Controller.api.bindevent(); //如果这个没写，上传也无法使用
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
