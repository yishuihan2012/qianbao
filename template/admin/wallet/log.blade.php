@extends('admin/layout/layout_main')
@section('title','钱包详情~')  
@section('wrapper')
 <style>
  .content td{vertical-align: middle;}
 </style>
<blockquote>
    <form action="" method="post">
            
             <div class="input-group" style="width: 150px;float: left;margin-right: 10px;">
                <span class="input-group-addon">收入支出</span>
                     <select name="log_wallet_type" class="form-control">
                            <option value="1">全部</option>
                             <option value="1">收入</option>
                             <option value="2">支出</option>
                               
                    </select>
         
                 </div>
            <div class="col-sm-2">
                <div class="input-group">
                     <input type="text" class="form-control date-picker" id="dateTimeRange" placeholder="时间段搜索" />
                     <input type="hidden" name="beginTime" id="beginTime" value="" />
                     <input type="hidden" name="endTime" id="endTime" value="" />
                     <z class='clearTime'>X</z>
                </div>
            </div>
            <div class="col-sm-1">
                 <input type="hidden" name="memberId" id="id" value="{{$log_wallet_id}}" />
                <button class="btn btn-primary" type="submit">搜索</button>
            </div>
    </form>
</blockquote>
    <h3>
      <i class="icon icon-yen"></i> 钱包收入 <small>共 <strong class="text-danger">{{$entertottal}}</strong> 元</small>
      <i class="icon icon-yen"></i> 钱包支出 <small>共 <strong class="text-danger">{{$leavetotal}}</strong> 元</small>
    </h3>
 <hr/>
<div class="items items-hover">
<div class="row">
    <div class="col-sm-2 text-center"><strong>添加时间</strong></div>
    <div class="col-sm-2 text-center">
        操作类型
    </div>
    <div class="col-sm-2 huise">操作金额</div>
    <div class="col-sm-4 text-ellipsis" title="">操作信息</div>

</div>
<hr/>
@foreach($WalletLog as $log)
<div class="row">
    <div class="col-sm-2 text-center"><strong>{{$log->log_add_time}}</strong></div>
    <div class="col-sm-2 text-center">
        @if($log->log_relation_type=='1')
        分润收益~
        @elseif($log->log_relation_type=='5')
        邀请红包~
        @elseif($log->log_relation_type=='2')
        分佣收益~
        @elseif($log->log_relation_type=='4')
        其他收益~        
        @endif
    </div>
    <div class="col-sm-2 huise"><i class="icon icon-{{$log->log_wallet_type=='1' ? 'plus' : 'minus' }}"></i>{{$log->log_wallet_amount}}</div>
    <div class="col-sm-4 text-ellipsis" title="{{$log->log_desc}}">{{$log->log_desc}}</div>
    <div class="col-sm-1 text-center"><i class="icon icon-check text-success"></i></div>
</div>
<hr/>
@endforeach
<h2></h2>
</div>

{!!$WalletLog->render()!!}
 <script type="text/javascript">
 $(document).ready(function(){
      $('table.datatable').datatable({sortable: true});
         $('.menu .nav .active').removeClass('active');
         $('.menu .nav li.wallet').addClass('active');
         $('.menu .nav li.wallet-manager').addClass('show');

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
 });
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
