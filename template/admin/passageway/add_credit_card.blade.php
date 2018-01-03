 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>修改通道</h4></div>
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
	 <form action="{{url('/index/passageway/add_credit_card')}}" method="post" class="form-horizontal" id="myform">
	 <h2></h2>
	 <div class="row form-group">
		 <label for="passageway_name" class="col-sm-3 text-right"><b>信用卡名称:</b></label>
		 <div class="col-sm-6" id="passageway_name">
			 <input type="text" class="form-control passageway_name" name="card_name" placeholder="请填信用卡名称" value="">
		 </div>		
	 </div>

	

	 <div class="row form-group">
		 <label for="passageway_no" class="col-sm-3 text-right"><b>单笔交易金额:</b></label>
		 <div class="col-sm-6" id="passageway_no">
			 <input type="text" class="form-control passageway_no" placeholder="单笔交易金额" value="" name="bank_single" >
		 </div>		
	 </div>

	 <div class="row form-group">
		 <label for="passageway_method" class="col-sm-3 text-right"><b>单日交易金额:</b></label>
		 <div class="col-sm-6" id="passageway_method">
			 <input type="text" class="form-control passageway_method"  placeholder="请填单日交易金额" name="bank_one_day" value="">
		 </div>		
	 </div>

	 <div class="row form-group">
		 <label for="passageway_mech" class="col-sm-3 text-right"><b>金额属性:</b></label>
		 <div class="col-sm-6" id="passageway_mech">
			 <input type="text" class="form-control passageway_mech" name="bank_attrbute" placeholder="请填金额属性" value="">
			  <input type="hidden" class="form-control passageway_mech" name="bank_passageway_id" value="{{$passageway_id}}">
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
	if(!$(".passageway_name").val()){
		 $(".passageway_name").parent().addClass("has-error");
		 return;
	 }

	 if($(".passageway_status").val()==1){
	 	if(!$(".passageway_no").val()){
		 	$(".passageway_status").parent().addClass("has-error");
		 	$(".passageway_no").parent().addClass("has-error");
			 return;
		 }
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