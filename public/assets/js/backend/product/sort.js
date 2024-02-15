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
          index_url: `product/sort/index`,
          add_url: "product/sort/add",
          edit_url: "product/sort/sedit",
          del_url: "product/sort/detele",
          table: "product_sort1", //数据库表名
        },
      });
      var table = $("#table");
      // 发送请求
      table.bootstrapTable({
        url: $.fn.bootstrapTable.defaults.extend.index_url,
        pk: "id",
        sortName: "weight",
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
              title: __("Sortname"),
              sortable: false, //是否允许排序
              operate: "LIKE", //模糊搜索
            },
            {
              field: "imageurl",
              title: "分类图片",
              operate: false,
              formatter: Table.api.formatter.image, //使用图片预览格式显示
            },
            {
              field: "weight",
              title: __("sortweight"), //使用系统自带的语言包
              sortable: true, //是否允许排序
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
