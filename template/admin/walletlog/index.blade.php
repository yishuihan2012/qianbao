@extends('admin/layout/layout_main')
@section('title','钱包日志管理~')
@section('wrapper')
<style>
	.text-ellipsis{cursor: pointer;}
</style>
<header>
  <h3>  
    <i class="icon-list-ul"></i> 日志条数 <small>共 <strong class="text-danger">{{$count}}</strong> 条</small>
    <i class="icon icon-yen"></i> 钱包收入 <small>共 <strong class="text-danger">{{$entertottal}}</strong> 元</small>
      <i class="icon icon-yen"></i> 钱包支出 <small>共 <strong class="text-danger">{{$leavetotal}}</strong> 元</small>
    </h3>
  </header>
<div class="panel">
  	<div class="panel-body">
  		<form action="" name="myform" class="form-group" method="get">
        <div class="input-group" style="width: 150px;float: left;margin-right: 10px;">
          <span class="input-group-addon">用户名</span>
          <input type="text" class="form-control" name="member_nick" value="" placeholder="用户名">
        </div>
  			 <div class="input-group" style="width: 150px;float: left;margin-right: 10px;">
                <span class="input-group-addon">收入支出</span>
                     <select name="log_wallet_type" class="form-control">
                             <option value="">全部</option>
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
                <button class="btn btn-primary" type="submit">搜索  </button>
            </div>
		</form>
  	</div>

</div>

<table class="table table-striped table-hover">
  	<thead>
	    <tr>
	      	<th>#</th>
	      	<th>用户名</th>
	      	<!-- <th>订单号</th> -->
          <th>操作金额</th>
	      	<th>实时余额</th>
	      	<th>描述</th>
	      	<th>添加时间</th>
	      	<th>状态</th>
	    </tr>
 	</thead>
  	<tbody>
  	@foreach($list as $log)
	    <tr>
	      	<td>{{$log['log_id']}}</td>
	      	<td>{{$log->wallet->member->member_nick}}</td>
	      	<!-- <td><code></code></td> -->
          <td><i class="icon icon-{{$log->log_wallet_type=='1' ? 'plus' : 'minus' }}">{{$log['log_wallet_amount']}}</td>
	      	<td><i class="icon icon-{{$log->log_wallet_type=='1' ? 'plus' : 'minus' }}">{{$log['log_balance']}}</td>
	      	<td class="text-ellipsis" title="{{$log->log_desc}}"><a class="Listen" href="{{$log['hrefurl']}}">{{$log['log_desc']}}</a></td>
	      	<td>{{$log['log_add_time']}}</td>
	      	<td><i class="icon icon-check text-success"></i></td>
	    </tr>
	@endforeach
  	</tbody>
  	<tfoot>
	    <tr>
	      	<td colspan="15">{!! $list->render() !!}</td>
	    </tr>
  	</tfoot>
</table>
<script type="text/javascript">
$(document).ready(function(){
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.walletlog').addClass('active');
    $('.menu .nav li.wallet-manager').addClass('show');
    $(".freezing").click(function(){
    	var id = $(this).attr('data-id');
    	var explain = $(this).attr('explain');
		bootbox.prompt({
		    title: "请输入要"+explain+"的原因",
		    inputType: 'text',
		    callback: function (result) {
		        if(result!=null){
		        	$.ajax({
		        		url : "{{url('/index/wallet/freezing')}}",
		        		data : {id:id,wallet_desc:result},
		        		type : 'POST',
		        		dataType : 'Json',
		        		success:function(data){
		    				explain+=data ? '成功' : '失败';
		    				type= data ? 'success' : 'error';
							new $.zui.Messager(explain, { type: type, close: true, }).show();
							window.location.reload();
		        		}
		        	})
		        }
		    }
		});
    })
    $(".Listen").click(function(){
      if($(this).attr('href')=="")
      {
        
        new $.zui.Messager('暂无订单信息~', { type: 'error', close: true, }).show();
        return false;
      }
    })
});
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
<!---->
@endsection
