 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>新增费率编码</h4></div>
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
	 <form action="{{url('/index/passageway_rate/creat')}}" method="post" class="form-horizontal" id="myform">
	 <h2></h2>
	 <div class="row form-group">
		 <label for="passageway_name" class="col-sm-3 text-right"><b>费率:</b></label>
		 <div class="col-sm-6" id="passageway_name">
			 <input type="text" class="form-control passageway_name" name="rate_rate" placeholder="请填写通道的名称" value="">
		 </div>		
	 </div>
	 <div class="row form-group">
		 <label for="passageway_true_name" class="col-sm-3 text-right"><b>荣邦平台费率套餐代码(又叫邀请码):</b></label>
		 <div class="col-sm-6" id="passageway_true_name">
			 <input type="text" class="form-control passageway_true_name" name="rate_code" placeholder="APP不显示，荣邦平台费率套餐代码(又叫邀请码)" value="">
		 </div>		
	 </div>
	  <div class="row form-group">
		 <label for="passageway_name" class="col-sm-3 text-right"><b>固定附加费用:</b></label>
		 <div class="col-sm-6" id="passageway_name">
			 <input type="text" class="form-control passageway_name" name="rate_charge" placeholder="请填写固定附加费用" value="">
		 </div>		
	 </div>
	 <div class="row form-group">
		 <label for="passageway_status" class="col-sm-3 text-right"><b>通道名称:</b></label>
		 <div id="passageway_status" class="col-sm-6">
			 <select name="rate_passway_id" class="form-control passageway_status">
			 	@foreach($passageway as $k => $v)
				 <option value="{{$v['passageway_id']}}">{{$v['passageway_name']}}</option>
				@endforeach
				
			 </select>
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
 $(".save").click(function(){	
	if(!$(".passageway_name").val()){
		 $(".passageway_name").parent().addClass("has-error");
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