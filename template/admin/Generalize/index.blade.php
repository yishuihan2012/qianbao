@extends('admin/layout/layout_main')
@section('title','素材列表管理~')
@section('wrapper')
 <style>
	 h4 > a,.pull-right > a{color:#145ccd;}
 </style>

 <section>
 <hr/>
 <div class="list">
      <header><h3><i class="icon-list-ul"></i> 素材列表 <small>共 <strong class="text-danger">{{$count}}</strong> 条</small></h3></header>
      <div class="row">
           @foreach($generalize as $list)
           <div class="col-sm-2">
                 <a class="card" href="###">
                      <div id="myNiceCarousel_{{$list['generalize_id']}}" class="carousel slide" data-ride="carousel">
                           <!-- 轮播项目 -->
                           <div class="carousel-inner" style="height: 350px; min-height: 350px;">
                                 {{-- */$i=0;/* --}}
                                 @foreach(explode("#",$list['generalize_thumb']) as $img)
                                 <div class="item @if($i==0) active @endif"><img alt="First slide" src="{{$img}}"  class="img-responsive"  data-toggle="lightbox"></div>
                                 {{-- */$i++;/*--}}
                                 @endforeach
                           </div>
                           <!-- 项目切换按钮 -->
                           <a class="left carousel-control" href="#myNiceCarousel_{{$list['generalize_id']}}" data-slide="prev"><span class="icon icon-chevron-left"></span></a>
                           <a class="right carousel-control" href="#myNiceCarousel_{{$list['generalize_id']}}" data-slide="next"><span class="icon icon-chevron-right"></span></a>
                      </div>
                      <div class="pull-right">
               <a href="{{url('/index/Generalize/edit/id/'.$list['generalize_id'])}}"><i class="icon-pencil"></i> 编辑</a> &nbsp;
               <a class="remove" href="#" data-url="{{url('/index/Generalize/remove/id/'.$list['generalize_id'])}}"><i class="icon-remove"></i> 删除</a>
             </div>
                      <div class="card-heading"><strong>{{$list['generalize_title']}}</strong></div>
                      <div class="card-content text-muted">{{msubstr($list['generalize_contents'],0,15)}}...</div>
                 </a>
           </div>
           @endforeach
      </div>
      {!! $generalize->render() !!}
</div>

</section>
<script type="text/javascript">
$(document).ready(function(){
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.generalize').addClass('active');
    	 $('.menu .nav li.generalize-manager').addClass('show');

    	 $(".parent li a").click(function(){
    	 	$("input[name='article_parent']").val($(this).attr('data-id'));
    	 	$("input[name='article_category']").val(0);
    	 	$("#myform").submit();
    	 })
    	 $(".son li a").click(function(){
    	 	$("input[name='article_category']").val($(this).attr('data-id'));
    	 	$("#myform").submit();
    	 })
    	 $(".remove").click(function(){
    	 	 var url=$(this).attr('data-url');
		 bootbox.confirm({
		    title: "删除推荐确认",
		    message: "确定删除这篇推荐吗? 删除后不可恢复!",
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
