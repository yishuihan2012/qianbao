@extends('admin/layout/layout_main')
@section('title','服务管理')
@section('wrapper')
<div class="panel">
  	<div class="panel-body">
  		<header>
		    <h3>
		        <i class="icon-list-ul"></i> 服务列表 <small>共 <strong class="text-danger">{{$count}}</strong> 条</small> 
		    </h3>
	   </header>

  	</div>
</div>

<table class="table table-striped table-hover">
  	<thead>
	    <tr>
	      	<th>ID</th>
	      	<th>名称</th>
	      	<th>所属模块</th>
	      	<th>会员专属</th>
	      	<th>图标</th>
	      	<th>权重</th>
	      	<th>是否开启</th>
	      	<th>创建时间</th>
	      	<th>操作</th>
	    </tr>
 	</thead>
  	<tbody>
  	@foreach($list as $val)
	    <tr>
	      	<td>{{$val->list_id}}</td>
	      	<td><a href="{{$val->list_url}}">{{$val->list_name}}</a></td>
	      	<td>{{$val->serverItem->item_name}}</td>
	      	<td>{{$val->list_authority ? '是' : '否'}}</td>
	      	<td><img src="{{$val->list_icon}}" style="width: 20px"></td>
	      	<td>{{$val->list_weight}}</td>
	      	<td>{{$val->list_state ? '是' : '否'}}</td>
	      	<td>{{$val->list_add_time}}</td>
	      	<td>
	      		<div class="btn-group">
	  				<a type="button" class="btn btn-sm" data-remote="{{url('/index/server_model/show_service/list_id/'.$val->list_id)}}" data-size='lg' data-toggle="modal" href="#">编辑</a>
	  				<a type="button" class="btn btn-sm" href="{{url('/index/server_model/del_service/list_id/'.$val->list_id)}}">删除</a>
				</div>
	      	</td>
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
    	 $('.menu .nav li.model-server').addClass('active');
    	 $('.menu .nav li.model-manager').addClass('show');
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
