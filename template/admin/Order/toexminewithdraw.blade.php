 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>审核</h4></div>
        	 <div class="col-sm-4">
            	 <div class="text-right">
	                 <span class="label label-dot label-primary"></span>
	                 <span class="label label-dot label-success"></span>
	                 <span class="label label-dot label-info"></span>
	                 <span class="label label-dot label-warning"></span>
	                 <span class="label label-dot label-danger"></span>
            	 </div>
        	 </div>
    	 </div>
    	 <div class="help-block"></div>
 </div>

 <!--dialog Content-->
 <div class="modal-content animated fadeInLeft">
	 <form action="{{url('/index/order/toexminewithdraw')}}" method="post" class="form-group" id="myform">
	 <input type="hidden" name="id" value="{{$id}}">
	 <table class="table table-bordered table-hover table-striped" style="width:90%; margin:0 auto;">
		 <tr>
			 <th>通过</th>	
			 <td><input type="radio" name="withdraw_total_money" checked value="12"></td>	
			 <th>未通过</th>	
			 <td><input type="radio" name="withdraw_total_money" class="not" value="-12"></td>	 	
		 </tr>	
	 </table>
	 <div class="help-block" style="display:none;"><code>(说明原因)</code>
		<textarea rows="3"></textarea>
	 </div>
	 </form>
 </div>

 <!--dialog Button-->
 <div class="modal-footer animated fadeInLeft">
	 <button type="button" class="btn btn-primary save">保存</button>
      <button type="button" class="btn" data-dismiss="modal">关闭</button>
 </div>
 <script>
 	 $(".not").click(function(){
 	 	$(".help-block").show();
 	 })
	 $(".save").click(function(){
		 $("#myform").submit()
	 })
 </script>