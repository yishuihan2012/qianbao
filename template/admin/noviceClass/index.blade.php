@extends('admin/layout/layout_main')
@section('title','新手分类列表~')
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
	      	<th>ID</th>
	      	<th>标题</th>
	      	<th>添加时间</th>
	      	<th>操作</th>
	    </tr>
 	</thead>
  	<tbody>
  		@foreach($list as $key => $val)
  		<tr>
  			<td>{{$val->novice_class_id}}</td>
  			<td>{{$val->novice_class_title}}</td>
  			<td>{{$val->novice_class_time}}</td>
  			<td><a type="button" class="btn btn-sm" href="{{url('/index/Novice_class/remove/id/'.$val['novice_class_id'])}}">删除</a></td>
  		</tr>
	    @endforeach
  	</tbody>
  	<tfoot>
	    <tr>
	      	<td colspan="15"></td>
	    </tr>
  	</tfoot>
</table>
<script type="text/javascript">
$(document).ready(function(){
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.novice_calss').addClass('active');
    $('.menu .nav li.article-manager').addClass('show');
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
