@extends('admin/layout/layout_main')
@section('title','交易订单管理~')
@section('wrapper')
<style>
   h4 > a,.pull-right > a{color:#145ccd;}
</style>

<section>
<hr/>
<div class="list">
  <header>
    <h3><i class="icon-list-ul"></i> 交易订单 <small>共 <strong class="text-danger" style="font-size: 16px">{{$count['count_size']}}</strong> 条</small>
    <i class="icon icon-yen"></i>全部总金额 <small><strong class="text-danger" style="font-size: 16px">{{$count['order_money']}}</strong>元</small>
    <i class="icon icon-yen"></i>取现成功金额 <small><strong class="text-danger" style="font-size: 16px">{{$count['order_money_yes']}}</strong>元</small>
    <i class="icon icon-yen"></i>未支付金额 <small><strong class="text-danger" style="font-size: 16px">{{$count['order_money_del']}}</strong>元</small>
    <i class="icon icon-yen"></i>全部手续费 <small><strong class="text-danger" style="font-size: 16px">{{$count['order_charge']}}</strong>元</small></h3>
    </h3>
    <h3>
      <i class="icon-list-ul"></i> 成本手续费 <small>共 <strong class="text-danger" style="font-size: 16px">{{$count['chengben']}}</strong> 元</small>
      <i class="icon-list-ul"></i> 通道结算金额 <small>共 <strong class="text-danger" style="font-size: 16px">{{$count['yingli']}}</strong> 元</small>
      <i class="icon-list-ul"></i> 分润金额 <small>共 <strong class="text-danger" style="font-size: 16px">{{$count['sanji']}}</strong> 元</small>
      <i class="icon-list-ul"></i> 分润后盈利 <small>共 <strong class="text-danger" style="font-size: 16px">{{$count['fenrunhou']}}</strong> 元</small>
    </h3>
  </header>
   <form action="" method="post">
    <div class="input-group" style="width: 150px;float: left;margin-right: 20px;">
    <span class="input-group-addon">用户名</span>
    <input type="text" class="form-control" name="member_nick" value="{{$r['member_nick']}}" placeholder="用户名">
  </div>
  <!-- <div class="input-group" style="width: 150px;float: left;margin-right: 20px;">
    <span class="input-group-addon">交易流水号</span>
    <input type="text" class="form-control" name="order_no" value="{{$r['order_no']}}" placeholder="交易流水号">
  </div> -->

  <div class="input-group" style="width: 200px;float: left;margin-right: 20px;">
    <span class="input-group-addon">手机号</span>
    <input type="text" class="form-control" name="member_mobile" value="{{$r['member_mobile']}}" placeholder="手机号">
  </div>
   <div class="input-group" style="width: 240px;float: left;margin-right: 10px;">
    <span class="input-group-addon">信用卡号</span>
    <input type="text" class="form-control" name="order_creditcard" value="{{$r['order_creditcard']}}" placeholder="信用卡号">
  </div>
  <div class="input-group" style="width: 150px;float: left;margin-right: 10px;">
     <span class="input-group-addon">订单状态</span>
  <select name="order_state" class="form-control">
      <option value="">全部</option>
      <option value="1" @if($r['order_state']==1) selected @endif>待支付</option>
      <option value="2" @if($r['order_state']==2) selected @endif>成功</option>
      <option value="-1" @if($r['order_state']==-1) selected @endif>失败</option>
      <option value="-2" @if($r['order_state']==-2) selected @endif>超时</option>
      <option value="3" @if($r['order_state']==3) selected @endif>代付未成功</option>
  </select>
 
  </div>
  <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
     <span class="input-group-addon">会员级别</span>
  <select name="member_group_id" class="form-control">
      <option value="" @if ($r['member_group_id']=='') selected="" @endif>全部</option>
    @foreach($member_group as $v)
      <option value="{{$v['group_id']}}" @if ($r['member_group_id']==$v['group_id']) selected @endif>{{$v['group_name']}}</option>
    @endforeach
  </select>
  </div>
<div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
     <span class="input-group-addon">通道</span>
  <select name="passageway_id" class="form-control">
      <option value="" @if ($r['passageway_id']=='') selected="" @endif>全部</option>
    @foreach($passageway as $v)
      @if($v['passageway_state']==1 && $v['passageway_also']==1)
      <option value="{{$v['passageway_id']}}" @if ($r['passageway_id']==$v['passageway_id']) selected @endif>{{$v['passageway_name']}}</option>
      @endif
    @endforeach
  </select>
  </div>

  <div class="input-group" style="width: 200px;float: left; margin-right: 10px;">
      <input type="text" class="form-control date-picker" id="dateTimeRange" placeholder="交易订单添加时间" />
      <input type="hidden" name="beginTime" id="beginTime" value="" />
      <input type="hidden" name="endTime" id="endTime" value="" />
      <z class='clearTime'>X</z>
  </div>
  <button class="btn btn-primary" type="submit">搜索</button>
  <input type="hidden" name="is_export" class="is_export" value="0">
  <div class="input-group" style="width: 180px;float: left; margin-right: 10px;">
    <span class="input-group-addon">导出页码,10万/页</span>
    <input type="text" name="start_p" class="form-control start_p" value="">
  </div>
  <button class="btn btn-primary export" type="submit">导出</button>
</form>
  <div class="items items-hover">
      <!-- HTML 代码-->
          <table class="table datatable">
           <thead>
                 <tr>
                 <!-- 以下两列左侧固定 -->
                      <th>#</th>
                      <th>交易流水号</th>
                      <!-- <th>受益人</th> -->
                      <th>刷卡人</th>
                      <!-- <th>结算卡</th> -->
                      <!-- <th>信用卡</th> -->
                      <th class="flex-col">总金额</th>
                      <!-- <th class="flex-col">分润消耗</th>  -->
                      <th class="flex-col">刷卡手续费</th> 
                      <!-- <th class="flex-col">费率</th>  -->
                      <th class="flex-col">成本手续费</th>
                      <th class="flex-col">结算</th>
                      <th class="flex-col">分润</th>
                      <th class="flex-col">盈利</th>
                      <th class="flex-col">通道</th> 
                      <th>订单状态</th>
                      <!-- <th>备注</th> -->
                      <th>创建时间</th>
                      <th>操作</th>
                 </tr>
      </thead>
      <tbody>
           @foreach ($order_lists as $list)
           <tr>
                 <td>{{$list->order_id}}</td>
                 <td><code>{{$list->order_no}}</code></td>
                 <!-- <td></td> -->
                 <td>{{$list->order_name}}</td>
                 <!-- <td>{{$list->order_card}}</td> -->
                 <!-- <td>{{$list->order_creditcard}}</td> -->

                 <td>{{$list->order_money}}</td>
                 <!-- <td>{{$list->order_fen}}</td> -->
                 <td>{{$list->order_charge+$list->order_buckle}}
                    ({{$list->order_money}}*{{$list->user_rate}}%+{{$list->order_buckle}})</td>
                 <!-- <td>{{$list->order_also}}%</td> -->
                 <td>{{$list->order_passway_profit+$list->passageway_fix}}
                  ({{$list->order_money}}*{{$list->passageway_rate}}%+{{$list->passageway_fix}})</td>
                 <td>
                  {{$list->order_fen+$list->yingli}}
                </td>
                 <td>
                  @if($list->order_fen!=0)
                  <a href="/index/Financial/fenrun?commission_from={{$list->order_id}}&commission_type=1" target="_blank" >
                  {{$list->order_fen}}</a>
                  @else
                  0
                  @endif
                </td>
                 <td>{{$list->yingli}}</td>
                 <td>{{$passageway[$list->order_passway]['passageway_name']}}</td>

                 <td>@if($list->order_state==1)待支付 @elseif($list->order_state==2) 成功@elseif($list->order_state==-1)失败@elseif($list->order_state==-2) 超时@else代付未成功@endif</td>
                 <!-- <td>{{$list->order_desc}}</td> -->
                 <td>{{$list->order_add_time}}</td>
                 <td>
                      <div class="btn-group">
                           <button type="button" data-toggle="modal" data-remote="{{url('/index/order/showcash/id/'.$list->order_id)}}" class="btn btn-default btn-sm">详细信息</button>
                      </div>
                      <!-- <div class="btn-group">
                           <button type="button" data-toggle="modal" data-remote="{{url('/index/order/showcash/id/'.$list->order_id)}}" class="btn btn-default btn-sm">审核</button>
                      </div> -->
                 </td>
           </tr>
           @endforeach
      </tbody>
</table>
  </div>
{!! $order_lists->render() !!}
</div>
</section>
<script>
  $(document).ready(function(){
       $('.menu .nav .active').removeClass('active');
       $('.menu .nav li.cash').addClass('active');
       $('.menu .nav li.order-manager').addClass('show');
    //初始化时间
        $('#dateTimeRange').val('{{$r["beginTime"]}} - {{$r["endTime"]}}');
        $('#beginTime').val('{{$r["beginTime"]}}');
        $('#endTime').val('{{$r["endTime"]}}');
 })
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
        opens : 'left',    // 日期选择框的弹出位置
        separator : ' 至 ',
        showWeekNumbers : true,     // 是否显示第几周

 
        //timePicker: true,
        //timePickerIncrement : 10, // 时间的增量，单位为分钟
        //timePicker12Hour : false, // 是否使用12小时制来显示时间
 
         
        //maxDate : moment(),           // 最大时间
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

</script>
 <style type="text/css">
   .clearTime{
    position: absolute;
    right: 5px;
    top: 5px;
    z-index: 99;
    border: 1px solid;
    color: red;
    font-size: .6rem;
    padding: 0 5px;
   }
 </style>
@endsection
