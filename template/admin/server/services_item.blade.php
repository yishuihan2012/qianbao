@extends('admin/layout/layout_main')
@section('title','系统公告列表')
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
	      	<th>模块名称</th>
	      	<th>模块图标</th>
	      	<th>排序</th>
	      	<th>是否显示</th>
	      	<th>创建时间</th>
	      	<th>操作</th>
	    </tr>
 	</thead>
  	<tbody>
  	@foreach($list as $val)
	    <tr>
	      	<td>{{$val->item_id}}</td>
	      	<td>{{$val->item_name}}</td>
	      	<td><img src="{{$val->item_icon}}" class="img-responsive" style="width:30px;height:30px"></td>
	      	<td>{{$val->item_weight}}</td>
	      	<td>{{$val->item_state==1 ? '显示' : '不显示'}}</td>
	      	<td>{{$val->item_add_time}}</td>
	      	<td>
	      		<div class="btn-group">
	  				<a type="button" class="btn btn-sm" data-remote="{{url('/index/server_model/edit_model/item_id/'.$val->item_id)}}" data-size='lg' data-toggle="modal" href="#">编辑</a>
	  				<a type="button" class="btn btn-sm" href="{{url('/index/server_model/del_model/item_id/'.$val->item_id)}}">删除</a>
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
    	 $('.menu .nav li.model-list').addClass('active');
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
