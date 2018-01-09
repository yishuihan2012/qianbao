
@extends('admin/layout/layout_main')
@section('title','分享链接列表~')
@section('wrapper')

 <style>
   h4 > a,.pull-right > a{color:#145ccd;}
 </style>

 <section>
 <hr/>
 <div class="list">
      <header><h3><i class="icon-list-ul"></i> 分享列表 <small>共 <strong class="text-danger">{{count($share)}}</strong> 条</small></h3></header>
      <div class="row">
           @foreach($share as $list)
           <div class="col-sm-2">
                 <a class="card" href="###">
                      <div id="myNiceCarousel_{{$list['share_id']}}" class="carousel slide" data-ride="carousel">
                           <!-- 轮播项目 -->
                           <div class="carousel-inner" style="height: 350px; min-height: 350px;">
                                 
                                 <div class="item  active "><img alt="First slide" src="{{$list['share_thumb']}}"  class="img-responsive"  data-toggle="lightbox"></div>
                               
                           </div>
                           <!-- 项目切换按钮 -->
                           <a class="left carousel-control" href="#myNiceCarousel_{{$list['share_id']}}" data-slide="prev"><span class="icon icon-chevron-left"></span></a>
                           <a class="right carousel-control" href="#myNiceCarousel_{{$list['share_id']}}" data-slide="next"><span class="icon icon-chevron-right"></span></a>
                      </div>
                      <div class="pull-right">
               <a href="{{url('/index/Generalize/shareedit/id/'.$list['share_id'])}}"><i class="icon-pencil"></i> 编辑</a> &nbsp;
               <a class="remove" href="#" data-url="{{url('/index/Generalize/shareRemove/id/'.$list['share_id'])}}"><i class="icon-remove"></i> 删除</a>
             </div>
                      <div class="card-heading"><strong>{{msubstr($list['share_title'],0,4)}}...</strong></div>
                     
                 </a>
           </div>
           @endforeach
      </div>
      {!! $share->render() !!}
</div>

</section>
<script type="text/javascript">
$(document).ready(function(){
       $('.menu .nav .active').removeClass('active');
       $('.menu .nav li.generalize_share').addClass('active');
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

<style>
	 h4 > a,.pull-right > a{color:#145ccd;}
</style>

<section>
<hr/>
<div class="list">
  <header>
    <h3><i class="icon-list-ul"></i> 分享链接列表 <small>共 <strong class="text-danger">{{count($share)}}</strong> 条</small></h3>
  </header>
  <div class="items items-hover">
      @foreach($share as $list)
      <div class="item">
      	 <div class="item-heading">
        		 <div class="pull-right">
        		 	 <!-- <a href="{{url('/index/Generalize/edit/id/')}}"><i class="icon-pencil"></i> 编辑</a> &nbsp; -->
        		 	 <a class="remove" href="#" data-url="{{url('/index/Generalize/shareRemove/id/'.$list['share_id'])}}"><i class="icon-remove"></i> 删除</a>
        		 </div>
        		 <h4><a href="{{url('/index/Generalize/edit/id/')}}">{{$list['share_title']}}
        		 </h4>
      	 </div>
     		 <div class="item-content">
     		 	 
     		 	 <div class="media pull-right text-right"><img src="{{$list['share_thumb']}}" alt="" style="width:70%"  data-toggle="lightbox"></div>
     		 	
      	 </div>
      	 <div class="item-footer">
        	 	 <a href="#" class="text-muted"><i class="icon-comments"></i> </a> &nbsp; <span class="text-muted">{{$list['share_time']}}</span>
      	 </div>
      </div>
    @endforeach 
  </div>
  {!! $share->render() !!}
</div>

</section>
<script type="text/javascript">
$(document).ready(function(){
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.generalize_share').addClass('active');
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
