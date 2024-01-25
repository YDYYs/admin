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
            index_url: "subject/subject/index",
            add_url: "subject/subject/add",
            edit_url: "subject/subject/edit",
            del_url: "subject/subject/del",
            chapter_url:'subject/chapter/index',//设置自定义按钮
            info_url:'subject/subject/info',//设置自定义按钮课程详情
            table: "subject",
          },
        });
        var table = $("#table");
        // 发送请求
        table.bootstrapTable({
          url: $.fn.bootstrapTable.defaults.extend.index_url,
          pk: "id",
          sortName: "createtime",
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
                title: __("SubjectTitle"), //自定义语言包定义的
                operate: "LIKE", //配置通用搜索的查询操作符，默认是'='精确搜索
              },
              {
                field: "category.name", //数据库字段
                title: __("SubjectCate"), //自定义语言包定义的
                operate: "LIKE",
              },

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
              // 操作按钮
              {
                field: "operate",
                title: __("Operate"),
                table: table,
                events: Table.api.events.operate,
                formatter: Table.api.formatter.operate,
                // 配置自定义按钮
                buttons: [
                  {
                    name: "info",
                    title: function (row) {
                      return `${row.title}详情`; //拿到的是后端返回的数据的title
                    },
                    icon: "fa fa-file-text", //配置图标
                    classname: "btn btn-xs btn-primary btn-dialog",
                    extend:
                      "data-toggle='tooltip' data-area= '[\"90%\", \"90%\"]'", //第一个参数(toggle)设置的是鼠标移到标签上显示的文字的位置如果没有设置，则会显示在下方，并且有延时，第二个参数(area)设置弹出的窗口大小
                    url: $.fn.bootstrapTable.defaults.extend.info_url, //使用上面配置好的地址作为参数
                  },
                  {
                    name: "chapter",
                    title: function (row) {
                      return `${row.title}-章节列表`;
                    },
                    icon: "fa fa-bars",
                    classname: "btn btn-xs btn-primary btn-dialog",
                    extend:
                      "data-toggle='tooltip' data-area= '[\"90%\", \"90%\"]'", //重点是这一句
                    url: $.fn.bootstrapTable.defaults.extend.chapter_url,
                  },
                ],
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
      info:function(){
        // 设置选项卡切换事件
        $("a[data-toggle='tab']").on("shown.bs.tab",function(){
         let panel=$($(this).attr('href'))//拿到切换的li中a标签的链接参数
          // console.log(panel)
          // 如果拿到了就进入
          if(panel.length>0){
            // 调用自带的方法，并把id值传过去
            console.log(panel.attr("id"));
            Controller.table[panel.attr("id")].call(this);
            // 切换后重新刷新一次,也就是点击一次左上角的刷新按钮
            $(this).on('click',function(){
                $($(this).attr("href"))
                          .find(".btn-refresh")
                          .trigger("click");
            });
          }
          // 关闭绑定的点击事件，避免被多次注册事假，增加发送请求的次数
          $(this).unbind("shown.bs.tab");
        });
        // 设置默认的效果
          $('ul.nav-tabs li.active a[data-toggle="tab"]').trigger(
            "shown.bs.tab"
          );
      },
      // 定义两个方法用于设置两个表的数据请求与渲染
      table:{
        order:function (){
          // 拿到当前课程的id值，
          var ids = Fast.api.query("ids");
          //初始化Table表格插件参数
          Table.api.init({
            extend: {
              index_url: `subject/subject/order?ids=${ids}`, //列表页面请求的地址
              table: "subject_order", //表名
            },
          });
          var TableOrder = $("#TableOrder");//这个必须要能在页面中找到，找不到就不会执行下面的操作
          // 初始化表格
          // $.ajax({})
          TableOrder.bootstrapTable({
            url: $.fn.bootstrapTable.defaults.extend.index_url, //发起请求地址，得到的是Table.api.init中的index_url的地址
            pk: "id", //主键
            sortName: "createtime", //排序字段
            toolbar: "#ToolbarOrder",
            //设置表格的表头部分
            columns: [
              [
                {
                  field: "id", //数据库的字段名称
                  title: __("ID"), //设置表头的名字
                  operate: false,
                },
                {
                  field: "code", //数据库字段
                  title: __("OrderCode"),
                  operate: "LIKE", //模糊搜索
                },
                {
                  field: "total", //数据库字段
                  title: __("OrderPrice"),
                  sortable: true, //是否允许排序
                  operate: false, //模糊搜索
                },
                {
                  field: "record.title", //数据库字段
                  title: "课程名称",
                  operate: "LIKE", //模糊搜索
                },
                {
                  field: "createtime",
                  title: __("OrderCreatetime"),
                  sortable: true, //是否允许排序
                  operate: "RANGE",
                  addclass: "datetimerange",
                  formatter: Table.api.formatter.datetime,
                },
              ],
            ],
          });
          // 为表格绑定事件
          Table.api.bindevent(TableOrder);
        },
        comment:function(){
          // 拿到当前课程的id值，
          var ids = Fast.api.query("ids");
          //初始化Table表格插件参数
          Table.api.init({
            extend: {
              index_url: `subject/subject/comment?ids=${ids}`, //列表页面请求的地址
              del_url: `subject/subject/commentdel?ids=${ids}`,
              table: "subject_comment", //表名
            },
          });
          // 这个id不能写错，如果写的是工具栏的id则最大宽度只有左边工具栏的大小，必须写table的id
          var TableComment = $("#TableComment"); //这个必须要能在页面中找到，找不到就不会执行下面的操作
          // 初始化表格
          // $.ajax({})
          TableComment.bootstrapTable({
            url: $.fn.bootstrapTable.defaults.extend.index_url, //发起请求地址，得到的是Table.api.init中的index_url的地址
            pk: "id", //主键
            sortName: "createtime", //排序字段
            toolbar: "#ToolbarComment",
            //设置表格的表头部分
            columns: [
              [
                { checkbox: true }, //复选框
                {
                  field: "id", //数据库的字段名称
                  title: __("ID"), //设置表头的名字
                  operate: false,
                },
                {
                  field: "content", //数据库字段
                  title: __("CommentContent"),
                  sortable: true, //是否允许排序
                  operate: false, //模糊搜索
                },
                {
                  field: "correlate.nickname",
                  title: "用户昵称",
                  operate: "LIKE",
                },
                {
                  field: "createtime",
                  title: __("Createtime"), //使用系统自带的语言包
                  sortable: true, //是否允许排序
                  operate: "RANGE",
                  addclass: "datetimerange",
                  formatter: Table.api.formatter.datetime,
                },
                // 操作按钮删除
                {
                  field: "operate",
                  title: __("Operate"),
                  table: TableComment,
                  events: Table.api.events.operate,
                  formatter: Table.api.formatter.operate,
                },
              ],
            ],
          });
          // 为表格绑定事件
          Table.api.bindevent(TableComment);
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