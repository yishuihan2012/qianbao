 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>新增素材</h4></div>
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
	 <form action="{{url('/index/generalize/creat')}}" method="post" class="form-horizontal" id="myform">
	 <h2></h2>
	 <div class="row form-group">
		 <label for="generalize_title" class="col-sm-3 text-right"><b>素材名称:</b></label>
		 <div class="col-sm-6" id="generalize_title">
			 <input type="text" class="form-control generalize_title" name="generalize_title" placeholder="请填写素材的名称" value="">
		 </div>		
	 </div>

	<div class="row form-group">
		 <label for="passageway_desc" class="col-sm-3 text-right"><b>素材内容:</b></label>
		 <div class="col-sm-6" id="passageway_desc">
			 <textarea name="generalize_contents" class="form-control passageway_desc"></textarea>
		 </div>		
	 </div>

	 <div class="row form-group">
		 <label for="generalize_thumb" class="col-sm-3 text-right"><b>素材图片:</b></label>
		 <div id="generalize_thumb" class="col-sm-6">
			 <div id='uploaderExample3' class="uploader">
			 	 <div class="uploader-message text-center">
			    	 	 <div class="content"></div>
			    		 <button type="button" class="close">×</button>
			  	 </div>
			  	 <div class="uploader-files file-list file-list-grid"></div>
			 	 <div>
			 	 	 <hr class="divider">
			 	 	 <div class="uploader-status pull-right text-muted"></div>
			 	 	 <button type="button" class="btn btn-link uploader-btn-browse"><i class="icon icon-plus"></i> 选择文件</button>
			 	 	 <button type="button" class="btn btn-link uploader-btn-start"><i class="icon icon-cloud-upload"></i> 开始上传</button>
			 	 </div>
			 </div>
			 <input type="hidden" class="form-control generalize_thumb" name="generalize_thumb" value="">
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
	 limitFilesCount:3,
	 onFileUploaded(file, responseObject) {

	    	 attr=eval('('+responseObject.response+")");
	    	 console.log(attr);
	    	 // attr.code ?  bootbox.alert({ message: attr.msg, size: 'small' }): $(".generalize_thumb").val('attr.url');
	    	 var generalize_thumb = $(".generalize_thumb").val()
	    	 if( generalize_thumb == ''){
	    	 	$(".generalize_thumb").val(attr.url);
	    	 	bootbox.alert({ message: attr.msg, size: 'small' })
	    	 }else{
	    	 	$(".generalize_thumb").val(generalize_thumb+"#"+attr.url);
	    	 	bootbox.alert({ message: attr.msg, size: 'small' })
	    	 }
	 }

 });

  $(".save").click(function(){	
	if(!$(".generalize_title").val()){
		 $(".generalize_title").parent().addClass("has-error");
		 return;
	 }
	 $("#myform").submit()
 })
</script>