@extends('admin/layout/layout_main')
@section('title','新增新手指引管理~')
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
	 <form action="{{url('index/article/noviceCreat')}}" method="post" class="form-horizontal" id="myform">
		 <div class="row form-group">
			<label for="article_title" class="col-sm-2 text-right"><b>文章标题:</b></label>
			<div id="article_title" class="col-sm-6"><input type="text" class="form-control article_title" name="novice_name" placeholder="新手标题" value="{{ $article['article_title'] or ''}}"></div>
		 </div>
		 <div class="row form-group">
			 <label for="article_show" class="col-sm-2 text-right"><b>内容类型:</b></label>
			 <div class="col-sm-6" id="article_show">
				 <ul class="dowebok tags">
				 <select name="novice_class" class="form-control">
				@foreach($noviceclass as $k => $v)
				  	<option value="{{$v->novice_class_id}}" >{{$v->novice_class_title}}</option>
				@endforeach
				 </select>
					 <!-- <li><input type="radio" name="novice_class" @if( !isset($article['article_show']) or $article['article_show']=='1') checked  @endif value="0" data-labelauty="收款" /></li> -->
				 </ul>
			 </div>
		 </div>
		 <div class="row form-group">
			 <label for="data_text" class="col-sm-2 text-right"><b>文章内容:</b></label>
			 <div class="col-sm-6" id="data_text">
				 <textarea name="novice_contents" cols="30" id="content" class="form-control kindeditor" rows="15"></textarea>
			 </div>
		 </div>
	</form>
</div>
<!--dialog Button-->
<div class="row">
	<div class="col-sm-4 text-right"><button type="submit" class="btn btn-primary save">保存</button></div>
	<div class="col-sm-4 text-left"><button type="button" class="btn goHistory">返回</button></div>
</div>
<!--Kingedit脚本-->
<script src="/static/js/jquery-labelauty.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('.menu .nav .active').removeClass('active');
     $('.menu .nav li.new_zhiyin').addClass('active');
    $('.menu .nav li.article-manager').addClass('show');
    //获取二级分类
	$(".save").click(function(){
		// alert('123');
/*		if(!$(".article_title").val()){
			$(".article_title").parent().addClass("has-error");
			return false;
		}*/
		html = editor.html();
		// 同步数据后可以直接取得textarea的value
		editor.sync();
		html = document.getElementById('content').value; // 原生API
		html = KindEditor('#content').val(); // KindEditor Node API
		html = $('#content').val(); // jQuery
		editor.html(html);
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
	      filterMode : true,
           uploadJson : "{{url('/index/Tool/KindEditor_upload')}}",
           fileManagerJson : "{{url('/index/Tool/KindEditor_upload')}}",
           allowFileManager : true
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