@extends('admin/layout/layout_main')
@section('title','文章分类管理~')
@section('wrapper')
<style>
	 h4 > a,.pull-right > a{color:#145ccd;}
</style>
  
<section>
    
    <ul class="tree tree-menu" data-ride="tree">
       @foreach($tree as $trees)
      <li>
        <a style="float: right;margin-right: 20px;" class="remove" href="#" data-url="{{url('/index/article_category/remove/id/'.$trees['category_id'])}}"><i class="icon icon-trash"></i> 删除</a>
        <a style="float: right;margin-right: 20px;" href="{{url('/index/article_category/category_edit/id/'.$trees['category_id'])}}"><i class="icon-pencil"></i> 编辑</a>
        <a href="#">{{$trees['category_name']}}</a>

        @if(!empty($trees['category_parent']))
        <ul>
          @foreach($trees['category_parent'] as $val)
          <li>
            <a style="float: right;margin-right: 20px;" class="remove" href="#" data-url="{{url('/index/article_category/remove/id/'.$val['category_id'])}}"><i class="icon icon-trash"></i> 删除</a>
            <a style="float: right;margin-right: 20px;" href="{{url('/index/article_category/category_edit/id/'.$val['category_id'])}}"><i class="icon-pencil"></i> 编辑</a>
            <a href="#">{{$val['category_name']}}</a></li>
          @endforeach
        </ul>
        @endif
      </li>
      @endforeach
    </ul>











      <!-- <nav class="navbar navbar-default" role="navigation">
           <div class="container-fluid">
           <ul class="nav navbar-nav nav-justified">
                 @foreach($tree as $trees)
                 <li class="dropdown"><a href="#" @if(!empty($trees['category_parent']))  class="dropdown-toggle" data-toggle="dropdown" @endif>{{$trees['category_name']}}  <b class="icon icon-pencil"></b>  @if(!empty($trees['category_parent'])) <b class="caret"></b> @endif</a>
                      <ul class="dropdown-menu text-right" role="menu">
                           @if(!empty($trees['category_parent']))
                           @foreach($trees['category_parent'] as $son)
                            <li><a href="your/nice/url">{{$son['category_name']}} </a></li>
                           @endforeach
                           @endif
                      </ul>
                 </li>
                 @endforeach
           </ul>
           </div> 
      </nav> -->
</section>
<script type="text/javascript">
$(document).ready(function(){
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.articles_category').addClass('active');
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
		    title: "删除分类确认",
		    message: "确定删除这条分类吗? 删除后不可恢复!",
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
