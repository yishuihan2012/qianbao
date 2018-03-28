 @extends('admin/layout/layout_main')
 @section('title','财务管理-消费分润管理~')
 @section('wrapper')
 <style>
   h4 > a,.pull-right > a{color:#145ccd;}
      .clearTime{ position: absolute; right: 5px; top: 5px; z-index: 99; border: 1px solid; color: red; font-size: .6rem; padding: 0 5px;}
 </style>
 <div class="panel">
    <header>
    <h3>
     分润统计: 共成功分润<strong class="text-danger"> {{$data['count']}}</strong> 笔, 总金额为 <strong class="text-danger">{{$data['money']}}</strong> 元
    </h3>

  </header>
      <div class="panel-body">
      <form action="" method="post">
    <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
            <span class="input-group-btn"><button class="btn btn-default" type="button">收益人</button></span>
        <div class="input-control ">
             <input id="inputAccountExample1" type="text" class="form-control" name="parent" placeholder="用户名/手机号" value="{{$r['parent'] or ''}}">
        </div>
    </div>
    <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
            <span class="input-group-btn"><button class="btn btn-default" type="button">触发人</button></span>
        <div class="input-control ">
             <input id="inputAccountExample1" type="text" class="form-control" name="child" placeholder="用户名/手机号" value="{{$r['child'] or ''}}">
        </div>
    </div>

         <div class="col-sm-2">
              <div class="input-group">
                   <span class="input-group-btn"><button class="btn btn-default" type="button">金额</button></span>
                   <input type="text" class="form-control" name="min_money" value="{{$r['min_money'] or ''}}">
                   <span class="input-group-btn fix-border"><button class="btn btn-default" type="button">~</button></span>
                   <input type="text" class="form-control" name="max_money" value="{{$r['max_money'] or ''}}">
              </div>
         </div>
         <div class="col-sm-2">
              <div class="input-group">
                   <input type="text" class="form-control date-picker" id="dateTimeRange" placeholder="收益时间查询" value="" readonly/>
                   <input type="hidden" name="beginTime" id="beginTime" value="{{$beginTime or ''}}" />
                   <input type="hidden" name="endTime" id="endTime" value="{{isset($endTime)?$endTime:''}}" />
                   <z class='clearTime'>X</z>
              </div>
         </div>
           <div class="col-sm-1">
                <button class="btn btn-primary" type="submit">搜索</button>
           </div>
           <input type="hidden" name="is_export" class="is_export" value="0">
           <div class="input-group" style="width: 180px;float: left; margin-right: 10px;">
            <span class="input-group-addon">导出页码,10万/页</span>
            <input type="text" name="start_p" class="form-control start_p" value="">
          </div>
  <button class="btn btn-primary export" type="submit">导出</button>
      </form>
    </div>
 </div>

 <section>
 <hr/>
 <table class="table">
      <thead>
           <tr>
                <th>#</th>
                <th>收益人</th>
                <th>触发人</th>
                <th>分润金额</th>
                <th>收益人费率</th>
                <th>收益人代扣费</th>
                <th>备注</th>
                <th>时间</th>
           </tr>
      </thead>
      <tbody>
        @foreach($list as $key)
           <tr>
              <td>{{$key['commission_id']}}</td>
              <td>{{$key['parent']}}</td>
              <td>{{$key['child']}}</td>
              <td>{{$key['commission_money']}}</td>
              <td>{{$key['commission_cash_rate']}}</td>
              <td>{{$key['commission_cash_fix']}}</td>
              <td>{{$key['commission_desc']}}</td>
              <td>{{$key['commission_creat_time']}}</td>
  
           </tr>
        @endforeach
      </tbody>
      <tfoot>
           <tr>
                <td colspan="10">{!! $list->render() !!}</td>
           </tr>
      </tfoot>
 </table>
 </section>

 <script type="text/javascript">
  $('.export').click(function(){
  $(".is_export").val(1);
  setTimeout(function(){
    $(".is_export").val(0);
  },100);
  var start_p=$('.start_p').val();
  var end_p=$('.end_p').val();
  if(start_p){
    var re=/^\d+$/;
    if(!re.test(start_p)){
      alert('导出页码请输入数字');
      return false;
    }
  }
  alert("数据量大的话请耐心等待不要重复点击导出\n单次最大10万条数据\n点击确定开始导出");
})
 $(document).ready(function(){
       $('.menu .nav .active').removeClass('active');
       $('.menu .nav li.fenrun_center').addClass('active');
       $('.menu .nav li.financial-manager').addClass('show'); 
 });
 //时间日期
 var start="{{$r['beginTime'] or ''}}";
 var end="{{$r['endTime'] or ''}}";
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
           if(start){
               $('#dateTimeRange').val(start+'-'+end);
           }
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
