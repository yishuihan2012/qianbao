@extends('admin/layout/layout_main')
@section('title','钱包日志管理~')
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
	      	<th>#</th>
	      	<th>会员</th>
	      	<th>订单号</th>
	      	<th>操作金额</th>
	      	<th>描述</th>
	      	<th>添加时间</th>
	      	<th>状态</th>
	    </tr>
 	</thead>
  	<tbody>
  	@foreach($list as $log)
	    <tr>
	      	<td>{{$log['log_id']}}</td>
	      	<td></td>
	      	<td><code></code></td>
	      	<td>{{format_money($log['log_wallet_amount'])}}</td>
	      	<td class="text-ellipsis" title="{{$log->log_desc}}">{{$log['log_desc']}}</td>
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
});
</script>
<!---->
@endsection
