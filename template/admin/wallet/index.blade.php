@extends('admin/layout/layout_main')
@section('title','钱包列表管理~')
@section('wrapper')
<div class="panel">
  	<div class="panel-body">
  		<form action="" name="myform" class="form-group" method="get">

		</form>

  	</div>
</div>

<table class="table table-striped table-hover">
  	<thead>
	    <tr>
	      	<th>会员</th>
	      	<th>钱包余额</th>
	      	<th>钱包状态</th>
	      	<th>更新时间</th>
	      	<th>添加时间</th>
	      	<th>操作</th>
	    </tr>
 	</thead>
  	<tbody>
  	@foreach($list as $wallet)
	    <tr>
	      	<td>{{$wallet->member_nick}}</td>
	      	<td>{{format_money($wallet->wallet_amount)}}</td>
	      	<td><span class="label label-{{$wallet->wallet_state=='2' ? 'success' : 'danger'}}">{{$wallet->wallet_state=='2' ? '正常' : '冻结' }}</span></td>
	      	<td>{{$wallet->wallet_update_time}}</td>
	      	<td>{{$wallet->wallet_add_time}}</td>
	      	<td>
	      		<div class="btn-group">
	  				<a type="button" class="btn btn-sm" data-remote="{{url('/index/wallet/look_log/id/'.$wallet->wallet_id)}}" data-size='lg' data-toggle="modal" href="#">查看日志</a>
	  				<div class="btn-group">
					    <button type="button" class="btn dropdown-toggle btn-sm" data-toggle="dropdown"><span class="caret"></span></button>
					    <ul class="dropdown-menu" role="menu">
					      	<!-- <li class="divider"></li> -->
					      	@if($wallet->wallet_state=='2')
					      	<li><a class="freezing" href="#" data-id="{{$wallet->wallet_id}}" explain="冻结此钱包">冻结</a></li>
					      	@else
					      	<li><a class="freezing" href="#" data-id="{{$wallet->wallet_id}}" explain="解冻此钱包">解冻</a></li>
					      	@endif
					    </ul>
	  				</div>
				</div>
	      	</td>
	    </tr>
	@endforeach
  	</tbody>
  	<tfoot>
	    <tr>
	      	<td colspan="4">{!! $list->render() !!}</td>
          <td colspan="2" style="line-height: 55px">当前共<em style="font-size: 20px;color:red">{{$count}}</em>条记录</td>
	    </tr>
  	</tfoot>
</table>
<script type="text/javascript">
$(document).ready(function(){
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.wallet').addClass('active');
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
