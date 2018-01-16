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
      <form action="{{url('index/Financial/index')}}" method="post">
           <div class="col-sm-2">
                <div class="input-control has-icon-left">
                     <input id="inputAccountExample1" type="text" class="form-control" name="member_nick" placeholder="用户姓名或者手机号" value="{{$conditions['member_nick'] or ''}}">
                     <label for="inputAccountExample1" class="input-control-icon-left"><i class="icon icon-user "></i></label>
                </div>
           </div>
           <div class="col-sm-2">
                <div class="input-group">
                     <span class="input-group-btn"><button class="btn btn-default" type="button">金额</button></span>
                     <input type="text" class="form-control" name="min_money" value="{{$conditions['min_money'] or ''}}">
                     <span class="input-group-btn fix-border"><button class="btn btn-default" type="button">~</button></span>
                     <input type="text" class="form-control" name="max_money" value="{{$conditions['max_money'] or ''}}">
                </div>
           </div>

           <div class="col-sm-2">
                <div class="input-group">
                     <input type="text" class="form-control date-picker" id="dateTimeRange" placeholder="收益时间查询" value="" />
                     <input type="hidden" name="beginTime" id="beginTime" value="" />
                     <input type="hidden" name="endTime" id="endTime" value="" />
                     <z class='clearTime'>X</z>
                </div>
           </div>
           <div class="col-sm-1">
                <button class="btn btn-primary" type="submit">搜索</button>
           </div>
      </form>
    </div>
 </div>

 <blockquote> 对账中心: 会员升级收益 <strong class="text-danger">{{$data['level']}}</strong> 元, 快捷支付收益 <strong class="text-danger">{{$data['quickPay']}}</strong> 元, 代还收益 <strong class="text-danger">{{$data['autoPay']}}</strong> 元, 共提现成功 <strong class="text-danger"> {{$data['withdraw']}} </strong>元。
 </blockquote>
 <section>
 <hr/>
 <div id="tabs" class="tabs" style="height: 550px;">
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
 var start="{{$conditions['beginTime'] or ''}}";
 var end="{{$conditions['endTime'] or ''}}";
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
 setTimeout(function(){
      $('#beginTime').val(start.format('YYYY-MM-DD'));
      $('#endTime').val(end.format('YYYY-MM-DD'));
      $('#dateTimeRange').val(start+'-'+end);
      console.log(start);
 },100);
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
