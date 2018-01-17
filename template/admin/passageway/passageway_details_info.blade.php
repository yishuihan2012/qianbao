 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>订单详情</h4></div>
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
 </div>

 <!--dialog Content-->
 <div class="modal-content animated fadeInLeft">
	 <form action="" method="post" class="form-group" id="myform">
	 <input type="hidden" name="id" value="">
	 <div class="help-block"><code>(订单信息)</code></div>
	 <table class="table table-bordered table-hover table-striped" style="width:90%; margin:0 auto;">
		 <tr>
			 <td>订单编号</td>
			 <td>{{$order['order_no']}}</td>
			 <td>订单用户</td>
			 <td>{{$users[$order['order_member']]}}</td>
		 </tr>
		 <tr>
			 <td>交易金额</td>
			 <td><code>{{substr($order['order_money'],0,-2)}}</code> (元)</td>
			 <td>手续费</td>
			 <td><code>{{substr($order['order_charge'],0,-2)}}</code> (元)</td>
		 </tr>
		 <tr>
			 <td>信用卡号</td>
			 <td>{{$order['order_creditcard']}}</td>
			 <td>结算卡号</td>
			 <td>{{$order['order_card']}}</td>
		 </tr>
		 <tr>
			 <td>订单状态</td>
			 <td>{{$order['order_state']}}</td>
			 <td>交易时间</td>
			 <td>{{$order['order_update_time']}}</td>
		 </tr>
		  <tr>
		 	 <td>订单描述</td>
			 <td  colspan="3">{{$order['order_desc']}}</td> 
		 </tr>
	 </table>
	 <div class="help-block"><code>(分润信息)</code></div>
	 <table class="table table-bordered table-hover table-striped" style="width:90%; margin:0 auto;">
	 	@foreach($fenrun as $v)
		 <tr>
			 <td style="min-width: 70px">分润编号</td>
			 <td>{{$v['commission_id']}}</td>
			 <td>分润用户</td>
			 <td>{{$users[$v['commission_member_id']]}}</td>
		 </tr>
		 <tr>
			 <td>{{$v['level']}}分润</td>
			 <td>{{$v['commission_money']}}</td>
		 	 <td >分润状态</td>
		 	  <td>{{$v['commission_state']==1 ? '正常' : '异常'}}</td>
		 </tr>
		  <tr>
		 	 <td>分润简介</td>
			 <td  colspan="3">{{$v['commission_desc']}}</td> 
		 </tr>
		 @endforeach
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
	 //移除背景多余遮罩
	 $('.backdrop').click(function(){
	 	$('.modal-backdrop').remove();
	 })
	 $(function(){
	 })
 </script>