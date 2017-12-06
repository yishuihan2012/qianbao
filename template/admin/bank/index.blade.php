@extends('admin/layout/layout_main')
@section('title','银行列表管理~')
@section('wrapper')
<style>
	 h4 > a,.pull-right > a{color:#145ccd;}
 </style>
 <blockquote> 银行列表管理</blockquote>
 <section>
 <hr/>
  <div class="row">
      @foreach($bank_list as $list)
      <div class="col-sm-2">
           <a class="card" href="###">
                 <img src="{{$list['bank_avatar']}}" alt="{{$list['bank_name']}}"  class="img-responsive" data-toggle="lightbox">
                 <div class="caption">{{$list['bank_name']}}  {{$list['bank_state'] ? '启用' : '禁用'}} </strong></div>
                 <div class="card-heading"><strong>{{$list['bank_name']}}<code>{{$list['bank_state'] ? '启用' : '禁用'}}</code></strong></div>
                 <div class="card-content text-muted"><div class="help-block">添加时间:<code>{{$list['bank_add_time']}}</code></div> </div>
           </a>
      </div>
      @endforeach
  </div>
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
