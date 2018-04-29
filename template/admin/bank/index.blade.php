@extends('admin/layout/layout_main')
@section('title','银行列表管理~')
@section('wrapper')
<style>
	 h4 > a,.pull-right > a{color:#145ccd;}
 </style>
 <blockquote> 银行列表管理</blockquote>
 <section>
 <hr/>
 <table class="table datatable">
      <thead>
           <tr>
                 <!-- 以下两列左侧固定 -->
                 <th>ID</th>
                 <th>银行名称</th>
                 <!-- 以下三列中间可滚动 -->
                 <th class="flex-col">logo</th> 
                 <th class="flex-col">状态</th>
                 <th class="flex-col">添加时间</th>
                 <!-- 以下列右侧固定 -->
                 <th>操作</th>
           </tr>
      </thead>
      <tbody>
           @foreach($bank_list as $list)
           <tr>
                 <td>{{$list->bank_id}}</td>
                 <td>{{$list->bank_name}}</td>
                 <td>
                 <img style="width: 120px" src="{{$list['bank_avatar']}}" alt="{{$list['bank_name']}}"  class="img-responsive" data-toggle="lightbox">
                </td>
                 <td>
                 {{$list['bank_state'] ? '启用' : '禁用'}}
                 </td>
                 <td>{{$list->bank_add_time}}</td>
                 <td>
                   <button type="button" data-toggle="modal" data-remote="/index/bank/creat/bank_id/{{$list['bank_id']}}" class="btn btn-default btn-sm">编辑</button>
                   <a class="remove" href="#" data-url="{{url('/index/Bank/bankRemove/id/'.$list['bank_id'])}}"><i class="icon-remove"></i> 删除</a>
                </td>
           </tr>
           @endforeach
      </tbody>
 </table>



 {!! $bank_list->render() !!}
 </section>
 <script type="text/javascript">
 $(document).ready(function(){
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.bank_list').addClass('active');
    	 $('.menu .nav li.bank-manager').addClass('show');

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
