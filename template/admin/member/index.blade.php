@extends('admin/layout/layout_main')
@section('title','会员列表管理~')  
@section('wrapper')

 <div class="row">
      @foreach ($member_list as $list)
      <div class="col-sm-3">
           <a class="card" href="###" class="btn btn-default btn-sm">
              <img src="{{$list->member_image}}" data-toggle="lightbox"  class="img-circle">
              <div class="card-heading"><strong>{{$list->member_nick}}({{$list->member_mobile}}){{state_preg($list->member_cert,1,'实名')}}</strong></div>
              <div class="card-content text-muted">良辰美景奈何天，赏心乐事谁家院。</div>


              <div class="pull-right text-gray">

                <button class="btn" data-toggle="modal" data-remote="{{url('/index/member/info/id/'.$list->member_id)}}"  type="button">查看详情</button>

              </div>


              <div class="card-actions">
                <span style="font-size: 12px;">会员等级:</span> <code>{{$list->group_name}}</code>
                <div class="pull-right text-gray"><button class="btn" type="button">升级会员</button></div>
              </div>


              <div class="card-actions">
                <span style="font-size: 12px;">登录状态:</span> <code>@if($list->login_state==1)正常@else封停@endif</code>
                <div class="pull-right text-gray"><span style="font-size: 12px;">注册时间:</span> <code>{{$list->member_creat_time}}</code></div>
              </div>
           </a>
      </div>
      @endforeach
</div>


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
