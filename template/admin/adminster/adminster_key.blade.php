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
	 <form action="{{url('/index/adminster/adminster_key')}}" method="post" class="form-horizontal" id="myform">
	 <h2></h2>
	 <div class="row form-group">
		 <label for="generalize_title" class="col-sm-3 text-right"><b>用户口令:</b></label>
		 <div class="col-sm-6" id="generalize_title">
			 <input type="text" class="form-control generalize_title" name="adminster_key" placeholder="请填写素材的名称" value="{{$adminster_key}}">
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
	if(!$(".generalize_title").val()){
		 $(".generalize_title").parent().addClass("has-error");
		 return;
	 }
	 $("#myform").submit()
 })
</script>