 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>新增银行</h4></div>
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
	 <form action="{{url('bank/creat')}}" method="post" class="form-horizontal" id="myform">
	 <h2></h2>
	 <div class="row form-group">
		 <label for="bank_name" class="col-sm-3 text-right"><b>银行名称:</b></label>
		 <div class="col-sm-6" id="bank_name">
			 <input type="text" class="form-control bank_name" name="bank_name" placeholder="请填写银行的名称" value="">
		 </div>		
	 </div>

	 <div class="row form-group">
		 <label for="category_order" class="col-sm-3 text-right"><b>银行图标:</b></label>
		 <div id="category_order" class="col-sm-6">
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
			 <input type="hidden" class="form-control category_order" name="bank_avatar" value="">
		 </div>		
	 </div>

	 <div class="row form-group">
		 <label for="bank_state" class="col-sm-3 text-right"><b>银行状态:</b></label>
		 <div id="bank_state" class="col-sm-6">
			 <select name="bank_state" class="form-control">
				 <option value="1">正常</option>
				 <option value="0">停用</option>
			 </select>
		 </div>		
	 </div>
	 <div class="row form-group">
		 <label for="bank_state" class="col-sm-3 text-right"><b>首字母:</b></label>
		 <div id="bank_state" class="col-sm-6">
			 <select name="bank_letter" class="form-control">
				 <option value="A">A</option>
				 <option value="B">B</option>
				 <option value="C">C</option>
				 <option value="D">D</option>
				 <option value="E">E</option>
				 <option value="F">F</option>
				 <option value="G">G</option>
				 <option value="H">H</option>
				 <option value="I">I</option>
				 <option value="J">J</option>
				 <option value="K">K</option>
				 <option value="L">L</option>
				 <option value="M">M</option>
				 <option value="N">N</option>
				 <option value="O">O</option>
				 <option value="P">P</option>
				 <option value="Q">Q</option>
				 <option value="R">R</option>
				 <option value="S">S</option>
				 <option value="T">T</option>
				 <option value="U">U</option>
				 <option value="V">V</option>
				 <option value="W">W</option>
				 <option value="X">X</option>
				 <option value="Y">Y</option>
				 <option value="Z">Z</option>
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
	if(!$(".bank_name").val()){
		 $(".bank_name").parent().addClass("has-error");
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
	    	 attr.code ? $("input[name=bank_avatar]").val(attr.url) : bootbox.alert({ message: attr.msg, size: 'small' });
	 }
 });
</script>