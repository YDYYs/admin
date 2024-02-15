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
          index_url: `product/manage/index`,
          add_url: "product/manage/add",
          edit_url: "product/manage/sedit",
          del_url: "product/manage/del",
          table: "product_m", //数据库表名
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
              title: "主键", //设置表头的名字
              operate: false,
            },
            {
              field: "name", //数据库字段
              title: "商品名称",
              sortable: false, //是否允许排序
              operate: "LIKE", //模糊搜索
            },
            {
              field: "price",
              title: "商品价格",
              operate: false,
            },
            {
              field: "repertorynum",
              title: "库存", //使用系统自带的语言包
              sortable: false, //是否允许排序
            },
            {
              field: "prodsort.name",
              title: "商品分类",
              formatter: Table.api.formatter.label,
            },
            {
              field: "produnit.unitname",
              title: "商品单位",
              formatter: Table.api.formatter.label,
            },
            {
              field: "state",
              title: "商品状态",
              searchList: { 0: "下架", 1: "上架", 2: "补货中" },
              formatter: Table.api.formatter.label,
            },
            {
              field: "label",
              title: "标签",
              searchList: { 0: "热卖", 1: "新品", 2: "推荐", 3: "置顶" },
              formatter: Table.api.formatter.label,
            },
            {
              field: "createtime",
              title: "上架时间", //使用公共语言包配置好的
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
