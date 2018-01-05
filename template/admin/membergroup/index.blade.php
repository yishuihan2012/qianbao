@extends('admin/layout/layout_main')
@section('title','会员用户组管理~')
@section('wrapper')
<blockquote>
	 用户组列表(组级别越高代表组内用户等级越高)
</blockquote>
 <hr/>
<table class="table table-striped table-hover">
  	<thead>
	    <tr>
	      	<th>#</th>
	      	<th>用户组</th>
	      	<th>组级别</th>
	      	<th>组 升 级</th>
	      	<th>升级方式</th>
	      	<th>升级条件</th>
	      	<th>分佣</th>
	      	<th>分润</th>
	      	<th>注册时间</th>
	      	<th>操作</th>
	    </tr>
 	</thead>
  	<tbody>
  	@foreach ($list as $group)
	    <tr>
	      	<td>{{$group['group_id']}}</td>
	      	<td>{{$group['group_name']}}</td>
	      	<td>{{$group['group_salt']}} 级</td>
	      	<td>{{$group['group_level']=='1' ? '可以升级' :  '禁止升级'}}</td>
	      	<td>
	      	@if($group['group_level_type']=='-1')
	      		不限
	      	@elseif($group['group_level_type']=='1')
				总推荐人数
	      	@elseif($group['group_level_type']=='2')
	      		总刷卡额
	      	@elseif($group['group_level_type']=='3')
	      		付费升级
	      	@endif
	      	</td>
	      	<td>
	      	@if($group['group_level_type']=='1')
				总推荐人数>{{$group['group_level_invite']}} 人
	      	@elseif($group['group_level_type']=='2')
				总刷卡额>{{$group['group_level_transact']}} 元
	      	@elseif($group['group_level_type']=='3')
				付费金额>{{$group['group_level_money']}} 元
	      	@elseif($group['group_level_type']=='-1')
				推荐人数>{{$group['group_level_invite']}} 人| 刷卡额 > {{$group['group_level_transact']}} 元 | 付费金额>{{$group['group_level_money']}} 元
	      	@endif
	      	</td>
	      	<td>{{$group['group_cent']=='1' ? '允许' :  '不允许'}}</td>
	      	<td>{{$group['group_run']=='1' ? '允许' :  '不允许'}}</td>
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
	      	<td colspan="8">{!! $list->render() !!}</td>
          <td colspan="2" style="line-height: 55px">当前共<em style="font-size: 20px;color:red">{{$count}}</em>条记录</td>
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
