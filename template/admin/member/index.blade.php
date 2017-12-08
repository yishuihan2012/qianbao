@extends('admin/layout/layout_main')
@section('title','会员列表管理~')  
@section('wrapper')

 <div class="row">
      @foreach ($member_list as $list)
      <div class="col-sm-3">
           <a class="card" href="###">
              <img src="{{$list->member_image}}" data-toggle="lightbox"  class="img-circle">
              <div class="card-heading"><strong>{{$list->member_nick}}({{$list->member_mobile}}){{state_preg($list->member_status,1,'实名')}}</strong></div>
              <div class="card-content text-muted">良辰美景奈何天，赏心乐事谁家院。</div>
              <div class="card-actions">
                <span style="font-size: 12px;">登录状态:</span> <code>{{$list->login_state}}</code>
                <div class="pull-right text-gray"><span style="font-size: 12px;">注册时间:</span> <code>{{$list->member_creat_time}}</code></div>
              </div>
           </a>
      </div>
      @endforeach
<<<<<<< HEAD
      <table class="table datatable">
           <thead>
                 <tr>
                 <!-- 以下两列左侧固定 -->
                      <th>#</th>
                      <th>昵称</th>
                      <th>手机号</th>
                      <th>商户号</th>
                      <!-- 以下三列中间可滚动 -->
                      <th class="flex-col">推荐码</th> 
                      <th class="flex-col">用户角色</th> 
                      <!-- 以下列右侧固定 -->
                      <th>状态</th>
                      <th>注册时间</th>
                      <th>操作</th>
                 </tr>
      </thead>
      <tbody>
           @foreach ($member_list as $list)
           <tr>
                 <td>{{$list->member_id}}</td>
                 <td><code>{{$list->member_nick}}</code> @if(!$list->member_cert)<i class="icon icon-flag text-success" title="已实名"></i> @endif</td>
                 <td>{{$list->member_mobile}}</td>
                 <td>{{$list->member_no}}</td>

                 <td>{{$list->member_code}}</td>
                 <td>{{$list->member_level_id}}</td>

                 <td>@if($list->member_state==1)正常@else封停@endif</td>
                 <td>{{$list->member_creat_time}}</td>
                 <td>
                      <div class="btn-group">
                           <button type="button" data-toggle="modal" data-remote="{{url('/index/member/info/id/'.$list->member_id)}}" class="btn btn-default btn-sm">详细信息</button>
                           <div class="btn-group">
                                 <button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown"><span class="caret"></span></button>
                                 <ul class="dropdown-menu" role="menu">
                                      <li><a href="#">封停用户</a></li>
                                      
                                 </ul>
                           </div>
                      </div>
                 </td>
           </tr>
           @endforeach
      </tbody>
</table>

</div>
=======
 </div>
>>>>>>> 82a1b26e20e8c7e5d8908512284896ae837292db
 <script type="text/javascript">
 $(document).ready(function(){
      $('table.datatable').datatable({sortable: true});
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.member').addClass('active');
    	 $('.menu .nav li.member-manager').addClass('show');

    	 $(".remove").click(function(){
    	 	 var url=$(this).attr('data-url');
		 bootbox.confirm({
		    title: "删除文章确认",
		    message: "确定删除这篇文章吗? 删除后不可恢复!",
		    buttons: {
		        cancel: {label: '<i class="fa fa-times"></i> 点错了'},
		        confirm: {label: '<i class="fa fa-check"></i> 确定'}
		    },
		    callback: function (result) {
		    	 if(result)
		    	 	window.location.href=url;
		    }
		 });
    	 })
 });
 </script>
 @endsection
