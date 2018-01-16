@extends('admin/layout/layout_main')
@section('title','会员列表管理~')  
@section('wrapper')
 <style>
  .content td{vertical-align: middle;}
 </style>
<blockquote>
	<form action="" method="post">
  			
            <div class="col-sm-2">
                <div class="input-group">
                     <span class="input-group-btn"><button class="btn btn-default" type="button">金额</button></span>
                     <input type="text" class="form-control" name="min_money">
                     <span class="input-group-btn fix-border"><button class="btn btn-default" type="button">~</button></span>
                     <input type="text" class="form-control" name="max_money">
                </div>
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
            	 <input type="hidden" name="memberId" id="memberId" value="{{$memberId}}" />
                <button class="btn btn-primary" type="submit">搜索</button>
            </div>
	</form>
		分佣/分润总共<em style="color:red;">{{$sum}}</em>元
</blockquote>
 <hr/>
<div class="items items-hover">
     
<table class="table table-striped table-hover">
    <thead>
	 	<tr>
	 		<th>支付类型</th>
	 		<th>分佣/分润金额</th>
	 		<th>分佣分润状态</th>
	 		<th>简介</th>
	 		<th>添加时间</th>
	 	</tr>

	 	@foreach($list as $k => $v)
		 <tr>
			 <td>@if($v['commission_type'] ==1) 支付分润 
			 	@elseif($v['commission_type'] == 2) 分佣 @else 代还分润 @endif
			 </td>
			 <td>{{$v->commission_money}}</td>
			 <td>@if($v->commission_state == 1 ) 正常 @else 异常许审核 @endif</td>
			  <td>{{$v->commission_desc}}</td>
			  <td>{{$v->commission_creat_time}}</td>
		 </tr>
		@endforeach
 </tfoot>
</table>
{!! $list->render() !!}

 <script type="text/javascript">
 $(document).ready(function(){
      $('table.datatable').datatable({sortable: true});
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.member').addClass('active');
    	 $('.menu .nav li.member-manager').addClass('show');

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