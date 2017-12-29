@extends('admin/layout/layout_main')
@section('title','用户反馈列表~')
@section('wrapper')
<style>
	 h4 > a,.pull-right > a{color:#145ccd;}
</style>
<section>
<hr/>
<div class="list">
  <header>
    <h3><i class="icon-list-ul"></i> 列表 <small>共 <strong class="text-danger">{{$count}}</strong> 条</small></h3>
  </header>
  <div class="items items-hover">
      @foreach($lists as $list)
      <div class="item">
      	 <div class="item-heading">
        		 <div class="pull-right">
        		 	 <a href="{{url('/index/Suggestion/edit/id/'.$list['suggestion_id'])}}"><i class="icon-pencil"></i> 编辑</a> &nbsp;
        		 	 <a class="remove" href="#" data-url="{{url('/index/Suggestion/remove/id/'.$list['suggestion_id'])}}"><i class="icon-remove"></i> 删除</a>
        		 </div>
        		 <h4><a href="{{url('/index/Suggestion/edit/id/'.$list['suggestion_id'])}}">
        		 </h4>
      	 </div>
     		 <div class="item-content">
        		 <div class="text">{{$list['suggestion_info']}}</div>
      	 </div>
      	 <div class="item-footer">
        	 	 <a href="#" class="text-muted"><i class="icon-comments"></i> {{$list['member_nick']}}</a> &nbsp; <span class="text-muted">{{$list['suggestion_creat_time']}}</span>
      	 </div>
      </div>
      @endforeach
  </div>
 {!! $lists->render() !!}
</div>

</section>
<script type="text/javascript">
$(document).ready(function(){
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.suggestion_list').addClass('active');
    	 $('.menu .nav li.suggestion').addClass('show');

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
		    title: "删除反馈内容确认",
		    message: "确定删除这篇反馈吗? 删除后不可恢复!",
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
