@extends('admin/layout/layout_main')
@section('title','app弹窗列表')
@section('wrapper')
<div class="panel">
  	<div class="panel-body">
  		 <header>
   			 <h3><i class="icon-list-ul"></i>列表 <small>共 <strong class="text-danger">{{$data['count']}}</strong> 条</small>
     		</h3>
  		</header>
  	</div>
</div>

<table class="table table-striped table-hover">
  	<thead>
	    <tr>
	      	<th>ID</th>
	      	<th>图片</th>
	      	<th>跳转链接</th>
	      	<th>状态</th>
	      	<th>创建时间</th>
	      	<th>操作</th>
	    </tr>
 	</thead>
  	<tbody>
  	@foreach($list as $key => $val)
	    <tr>
	      	<td>{{$val->alert_id}}</td>
	      	<td><img src="{{$val->alert_img}}" height="50px"></td>
	      	<td>{{$val->alert_url}}</td>
	      	<td>{{$val->alert_status==1 ? '正常' : '禁用'}}</td>
	      	<td>{{$val->alert_createtime}}</td>
	      	<td>
                <a type="button" class="btn btn-sm" href="{{url('/index/alert/add/id/'.$val['alert_id'])}}" data-remote="" data-size="lg" data-toggle="modal">修改</a>
  				 | <a type="button" class="btn btn-sm" href="{{url('/index/alert/delete/id/'.$val['alert_id'])}}">删除</a>
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
    $('.menu .nav li.setting-Appversion').addClass('active');
    $('.menu .nav li.system-setting').addClass('show');
});
</script>
<!---->
@endsection
