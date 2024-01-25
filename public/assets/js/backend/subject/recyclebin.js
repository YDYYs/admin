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
      // 设置选项卡切换事件
      $("a[data-toggle='tab']").on("shown.bs.tab", function () {
        let panel = $($(this).attr("href")); //拿到切换的li中a标签的链接参数
        console.log(panel);
        // 如果拿到了就进入
        if (panel.length > 0) {
          // 调用自带的方法，并把id值传过去
          console.log(panel.attr("id"));
          Controller.table[panel.attr("id")].call(this);
          // 切换后重新刷新一次,也就是点击一次左上角的刷新按钮
          $(this).on("click", function () {
            $($(this).attr("href")).find(".btn-refresh").trigger("click");
          });
        }
        // 关闭绑定的点击事件，避免被多次注册事假，增加发送请求的次数
        $(this).unbind("shown.bs.tab");
      });
      // 设置默认的效果
      $('ul.nav-tabs li.active a[data-toggle="tab"]').trigger("shown.bs.tab");
    },
    table: {
      // 课程回收站
      subject: function () {
        //初始化Table表格插件参数
        Table.api.init({
          extend: {
            index_url: `subject/subject/recyclebin`, //列表页面请求的地址
            del_url: "subject/subject/destroy", //彻底删除
            restore_url: "subject/subject/restore",
            table: "subject", //表名
          },
        });
        var TableSubject = $("#TableSubject"); //这个必须要能在页面中找到，找不到就不会执行下面的操作
        // 初始化表格
        // $.ajax({})
        TableSubject.bootstrapTable({
          url: $.fn.bootstrapTable.defaults.extend.index_url, //发起请求地址，得到的是Table.api.init中的index_url的地址
          pk: "id", //主键
          sortName: "createtime", //排序字段
          toolbar: "#ToolbarSubject",
          //设置表格的表头部分
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
                title: __("SubjectTitle"), //自定义语言包定义的
                operate: "LIKE", //配置通用搜索的查询操作符，默认是'='精确搜索
              },
            //   {
            //     field: "category.name", //数据库字段
            //     title: __("SubjectCate"), //自定义语言包定义的
            //     operate: "LIKE",
            //   },

              {
                field: "content_text",
                title: __("SubjectContent"), //使用公共语言包配置好的
                operate: false,
                // 内容太长，设置最大宽度，溢出的部分隐藏，设置style样式
                cellStyle: {
                  css: {
                    "max-width": "300px",
                    "white-space": "nowrap",
                    overflow: "hidden",
                    "text-overflow": "ellipsis",
                  },
                },
              },
              {
                field: "thumbs",
                title: __("SubjectThumbs"), //使用公共语言包配置好的
                operate: false,
                formatter: Table.api.formatter.image,
              },
              {
                field: "likes_count", //通过模型创建的伪字段，数据库无该字段，不可排序
                title: __("SubjectLikes"), //使用公共语言包配置好的
                operate: false,
              },
              {
                field: "price",
                title: __("SubjectPrice"), //使用公共语言包配置好的
                operate: false,
              },
              {
                field: "flag",
                title: __("SubjectFlag"), //使用公共语言包配置好的
                // 配置显示的中文字
                searchList: { hot: "热门", news: "最新", top: "置顶" },
                // operate: false,
                // 枚举类搜索
                operate: "FIND_IN_SET",
                formatter: Table.api.formatter.flag,
              },
              {
                field: "createtime",
                title: __("SubjectTime"), //使用公共语言包配置好的
                operate: "RANGE",
                sortable: true,
                addclass: "datetimerange",
                formatter: Table.api.formatter.datetime,
              },
              {
                field: "deletetime",
                title: __("Deletetime"), //使用公共语言包配置好的
                operate: "RANGE",
                sortable: true,
                addclass: "datetimerange",
                formatter: Table.api.formatter.datetime,
              },
              // 操作按钮
              {
                field: "operate",
                title: __("Operate"),
                table: TableSubject,
                events: Table.api.events.operate,
                formatter: Table.api.formatter.operate,
                // 配置自定义按钮
                buttons: [
                  {
                    name: "restore",
                    title: "恢复",
                    icon: "fa fa-circle-o-notch",
                    classname: "btn btn-xs btn-success btn-magic btn-ajax",
                    confirm: "是否确认恢复数据",
                    extend:
                      "data-toggle='tooltip' data-area= '[\"100%\", \"100%\"]'", //重点是这一句
                    url: $.fn.bootstrapTable.defaults.extend.restore_url,//发请求的地址
                    success: function (data, ret) {
                      //成功后刷新表格
                      TableSubject.bootstrapTable("refresh");
                    },
                  },
                ],
              },
            ],
          ],
        });
        // 绑定顶上恢复按钮的点击事件
        $(".btn-restore").on('click',function(){
            // 拿到勾选的行的id值,使用自带的Table的api拿到TableSubject的
            let ids=Table.api.selectedids(TableSubject);
            // 确认弹出框
            layer.confirm(
                '是否确认恢复数据',
                {
                    title:'恢复提醒',btn:['是','否']
                },
                function(index){
                    // 使用backend的api发请求
                    Backend.api.ajax({
                      url:
                        $.fn.bootstrapTable.defaults.extend.restore_url +
                        `?ids=${ids}`,
                    },()=>{
                        layer.close(index)
                        TableSubject.bootstrapTable('refresh')
                    });
                }
            )
        })
        // 为表格绑定事件
        Table.api.bindevent(TableSubject);
      },
      order:function(){
        // console.log('order')
        Table.api.init({
          // 初始化表格参数
          extend: {
            index_url: `subject/order/recyclebin`,
            del_url: "subject/order/destroy", //彻底删除
            restore_url: "subject/order/restore",
            table: "subject_order",
          },
        });
        var TableOrder = $("#TableOrder");
        // 发送请求
        TableOrder.bootstrapTable({
          url: $.fn.bootstrapTable.defaults.extend.index_url,
          pk: "id",
          sortName: "createtime",
          toolbar: "#ToolbarOrder",
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
                field: "code", //数据库字段
                title: __("OrderCode"),
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
              {
                field: "deletetime",
                title: __("Deletetime"), //使用公共语言包配置好的
                operate: "RANGE",
                sortable: true,
                addclass: "datetimerange",
                formatter: Table.api.formatter.datetime,
              },
              // 操作按钮
              {
                field: "operate",
                title: __("Operate"),
                table: TableSubject,
                events: Table.api.events.operate,
                formatter: Table.api.formatter.operate,
                // 配置自定义按钮
                buttons: [
                  {
                    name: "restore",
                    title: "恢复",
                    icon: "fa fa-circle-o-notch",
                    classname: "btn btn-xs btn-success btn-magic btn-ajax",
                    confirm: "是否确认恢复数据",
                    extend:
                      "data-toggle='tooltip' data-area= '[\"100%\", \"100%\"]'", //重点是这一句
                    url: $.fn.bootstrapTable.defaults.extend.restore_url, //发请求的地址
                    success: function (data, ret) {
                      //成功后刷新表格
                      TableOrder.bootstrapTable("refresh");
                    },
                  },
                ],
              },
            ],
          ],
        });

        // 绑定顶上恢复按钮的点击事件
        $(".btn-restore").on("click", function () {
          // 拿到勾选的行的id值,使用自带的Table的api拿到TableSubject的
          let ids = Table.api.selectedids(TableOrder);
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
                    $.fn.bootstrapTable.defaults.extend.restore_url +
                    `?ids=${ids}`,
                },
                () => {
                  layer.close(index);
                  TableOrder.bootstrapTable("refresh");
                }
              );
            }
          );
        });

        Table.api.bindevent(TableOrder);
      },
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
