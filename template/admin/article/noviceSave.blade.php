 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>修改模块</h4></div>
        	 <div class="col-sm-4">
            	 <div class="text-right">
                	 <span class="label label-dot"></span>
                	 <span class="label label-dot label-primary"></span>
                	 <span class="label label-dot label-success"></span>
                	 <span class="label label-dot label-info"></span>
                	 <span class="label label-dot label-warning"></span>
                	 <span class="label label-dot label-danger"></span>
            	 </div>
        	 </div>
    	 </div>
 </div>

 <!--dialog Content-->
 <div class="modal-content animated fadeInLeft">
	 <form action="{{url('/index/article/noviceSave')}}" method="post" class="form-horizontal" id="myform">
	 <h2></h2>
	 <div class="row form-group">
		 <label for="generalize_title" class="col-sm-3 text-right"><b>模块名称:</b></label>
		 <div class="col-sm-6" id="generalize_title">
			 <input type="text" class="form-control generalize_title" name="novice_name" placeholder="请填写模块的名称" value="{{$info['novice_name']}}">
		 </div>		
	</div>
	<input type="hidden" class="form-control" name="novice_id" value="{{$info['novice_id']}}">
	
	 <div class="row form-group">
		 <label for="passageway_state" class="col-sm-3 text-right"><b>类型:</b></label>
		 <div id="passageway_state" class="col-sm-6">
			 <select name="novice_class" class="form-control">
			 	@foreach($noviceclass as $k => $v)
				 <option value="{{$v['novice_class_id']}}" @if($info['novice_class'] == $v['novice_class_id']) selected @endif>{{$v['novice_class_title']}}</option>
				
				@endforeach
			 </select>
		 </div>		
	 </div>
	<div class="row form-group">
		 <label for="data_text" class="col-sm-2 text-right"><b>文章内容:</b></label>
		 <div class="col-sm-6" id="data_text">
			 <textarea name="novice_contents" cols="30" id="content" class="form-control kindeditor" rows="15">{{$info->novice_contents}}</textarea>
		 </div>
	 </div>

	 <h2></h2>
	 </form>
</div>

 <!--dialog Button-->
 <div class="modal-footer animated fadeInLeft">
	 <button type="button" class="btn btn-primary save">保存</button>
      <button type="button" class="btn" data-dismiss="modal">关闭</button>
 </div>

 <script>

 //上传文件设置
 $('#uploaderExample3').uploader({
      url: "{{url('/index/Tool/upload_one')}}",
	 file_data_name:'generalize',
	 filters:{ max_file_size: '10mb',},
	 limitFilesCount:1,
	 onFileUploaded(file, responseObject) {
	    	 attr=eval('('+responseObject.response+")");
	    	 console.log(attr);
	    	 // attr.code ?  bootbox.alert({ message: attr.msg, size: 'small' }): $(".generalize_thumb").val('attr.url');
	    	 var generalize_thumb = $(".generalize_thumb").val()
	    	 	$(".generalize_thumb").val(attr.url);
	    	 	bootbox.alert({ message: attr.msg, size: 'small' })
	 }
 });

  $(".save").click(function(){	
  	// console.log($('#content').val());return;
	if(!$(".generalize_title").val()){
		 $(".generalize_title").parent().addClass("has-error");
		 return;
	 }
	 $("#myform").submit()
 })
  
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

</script>