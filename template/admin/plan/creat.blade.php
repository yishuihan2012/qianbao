 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>新增通道</h4></div>
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
	 <form action="{{url('/index/plan/creat')}}" method="post" class="form-horizontal" id="myform">
	 <h2></h2>
	 <div class="row form-group">
		 <label for="passageway_name" class="col-sm-3 text-right"><b>计划ID:</b></label>
		 <div class="col-sm-6" id="passageway_name">
			 <input type="text" class="form-control order_id" name="order_id" placeholder="请填写通道的名称" value="">
		 </div>		
	 </div>
	 <div class="row form-group">
		 <label for="passageway_true_name" class="col-sm-3 text-right"><b>金额:</b></label>
		 <div class="col-sm-6" id="passageway_true_name">
			 <input type="text" class="form-control money" name="money" placeholder="APP不显示，用于后台确认是哪个通道" value="">
		 </div>		
	 </div>
	 </form>
</div>

 <!--dialog Button-->
 <div class="modal-footer animated fadeInLeft">
	 <button type="button" class="btn btn-primary save">保存</button>
      <button type="button" class="btn" data-dismiss="modal">关闭</button>
 </div>

 <script>

 $(".save").click(function(){	
 	alert();
		if(!$(".order_id").val()){
			 $(".order_id").parent().addClass("has-error");
			 return;
		 }

	 	if(!$(".money").val()){
		 	$(".money").parent().addClass("has-error");
			 return;
		 }


	 $("#myform").submit()
 })
 //上传文件设置
 $('#uploaderExample3').uploader({
      url: "{{url('/index/Tool/upload_one')}}",
	 file_data_name:'bank',
	 filters:{ max_file_size: '10mb',},
	 limitFilesCount:1,
	 onFileUploaded(file, responseObject) {
	    	 var attr=eval('('+responseObject.response+")");
	    	 attr.code ? $("input[name=passageway_avatar]").val(attr.url) : bootbox.alert({ message: attr.msg, size: 'small' });
	 }
 });
</script>