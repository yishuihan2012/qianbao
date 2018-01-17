@extends('admin/layout/layout_main')
@section('title','回复用户~')
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

<div class="row">
	 <form action="" method="post" class="form-horizontal" id="myform">


		 <div class="row form-group">
			 <label for="article_desc" class="col-sm-2 text-right"><b>反馈内容:</b></label>
			 <div class="col-sm-6" id="article_desc">
				 <textarea name="suggestion_info" cols="30" class="form-control" rows="7">{{ $data['suggestion_info'] or '反馈的内容'}}</textarea>
			 </div>
		 </div>

		 <div class="row form-group">
			 <label for="article_desc" class="col-sm-2 text-right"><b>回复内容:</b></label>
			 <div class="col-sm-6" id="article_desc">
				 <textarea name="return_info" cols="30" class="form-control" rows="7">{{ $data['return_info'] or '回复的内容'}}</textarea>
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
    $('.menu .nav li.suggestion_list').addClass('active');
    $('.menu .nav li.suggestion').addClass('show');
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
		// 同步数据后可以直接取得textarea的value
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