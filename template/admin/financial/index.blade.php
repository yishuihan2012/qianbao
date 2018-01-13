 @extends('admin/layout/layout_main')
 @section('title','财务管理-对账中心~')
 @section('wrapper')
 <style>
	 h4 > a,.pull-right > a{color:#145ccd;}
      .clearTime{ position: absolute; right: 5px; top: 5px; z-index: 99; border: 1px solid; color: red; font-size: .6rem; padding: 0 5px;}
 </style>
 <link rel="stylesheet" href="/static/lib/tabs/zui.tabs.min.css">

 <div class="panel">
      <div class="panel-body">
      <form action="" method="post">
           <div class="input-group" style="width: 200px;float: left; margin-right: 10px;">
                <input type="text" class="form-control date-picker" id="dateTimeRange" placeholder="注册时间查询" />
                <input type="hidden" name="beginTime" id="beginTime" value="" />
                <input type="hidden" name="endTime" id="endTime" value="" />
                <z class='clearTime'>X</z>
           </div>
           <button class="btn btn-primary" type="submit">搜索</button>
      </form>
    </div>
 </div>

 <blockquote> 对账中心</blockquote>
 <section>
 <hr/>
 <div id="tabs" class="tabs">
  <nav class="tabs-navbar"></nav>
  <nav class="tabs-container"></nav>
 </div>
 </section>
 <script src="/static/lib/tabs/zui.tabs.js"></script> 
 <script type="text/javascript">
      // 定义标签页
      var tabs = [{
                title: '会员升级收益',
                url: "{{url('index/financial/level')}}",
                type: 'iframe',
                forbidClose: true
           }, {
                title: '快捷支付收益',
                url: "{{url('index/financial/income')}}",
                type: 'iframe',
                forbidClose: true
           }, {
                title: '自动代还收益',
                url: "{{url('index/financial/substitute')}}",
                type: 'iframe',
                forbidClose: true
           }, {
                title: '提现统计',
                url: "{{url('index/financial/withdraw')}}",
                type: 'iframe',
                forbidClose: true
           }];
      // 初始化标签页管理器
      $('#tabs').tabs({tabs: tabs});
 </script>
 <script type="text/javascript">
 $(document).ready(function(){
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.financial_center').addClass('active');
    	 $('.menu .nav li.financial-manager').addClass('show'); 
 });
 //时间日期
 $('#dateTimeRange').daterangepicker({
      applyClass : 'btn-sm btn-success',
      cancelClass : 'btn-sm btn-default',
      locale: {
           applyLabel: '确认',
           cancelLabel: '取消',
           fromLabel : '起始时间',
           toLabel : '结束时间',
           customRangeLabel : '自定义',
           firstDay : 1
      },
      ranges : {
            //'最近1小时': [moment().subtract('hours',1), moment()],
           '今日': [moment().startOf('day'), moment()],
           '昨日': [moment().subtract('days', 1).startOf('day'), moment().subtract('days', 1).endOf('day')],
           '最近7日': [moment().subtract('days', 6), moment()],
           '最近30日': [moment().subtract('days', 29), moment()],
           '本月': [moment().startOf("month"),moment().endOf("month")],
           '上个月': [moment().subtract(1,"month").startOf("month"),moment().subtract(1,"month").endOf("month")]
      },
      opens : 'right',    // 日期选择框的弹出位置
      separator : ' 至 ',
      showWeekNumbers : true,     // 是否显示第几周
      format: 'YYYY-MM-DD'
    }, function(start, end, label) { // 格式化日期显示框
      $('#beginTime').val(start.format('YYYY-MM-DD'));
      $('#endTime').val(end.format('YYYY-MM-DD'));
 });
 begin_end_time_clear();
 $('.clearTime').click(begin_end_time_clear);
 //清除时间
 function begin_end_time_clear() {
      $('#dateTimeRange').val('');
      $('#beginTime').val('');
      $('#endTime').val('');
 }
 </script>
 @endsection
