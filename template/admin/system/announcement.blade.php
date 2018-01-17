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
	      	<th>标题</th>
	      	<th>创建人</th>
	      	<th>创建时间</th>
	      	<th>操作</th>
	    </tr>
 	</thead>
  	<tbody>
  	@foreach($list as $val)
	    <tr>
	      	<td>{{$val->announcement_id}}</td>
	      	<td>{{$val->announcement_title}}</td>
	      	<td>{{$val->adminster_login}}</td>
	      	<td>{{$val->announcement_createtime}}</td>
	      	<td>
	      		<div class="btn-group">
	  				<a type="button" class="btn btn-sm" data-remote="{{url('/index/system/show_announcement/announcement_id/'.$val->announcement_id)}}" data-size='lg' data-toggle="modal" href="#">查看</a>

	  				<a type="button" class="btn btn-sm" href="{{url('/index/system/del_announcement/announcement_id/'.$val->announcement_id)}}">删除</a>

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
    $('.menu .nav li.setting-announcement').addClass('active');
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
