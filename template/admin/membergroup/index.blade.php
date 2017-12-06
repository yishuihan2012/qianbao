@extends('admin/layout/layout_main')
@section('title','会员用户组管理~')
@section('wrapper')
<table class="table table-striped table-hover">
  	<thead>
	    <tr>
	      	<th>#</th>
	      	<th>用户组</th>
	      	<th>类型</th>
	      	<th>红包类型</th>
	      	<th>更新时间</th>
	      	<th>注册时间</th>
	      	<th>操作</th>
	    </tr>
 	</thead>
  	<tbody>
  	@foreach ($list as $group)
	    <tr>
	      	<td>{{$group['group_id']}}</td>
	      	<td>{{$group['group_name']}}</td>
	      	<td>{{$group['group_type']=='1' ? '内部' : '外部'}}</td>
	      	<td></td>
	      	<td>{{$group['group_update_time']}}</td>
	      	<td>{{$group['group_add_time']}}</td>
	      	<td>
	      		<div class="btn-group">
	  				<a type="button" class="btn btn-sm" data-remote="{{ url('/index/member_group/group_edit/id/'.$group['group_id']) }}" data-toggle="modal" data-size="lg" href="#">编辑</a>
	  				<div class="btn-group">
					    <button type="button" class="btn dropdown-toggle btn-sm" data-toggle="dropdown"><span class="caret"></span></button>
					    <ul class="dropdown-menu" role="menu">
					      	<!-- <li class="divider"></li> -->
							<li><a class="GroupRemove" href="#" data="{{ $group['group_id'] }}" explain="删除会员组">删除</a></li>
					    </ul>
	  				</div>
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
    $('.menu .nav li.member_group').addClass('active');
    $('.menu .nav li.member-manager').addClass('show');
    $(".GroupRemove").click(function(){
    	var id  	=$(this).attr("data");
		bootbox.prompt({
		    title: "将本组会员移动至用户组",
		    inputType: 'select',
		    inputOptions: [
		    	@foreach ($lists as $group)
		        {
		            text: "{{ $group->group_name }}",
		            value: "{{ $group->group_id }}",
		        },
		        @endforeach
		    ],
		    callback: function (result) {
		        if(result==id)
		        {
		        	bootbox.alert("请移至其他用户组");
		        	return;
		        }
		        if(result!=null)
		        {
        			var dialog = bootbox.dialog({
					    title: '正在移动会员...',
					    message: '<p><i class="icon icon-spin icon-spinner-indicator"></i> Loading...</p>',
					    buttons: {
		                    Success: {
		                        label: "确认",
		                        className: "btn-primary",
		                        callback: function() {window.location.reload();}
		                    }
                		}
					});
		    		$.ajax({
		    			url:'{{ url("member_group/group_remove") }}',
		    			type:'POST',
		    			data: {id:id,result:result},
		    			dataType: "Json",
		    			success:function(data){
							dialog.init(function(){
							    setTimeout(function(){
							        dialog.find('.bootbox-body').html('删除会员组' + data ? '成功' : '失败');
							    }, 1000);
							});
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
