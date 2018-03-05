 <!--dialog Title-->
@extends('admin/layout/layout_main')
@section('title','订单列表管理~')
@section('wrapper')
<style>
	 h4 > a,.pull-right > a{color:#145ccd;}
</style>

<section>
<hr/>
<div class="list">
  <header>
    <h3><i class="icon-list-ul"></i> 计划列表详情 <small>共 <strong class="text-danger">{{count($list)}}</strong> 条</small></h3>
  </header>
      <div class="panel">
    <div class="panel-body">
      <form action="" name="myform" class="form-group" method="get">
          <div class="input-group" style="width: 150px;float: left;margin-right: 20px;">
            <span class="input-group-addon">会员名称</span>
            <input type="text" class="form-control" name="member_nick" value="{{$r['member_nick']}}" placeholder="会员名称" >
          </div>

          <div class="input-group" style="width: 200px;float: left;margin-right: 20px;">
            <span class="input-group-addon">手机号</span>
            <input type="text" class="form-control" name="member_mobile" value="{{$r['member_mobile']}}" placeholder="手机号">
          </div>
          <div class="input-group" style="width: 200px;float: left;margin-right: 20px;">
            <span class="input-group-addon">订单金额</span>
            <input type="text" class="form-control" name="order_money" value="{{$r['order_money']}}" placeholder="订单金额">
          </div> 
          <div class="input-group" style="width: 200px;float: left;margin-right: 20px;">
            <span class="input-group-addon">计划ID</span>
            <input type="text" class="form-control" name="id" value="{{$r['order_id']}}" placeholder="计划ID">
          </div> 
            <div class="input-group" style="width: 150px;float: left;margin-right: 10px;">
		     <span class="input-group-addon">计划状态</span>
		  <select name="order_status" class="form-control">
		    <option value="" >全部</option>
		    <option value="1" @if($r['order_status']==1) selected @endif>待执行</option>
		    <option value="2" @if($r['order_status']==2) selected @endif>成功</option>
		    <option value="3" @if($r['order_status']==3) selected @endif>取消</option>
		    <option value="-1" @if($r['order_status']==-1) selected @endif>失败</option>
		    <option value="4" @if($r['order_status']==4) selected @endif>待查证</option>
		  </select>
		 
		  </div>
		  <div class="input-group" style="width: 150px;float: left;margin-right: 10px;">
		     <span class="input-group-addon">订单类型</span>
		  <select name="order_type" class="form-control">
		    <option value="" >全部</option>
		    <option value="1" @if($r['order_type']==1) selected @endif>消费</option>
		    <option value="2" @if($r['order_type']==2) selected @endif>还款</option>
		  </select>
		 
		  </div>
          <div class="input-group" style="width: 200px;float: left; margin-right: 10px;">
              <input type="text" class="form-control date-picker" id="dateTimeRange" placeholder="执行时间" />
              <input type="hidden" name="beginTime" id="beginTime" value="{{isset($beginTime)?$beginTime:'1'}}" />
              <input type="hidden" name="endTime" id="endTime" value="{{isset($endTime)?$endTime:'2'}}" />
              <z class='clearTime'>X</z>
          </div>
          <button class="btn btn-primary" type="submit">搜索</button>
    </form>
    </div>
</div>

</form>
  <div class="items items-hover">
      <!-- HTML 代码 -->
        <table class="table datatable">
           <thead>
           	<tr>
           		<th>ID</th>
		 		<th>通道</th>
		 		<th>姓名</th>
		 		<th>手机号</th>
		 		<th>订单类型</th>
		 		<th>信用卡号</th>
		 		<th>银行名称</th>
		 		<th>订单金额</th>
		 		<th>订单手续费</th>
		 		<th>订单状态</th>
		 		<th>重新执行次数</th>
		 		<th>执行结果</th>
		 		<th>订单描述</th>
		 		<th>订单执行时间</th>
		 		<!-- <th>订单更新时间</th> -->
		 		<th>订单创建时间</th>
		 		<th>操作</th>
	 	    </tr>
	 	</thead>
	 	 <tbody>
	 	@foreach($list as $key => $value)
		 <tr style="">
		 	<td>{{$value->order_id}}</td>
		 	<td>{{$value->passageway_name}}</td>
			 <td>{{$value->member_nick}}</td>
			 <td>{{$value->member->member_mobile}}</td>
			 <td>@if($value->order_type == 1) <em style="color:#00FF00;"> 消费</em> @else <em style="color:#00FFFF;">还款</em>@endif </td>
			 <td>{{$value->order_card}}</td>
			 <td>{{$value->card_bankname}}</td>
			 <td>{{$value->order_money}}</td>
			 <td>{{$value->order_pound}}</td>
			 <td>@if($value->order_status == 1)<em style="color:#FF9900;">  待执行 </em>@elseif($value->order_status == 2)<em style="color:#33FF33;"> 成功</em> @elseif($value->order_status == 3)<em style="color:#FF00FF;"> 取消</em> @elseif($value->order_status ==4) <em style="color:#00FFFF;">带查证</em> @else <em style="color:red;">失败 </em>@endif </td>
			 <td>{{$value->order_retry_count}}</td>
			 <td>{{$value->back_statusDesc}}</td>
			 <td>{{$value->order_desc}}</td>
			 <td>{{$value->order_time}}</td>
			 <!-- <td>{{$value->order_edit_time}}</td> -->
			 <td>{{$value->order_add_time}}</td>
			 <td>
			 	@if($value->order_status == 3)
			  	<!-- <a class="remove" href="#" data-url="{{url('/index/Plan/order_status/status/1/id/'.$value['order_id'])}}"><i class="icon-pencil"></i> 继续执行 </a> -->
			  	@endif
			  	@if($value->order_status == 1)
			  	<a class="remove" href="#" data-url="{{url('/api/Membernet/action_single_plan/id/'.$value['order_id'])}}"><i class="icon-pencil"></i> 立即执行 </a>
			  	&nbsp;&nbsp;&nbsp;&nbsp;
			  	<a class="remove" href="#" data-url="{{url('/index/Plan/order_status/status/3/id/'.$value['order_id'])}}"><i class="icon-pencil"></i> 取消执行 </a>
			  	@endif
			  	@if($value->order_status == -1)
			  	<a class="remove" href="#" data-url="{{url('/api/Membernet/action_single_plan/id/'.$value['order_id'])}}"><i class="icon-pencil"></i> 重新执行 </a>
			  	@endif
			  	@if($value->order_status ==4) 
			  	<a class="remove1" data-toggle="modal" data-remote="{{url('/index/Plan/edit_status/id/'.$value['order_id'])}}" href="#"><i class="icon-pencil"></i> 更改状态 </a>
			  	@endif
			 </td>
			 <!-- <td>{{$value->back_tradeNo}}</td>
			 <td>{{$value->back_statusDesc}}</td>
			 <td>{{$value->back_status}}</td> -->
		 </tr>
		 @endforeach
	    </tbody>
	</table>
  </div>
  {!!$list->render()!!}
  <td colspan="7" style="line-height: 55px">当前共<em style="font-size: 20px;color:red">{{$count}}</em>条记录</td>
  <a  href="{{url('/index/plan/detail')}}" >返回</a>
</div>
</section>
<script>
 
$(document).ready(function(){
      var start="{{(isset($beginTime))?$beginTime : ''}}";
      var end="{{(isset($endTime))?$endTime : ''}}";
      // $('table.datatable').datatable({sortable: true});
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.plan_detail').addClass('active');
    	 $('.menu .nav li.plan-manager').addClass('show');
      $('#dateTimeRange span').html();
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
           $('#dateTimeRange').val(start+'-'+end);
      });
      setTimeout(function(){
           $('#beginTime').val(start.format('YYYY-MM-DD'));
           $('#endTime').val(end.format('YYYY-MM-DD'));
           $('#dateTimeRange').val(start+'-'+end);
           console.log(start);
      },100);
      begin_end_time_clear();
      $('.clearTime').click(begin_end_time_clear);
      function begin_end_time_clear() {
           $('#dateTimeRange').val('');
           $('#beginTime').val('');
           $('#endTime').val('');
      }
 });

		 $(".parent li a").click(function(){
		 	$("input[name='article_parent']").val($(this).attr('data-id'));
		 	$("input[name='article_category']").val(0);
		 	$("#myform").submit();
		 })
    	 $(".son li a").click(function(){
    	 	$("input[name='article_category']").val($(this).attr('data-id'));
    	 	$("#myform").submit();
    	 })
    	 $(".remove").click(function(){
    	 	 var url=$(this).attr('data-url');
    	 	 var ths=$(this);
		 bootbox.confirm({
		    title: "计划列表详情",
		    message: "是否执行此操作",
		    buttons: {
		        cancel: {label: '<i class="fa fa-times"></i> 点错'},
		        confirm: {label: '<i class="fa fa-check"></i> 确定'}
		    },
		    callback: function (result) {
		    	 if(result)
		    	 	$.ajax({
		    	 		url:url,
		    	 		type : 'POST',
		        		dataType : 'json',
		        		beforeSend:function(){
		        			ths.parent().html('<i class="icon icon-spin icon-spinner-indicator" style="z-index: 999;"></i>');
		        		},
		        		success:function(data){
		        			data = JSON.parse(data);
		        			alert(data.msg);
		        			window.location.reload(true);
		        		}
		    	 	})
		    	 	// window.location.href=url;
		    }
		 });
    	 })
</script>
@endsection