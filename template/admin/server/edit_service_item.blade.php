 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>新增模块</h4></div>
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
	 <form action="{{url('/index/server_model/edit_model')}}" method="post" class="form-horizontal" id="myform">
	 <h2></h2>
	 <div class="row form-group">
		 <label for="generalize_title" class="col-sm-3 text-right"><b>模块名称:</b></label>
		 <div class="col-sm-6" id="generalize_title">
			 <input type="text" class="form-control generalize_title" name="item_name" placeholder="请填写模块的名称" value="{{$info['item_name']}}">
		 </div>		
	</div>
	<input type="hidden" class="form-control" name="item_id" value="{{$info['item_id']}}">
	<div class="row form-group">
		 <label for="generalize_title" class="col-sm-3 text-right"><b>排序</b></label>
		 <div class="col-sm-6" id="generalize_title">
			 <input type="text" class="form-control generalize_title" name="item_weight" placeholder="请填写排序号码" value="{{$info['item_weight']}}">
		 </div>		
	</div>
	 <div class="row form-group">
		 <label for="passageway_state" class="col-sm-3 text-right"><b>状态:</b></label>
		 <div id="passageway_state" class="col-sm-6">
			 <select name="item_state" class="form-control">
				 <option value="1" @if($info['item_state']==1) selected @endif>显示</option>
				 <option value="0" @if($info['item_state']==0) selected @endif>不显示</option>
			 </select>
		 </div>		
	 </div>
	<div class="row form-group">
		 <label for="generalize_thumb" class="col-sm-3 text-right"><b>模块图标:</b></label>
		 <div id="generalize_thumb" class="col-sm-6">
			 <div id='uploaderExample3' class="uploader">
			 	 <div class="uploader-message text-center">
			    	 	 <div class="content"></div>
			    		 <button type="button" class="close">×</button>
			  	 </div>
			  	 <div class="uploader-files file-list file-list-grid">
					<img src="{{$info['item_icon']}}">
			  	 </div>
			 	 <div>
			 	 	 <hr class="divider">
			 	 	 <div class="uploader-status pull-right text-muted"></div>
			 	 	 <button type="button" class="btn btn-link uploader-btn-browse"><i class="icon icon-plus"></i> 选择文件</button>
			 	 	 <button type="button" class="btn btn-link uploader-btn-start"><i class="icon icon-cloud-upload"></i> 开始上传</button>
			 	 </div>
			 </div>
			 <input type="hidden" class="form-control generalize_thumb" name="item_icon" value="{{$info['item_icon']}}">
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
	if(!$(".generalize_title").val()){
		 $(".generalize_title").parent().addClass("has-error");
		 return;
	 }
	 $("#myform").submit()
 })
</script>