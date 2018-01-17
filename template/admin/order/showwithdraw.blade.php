 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>订单信息展示</h4></div>
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
	 <form action="" method="post" class="form-group" id="myform">
	 <input type="hidden" name="id" value="{{$order_info->withdraw_id }}">
	 <div class="help-block"><code>(基本信息)</code></div>
	 <table class="table table-bordered table-hover table-striped" style="width:90%; margin:0 auto;">
		 <tr>
			 <th>订单号</th>	
			 <td>{{$order_info->withdraw_no}}</td>	
			 <th>用户</th>	
			 <td>{{$order_info->withdraw_name}}</td>	
<!-- 			 <th>用户头像</th>	
			 <th><img src="{{$order_info->member_image}}"  data-toggle="lightbox"></th>	
 -->		 </tr>	
 		<tr>
 			<th>收款方式</th>
			 <td>{{$order_info->withdraw_no}}</td>	
 			<th>收款账号</th>
			 <td>{{$order_info->withdraw_no}}</td>	
 		</tr>
 		<tr>
 			<th>操作金额</th>
			 <td>{{$order_info->withdraw_amount}}</td>	
 			<th>手续费</th>
			 <td>{{$order_info->withdraw_charge}}</td>	
 		</tr>
	 </table>
	 <div class="help-block"><code>(其他信息)</code></div>
	 <table class="table table-bordered table-hover table-striped" style="width:90%; margin:0 auto;">
		 <tr>
			 <th>订单创建时间</th>
			 <td>{{$order_info->withdraw_add_time}}</td>
			 <th>用户注册时间</th>
			 <td>{{$order_info->member_creat_time}}</td>
		 </tr>
	 </table>
	 </form>
 </div>

 <!--dialog Button-->
 <div class="modal-footer animated fadeInLeft">
	 <!-- <button type="button" class="btn btn-primary save">保存</button> -->
      <button type="button" class="btn" data-dismiss="modal">关闭</button>
 </div>
 <script>
	 $(".save").click(function(){
		 $("#myform").submit()
	 })
 </script>