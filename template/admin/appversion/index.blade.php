@extends('admin/layout/layout_main')
@section('title','app版本号列表')
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
	      	<th>ID</th>
	      	<th>版本名称</th>
	      	<th>版本code</th>
	      	<th>类型</th>
	      	<th>版本连接</th>
	      	<th>版本描述</th>
	      	<th>版本状态</th>
	
	      	<th>是否强制</th>
	      	<th>操作</th>
	    </tr>
 	</thead>
  	<tbody>
  	@foreach($Appversions as $key => $val)
	    <tr>
	      	<td>{{$val->version_id}}</td>
	      	<td>{{$val->version_name}}</td>
	      	<td>{{$val->version_code}}</td>
	      	<td>{{$val->version_type}}</td>
	      	<td>{{$val->version_link}}</td>
	      	<td>{{$val->version_desc}}</td>
	      	<td>@if($val->version_state == 0) 旧版本 @else 新版本 @endif</td>
	      	<td>@if($val->version_force == 0) 否 @else 是 @endif</td>
	
	      	<td>
	      		

	  				<a type="button" class="btn btn-sm" href="{{url('/index/appversion/remove/id/'.$val['version_id'])}}">删除</a>

				</div>
	      	</td>
	    </tr>
	@endforeach
  	</tbody>
  	<tfoot>
	    <tr>
	      	<td colspan="15">{!! $Appversions->render() !!}</td>
	    </tr>
  	</tfoot>
</table>
<script type="text/javascript">
$(document).ready(function(){
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.setting-Appversion').addClass('active');
    $('.menu .nav li.system-setting').addClass('show');
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
		    				type = data ? 'success' : 'error';
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
