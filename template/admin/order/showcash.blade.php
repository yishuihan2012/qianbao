 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>还款计划详情</h4></div>
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
	 <form action=" " method="post" class="form-group" id="myform">
	 <input type="hidden" name="id" value="">
	
	 <div style="margin-bottom: 5px">
	 <table class="table table-bordered table-hover table-striped" style="width:60%;float: left;margin-bottom: 0;margin-left: 30px">
		 <tr>
			 <td>订单号</td>
			 <td>{{$info['order_no']}}</td>
		 </tr>
		 <tr>
			 <td>第三方订单编号</td>
			 <td>{{$info['order_thead_no']}}</td>
		 </tr>
		 <tr>
			 <td>会员名称</td>
			 <td>{{$info['member_nick']}}</td>
		 </tr>
		 <tr>
			 <td>会员手机号</td>
			 <td>{{$info['member_mobile']}}</td>
		 </tr>
		  <tr>
			 <td>交易通道</td>
			 <td>{{$info['passageway_name']}}</td>
		 </tr>
		 <tr>
			 <td>交易金额</td>
			 <td>{{$info['order_money']}}</td>
		 </tr>
		 <tr>
			 <td>手续费</td>
			 <td>{{$info['order_charge']}}</td>
		 </tr>
		 <tr>
			 <td>费率</td>
			 <td>{{$info['order_also']}}%</td>
		 </tr>
		  <tr>
			 <td>分润分佣消耗</td>
			 <td>{{$info['order_fen']}}</td>
		 </tr>
		 <tr>
			 <td>身份证号</td>
			 <td>{{$info['order_idcard']}}</td>
		 </tr>
		 <tr>
			 <td>信用卡号</td>
			 <td>{{$info['order_creditcard']}}</td>
		 </tr>
		  <tr>
			 <td>结算卡号</td>
			 <td>{{$info['order_card']}}</td>
		 </tr>
		 <tr>
			 <td>订单状态</td>
			 <td>@if($info['order_state']==1) 待支付  @elseif($info['order_state']==2) 成功 @elseif($info['order_state']==-1) 失败 @else 超时 @endif</td>
		 </tr>
		 <tr>
			 <td>订单描述</td>
			 <td>{{$info['order_desc']}}</td>
		 </tr>
		 <tr>
			 <td>更新时间</td>
			 <td>{{$info['order_update_time']}}</td>
		 </tr>
		  <tr>
			 <td>创建时间</td>
			 <td>{{$info['order_add_time']}}</td>
		 </tr>
		 
	 </table>
	 
	 
	 

	 </form>
 </div>

 <!--dialog Button-->
 <div class="modal-footer">
 	
 </div>
 <script>
	 //移除背景多余遮罩
	 $('.backdrop').click(function(){
	 	$('.modal-backdrop').remove();
	 })
 </script>