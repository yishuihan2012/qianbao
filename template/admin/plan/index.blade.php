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
	      	<th>已还款总额</th>
	      	<th>剩余总额</th>
	      	<th>手续费</th>
	      	<th>计划状态</th>
	      	<th>订单状态</th>
	      	<th>订单类型</th>
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
	      	<td >{{$v['generation_card']}}</td>
	      	<td >{{$v['generation_total']}}</td>
	      	<td >{{$v['generation_count']}}</td>
	      	<td >{{$v['generation_has']}}</td>
	      	<td >{{$v['generation_left']}}</td>
	      	<td >{{$v['generation_pound']}}</td>
	      	<td >@if($v['generation_state']==2) 还款中 @elseif($v['generation_state']==3)还款结束 @elseif($v['generation_state']==-1)还款失败 @endif</td>
	      	<td></td>
	      
	    </tr>
	    	@endforeach
  	</tfoot>
</table>
{!!$list->render()!!}
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
