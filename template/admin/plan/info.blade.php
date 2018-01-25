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
   


</form>
  <div class="items items-hover">
      <!-- HTML 代码 -->
        <table class="table datatable">
           <thead>
           	<tr>
		 		<th>通道</th>
		 		<th>会员名称</th>
		 		<th>订单消费类型</th>
		 		<th>信用卡号</th>
		 		<th>订单金额</th>
		 		<th>订单手续费</th>
		 		<th>订单状态</th>
		 		<th>订单描述</th>
		 		<th>订单执行时间</th>
		 		<th>订单更新时间</th>
		 		<th>订单创建时间</th>
		 		<th>操作</th>
		 		<!-- <th>返回订单号</th>
		 		<th>返回结果描述</th>
		 		<th>返回状态</th> -->
	 	    </tr>
	 	</thead>
	 	 <tbody>
	 	@foreach($list as $key => $value)
		 <tr>
		 	<td>{{$value->passageway_name}}</td>
			 <td>{{$value->member_nick}}</td>
			 <td>@if($value->order_type == 1) 消费 @else 还款 @endif</td>
			 <td>{{$value->order_card}}</td>
			 <td>{{$value->order_money}}</td>
			 <td>{{$value->order_pound}}</td>
			 <td>@if($value->order_status == 1) 待执行 @elseif($value->order_status == 2) 成功 @elseif($value->order_status == 3) 取消 @elseif($value->order_status ==4) 带查证 @else 失败 @endif </td>
			 <td>{{$value->order_desc}}</td>
			 <td>{{$value->order_time}}</td>
			 <td>{{$value->order_edit_time}}</td>
			 <td>{{$value->order_add_time}}</td>
			 <td>
			 	@if($value->order_status == 3)
			  	<!-- <a class="remove" href="#" data-url="{{url('/index/Plan/order_status/status/1/id/'.$value['order_id'])}}"><i class="icon-pencil"></i> 继续执行 </a> -->
			  	@endif
			  	@if($value->order_status == 1)
			  	<a class="remove" href="#" data-url="{{url('/api/Memnernet/action_single_plan/id/'.$value['order_id'])}}"><i class="icon-pencil"></i> 立即执行 </a>
			  	&nbsp;&nbsp;&nbsp;&nbsp;
			  	<a class="remove" href="#" data-url="{{url('/index/Plan/order_status/status/3/id/'.$value['order_id'])}}"><i class="icon-pencil"></i> 取消执行 </a>
			  	@endif
			  	@if($value->order_status == -1)
			  	<a class="remove" href="#" data-url="{{url('/api/Memnernet/action_single_plan/id/'.$value['order_id'])}}"><i class="icon-pencil"></i> 重新执行 </a>
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
  <a  href="{{url('/index/server_model/index')}}" >返回</a>
</div>
</section>
<script>
 
  $(document).ready(function(){
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.plan').addClass('active');
    	 $('.menu .nav li.plan-manager').addClass('show');

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
		 bootbox.confirm({
		    title: "计划列表详情",
		    message: "是否执行此操作",
		    buttons: {
		        cancel: {label: '<i class="fa fa-times"></i> 点错'},
		        confirm: {label: '<i class="fa fa-check"></i> 确定'}
		    },
		    callback: function (result) {
		    	 if(result)
		    	 	window.location.href=url;
		    }
		 });
    	 })
});
</script>
@endsection