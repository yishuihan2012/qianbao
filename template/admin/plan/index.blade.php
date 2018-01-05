@extends('admin/layout/layout_main')
@section('title','还款计划~')
@section('wrapper')
<style>
	.text-ellipsis{cursor: pointer;}
</style>
<div class="panel">
  	<div class="panel-body">
  		<form action="" name="myform" class="form-group" method="get">

		</form>

  	</div>
</div>

<table class="table table-striped table-hover">
  	<thead>
	    <tr>
	      	<th>还款会员</th>
	      	<th>还款会员手机号</th>
	      	<th>代还会员</th>
	      	<th>代还会员手机号</th>
	      	<th>需还款信用卡</th>
	      	<th>需还款总额</th>
	      	<th>还款次数</th>
	      	<th>代还会员</th>
	      	<th>已还款总额</th>
	      	<th>剩余总额</th>
	      	<th>手续费</th>
	    </tr>
 	</thead>
  	<tbody>
  
	    
  	</tbody>
  	<tfoot>
  		@foreach($list as $k => $v)
	    <tr>
	    	
	      	<td >{{$v['o_member_nick']}}</td>
	      	<td >{{$v['o_member_mobile']}}</td>
	      	<td >{{$v['member_nick']}}</td>
	      	<td >{{$v['member_mobile']}}</td>
	      	<td ></td>
	      	<td ></td>
	      	<td ></td>
	      	<td ></td>
	      	<td ></td>
	      	<td ></td>
	      	<td ></td>

	      
	    </tr>
	    	@endforeach
  	</tfoot>
</table>
{!!$page!!}
<script type="text/javascript">
$(document).ready(function(){
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.plan').addClass('active');
    $('.menu .nav li.plan-manager').addClass('show');
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
});
</script>
<!---->
@endsection
