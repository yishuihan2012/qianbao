@extends('admin/layout/layout_main')
@section('title','新增文章~')
@section('wrapper')
<link rel="stylesheet" href="/static/css/jquery-labelauty.css">
<style>
	input.labelauty + label > span.labelauty-unchecked-image{background-image: url( /static/images/input-unchecked.png );}
	input.labelauty + label > span.labelauty-checked-image{background-image: url( /static/images/input-checked.png );}
	.dowebok {padding-left: 3px;}
	.dowebok ul { list-style-type: none;}
	.dowebok li { display: inline-block;}
	.dowebok li { margin: -3px 20px -10px 0px}
	.dowebok label{margin-bottom: 0}
	input.labelauty + label { font: 12px "Microsoft Yahei";}
</style>
@if(isset($errormsg))
<div class="alert alert-danger">
  	<div class="content">{{$errormsg}}</div>
</div>
@endif

<div class="row">
	 <form action="" method="post" class="form-horizontal" id="myform">


		 <div class="row form-group">
			 <label for="category_parent" class="col-sm-2 text-right"><b>顶级分类:</b></label>
			 <div class="col-sm-3" id="category_parent">
				 <select class="form-control" name="category_parent">
				 	<option value="0" selected>请选择</option>
					@foreach($category_list as $cat)
				  	<option @if(isset($category['article_category']) && $cat['category_id']==$category['category_parent']) selected @endif value="{{$cat['category_id']}}" >{{$cat['category_name']}}</option>
				  	@endforeach
				 </select>
			 </div>

		 </div>
		 <div class="row form-group">
			<label for="category_name" class="col-sm-2 text-right"><b>分类名称:</b></label>
			<div id="category_name" class="col-sm-6"><input type="text" class="form-control category_name" name="category_name" placeholder="分类的标题" value="{{ $category['category_name'] or ''}}"></div>
		 </div>

		
		 <div class="row form-group">
			 <label for="category_is" class="col-sm-2 text-right"><b>是否显示:</b></label>
			 <div class="col-sm-6" id="category_is">
				 <ul class="dowebok tags">
					 <li><input type="radio" name="category_is" @if( isset($category['category_is']) && $category['category_is']=='0') checked  @endif value="0" data-labelauty="不显示" /></li>
					 <li><input type="radio" name="category_is" @if( !isset($category['category_is']) or $category['category_is']=='1') checked  @endif value="1" data-labelauty="显示" /></li>
				 </ul>
			 </div>
		 </div>
		 
	</form>
</div>
<!--dialog Button-->
<div class="row">
	<div class="col-sm-4 text-right"><button type="button" class="btn btn-primary save">保存</button></div>
	<div class="col-sm-4 text-left"><button type="button" class="btn goHistory">返回</button></div>
</div>
<!--Kingedit脚本-->
<script src="/static/js/jquery-labelauty.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.newslist').addClass('active');
    $('.menu .nav li.articles-manager').addClass('show');
    //获取二级分类
	$("select[name='article_parent']").change(function(){
	      var id=$(this).val();
	      $("select[name='article_category'] option:not(:first)").remove();
	      if(id!=0){
		    	 $.post("{{url('/index/article/getCategory')}}",{id:id},function(data){
	                $.each(data,function(n,value) {
	                	$("select[name='article_category']").append("<option value='"+value.category_id+"'>"+value.category_name+"</option>");
	                });
			 },'json');
	      }
	})
	$(".save").click(function(){
		if(!$(".category_name").val()){
			$(".category_name").parent().addClass("has-error");
			return false;
		}
		// html = editor.html();
		// // 同步数据后可以直接取得textarea的value
		// editor.sync();
		// html = document.getElementById('content').value; // 原生API
		// html = KindEditor('#content').val(); // KindEditor Node API
		// html = $('#content').val(); // jQuery
		// editor.html(html);
		// alert(123)
		$("#myform").submit()
	});
	//上传文件设置
	 $('#uploaderExample3').uploader({
	    	 url: "{{url('/index/Tool/upload_one')}}",
	      file_data_name:'article',
		 filters:{
		    	 // 最大上传文件为 10MB
		    	 max_file_size: '10mb',
		 },
		 limitFilesCount:1,
	      onFileUploaded(file, responseObject) {
	    	 var attr=eval('('+responseObject.response+")");
	    		 attr.code ? $("input[name=article_thumb]").val(attr.url) : bootbox.alert({ message: attr.msg, size: 'small' });
	      }

	 });
	 //编辑器初始化
      KindEditor.ready(function(K) {
            window.editor = K.create('#editor_id');
      });
	 var options = {
	      filterMode : true
	 };
	 var editor = KindEditor.create('textarea[id="content"]', options);
	 $(".goHistory").click(function(){
	 	 window.history.go(-1);
	 })
 });
 $(function(){
      $(':input').labelauty();
 });
</script>
<!---->
@endsection