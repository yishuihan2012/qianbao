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
			 <td>划款会员</td>
			 <td>{{$info['o_member_nick']}}</td>
		 </tr>
		 <tr>
			 <td>还款会员手机号</td>
			 <td>{{$info['o_member_mobile']}}</td>
		 </tr>
		 <tr>
			 <td>代还会员</td>
			 <td>{{$info['member_nick']}}</td>
		 </tr>
		 <tr>
			 <td>代还会员手机号</td>
			 <td>{{$info['member_mobile']}}</td>
		 </tr>
		  <tr>
			 <td>需还款信用卡</td>
			 <td>{{$info['generation_card']}}</td>
		 </tr>
		 <tr>
			 <td>需还款总额</td>
			 <td>{{$info['generation_total']}}</td>
		 </tr>
		 <tr>
			 <td>还款次数</td>
			 <td>{{$info['generation_count']}}</td>
		 </tr>
		 <tr>
			 <td>已还款总额</td>
			 <td>{{$info['generation_has']}}</td>
		 </tr>
		  <tr>
			 <td>剩余总额</td>
			 <td>{{$info['generation_left']}}</td>
		 </tr>
		 <tr>
			 <td>手续费</td>
			 <td>{{$info['generation_pound']}}</td>
		 </tr>
		 <tr>
			 <td>计划状态</td>
			 <td>@if($info['generation_state']==2) 还款中 @elseif($info['generation_state']==3)还款结束 @elseif($info['generation_state']==-1)还款失败 @endif</td>
		 </tr>
		  <tr>
			 <td>最近还款时间</td>
			 <td>{{$info['generation_edit_time']}}</td>
		 </tr>
		 <tr>
			 <td>订单类型</td>
			 <td>@if($info['order_type']==1) 消费  @else 还款 @endif</td>
		 </tr>
		 <tr>
			 <td>订单金额</td>
			 <td>{{$info['order_money']}}</td>
		 </tr>
		 <tr>
			 <td>订单手续费</td>
			 <td>{{$info['order_pound']}}</td>
		 </tr>
		  <tr>
			 <td>订单状态</td>
			 <td>@if($info['order_status']==1) 待执行 @elseif($info['generation_state']==-1)失败 @elseif($info['generation_state']==2)成功 @else 取消 @endif</td>
		 </tr>
		 <tr>
			 <td>订单描述</td>
			 <td>{{$info['order_desc']}}</td>
		 </tr>
		  <tr>
			 <td>执行时间</td>
			 <td>{{$info['order_time']}}</td>
		 </tr>
	 </table>
	 
	 
	 

	 </form>
 </div>

 <!--dialog Button-->
 <div class="modal-footer animated fadeInLeft">
 	
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
	 	$('.disables').click(function(){
 		var url=$(this).attr('data-url');
		 bootbox.confirm({
		    title: "封停用户",
		    message: "确定封停吗？",
		    buttons: {
		        cancel: {label: '<i class="fa fa-times"></i> 点错'},
		        confirm: {label: '<i class="fa fa-check"></i> 确定'}
		    },
		    callback: function (result) {
		    	 if(result)
		    	 	window.location.href=url;
		    }
		 });
	 	})
	 })
 </script>