@extends('admin/layout/layout_main')
@section('title','新手指引管理')
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
	      	<th>名称</th>
	      	
	      	<th>分类</th>
	      	<th>操作</th>
	    </tr>
 	</thead>
  	<tbody>
  	@foreach($MemberNovice as $val)
	    <tr>
	      	<td>{{$val->novice_id}}</td>
	      	<td><a href="">{{$val->novice_name}}</a></td>
	     
	      	<td>{{$val->novice_class_title}}</td>
	      	<td>
	      		<a type="button" class="btn btn-sm" data-remote="{{url('/index/article/noviceSave/novice_id/'.$val->novice_id)}}" data-size='lg' data-toggle="modal" href="#">编辑</a>
	  			<a type="button" class="btn btn-sm" href="{{url('/index/article/noviceRemove/novice_id/'.$val->novice_id)}}">删除</a>
	      	</td>
	    </tr>
	@endforeach
  	</tbody>
  	<tfoot>
	    <tr>
	      	<td colspan="15">{!! $MemberNovice->render() !!}</td>
	    </tr>
  	</tfoot>
</table>
<script type="text/javascript">
$(document).ready(function(){
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.new_zhiyin').addClass('active');
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
