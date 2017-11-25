@extends('admin/layout/layout_main')
@section('title','文章列表管理~')
@section('wrapper')
<style>
	 h4 > a,.pull-right > a{color:#145ccd;}
</style>
<blockquote>
<form action="" name="myform" class="form-group" id="myform" method="get">
	 <ul class="nav nav-secondary parent"><!-- nav-justified 自适应宽度-->
    		 <li class="nav-heading">顶级分类</li>
    		 <li @if(!isset($where['article_parent']) or $where['article_parent']=='0') class="active" @endif><a data-id="0" href="#">全部</a></li>
		 @foreach($category_list as $category_lists)
		 <li @if(isset($where['article_parent']) && $where['article_parent']==$category_lists['category_id']) class="active" @endif><a data-id="{{$category_lists['category_id']}}" href="#">{{$category_lists['category_name']}}</a></li>
		 @endforeach
		 <input type="hidden" name="article_parent" value="{{$where['article_parent'] or 0}}">
  	 </ul>
	 <ul class="nav nav-secondary son"><!-- nav-justified 自适应宽度-->
    		 <li class="nav-heading">二级分类</li>
    		 <li @if(!isset($where['article_category']) or $where['article_category']=='0') class="active" @endif><a href="#" data-id="0">全部</a></li>
		 @foreach($son_category as $son)
		 <li @if(isset($where['article_category']) && $where['article_category']==$son['category_id']) class="active" @endif><a href="#" data-id="{{$son['category_id']}}">{{$son['category_name']}}</a></li>
		 @endforeach
		 <input type="hidden" name="article_category" value="{{$where['article_category'] or 0}}">
  	 </ul>
</form>
</blockquote>

<section>
<hr/>
<div class="list">
  <header>
    <h3><i class="icon-list-ul"></i> 文章列表 <small>共 <strong class="text-danger">{{$count['count_size']}}</strong> 条</small></h3>
  </header>
  <div class="items items-hover">
      @foreach($article_list as $list)
      <div class="item">
      	 <div class="item-heading">
        		 <div class="pull-right">
        		 	 <a href="{{url('/index/Article/edit/id/'.$list['article_id'])}}"><i class="icon-pencil"></i> 编辑</a> &nbsp;
        		 	 <a class="remove" href="#" data-url="{{url('/index/Article/remove/id/'.$list['article_id'])}}"><i class="icon-remove"></i> 删除</a>
        		 </div>
        		 <h4><a href="{{url('/index/Article/edit/id/'.$list['article_id'])}}">{{$list['article_title']}}
        		 @if($list['article_recommend']!='0')
        		 </a> <span class="label label-success">推荐</span>
        		 @endif
        		 @if($list['article_topper']!='0')
        		 <span class="label label-danger">置顶</span>
        		 @endif
        		 </h4>
      	 </div>
     		 <div class="item-content">
     		 	 @if($list['article_thumb']!='')
     		 	 <div class="media pull-right text-right"><img src="{{$list['article_thumb']}}" alt="{{$list['article_title']}}" style="width:70%"  data-toggle="lightbox"></div>
     		 	 @endif
        		 <div class="text">{{$list['article_desc']}}</div>
      	 </div>
      	 <div class="item-footer">
        	 	 <a href="#" class="text-muted"><i class="icon-comments"></i> {{$list['article_read']}}</a> &nbsp; <span class="text-muted">{{$list['article_add_time']}}</span>
      	 </div>
      </div>
      @endforeach
  </div>
 {!! $article_list->render() !!}
</div>

</section>
<script type="text/javascript">
$(document).ready(function(){
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.articles').addClass('active');
    	 $('.menu .nav li.article-manager').addClass('show');

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
