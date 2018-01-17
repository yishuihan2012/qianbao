 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>分佣分润</h4></div>
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
    	 <div class="help-block"><code></code></div>
 </div>

 <!--dialog Content-->
 <div class="modal-content animated fadeInLeft">
	 <form action="{{ url('/index/member/member_edit/id/')}}" method="post" class="form-group" id="myform">
	 <input type="hidden" name="id" value="">
	 <div class="help-block"><code>(基本信息)</code>
	 <div style="margin-bottom: 5px">
	 <table class="table table-bordered table-hover table-striped" style="width:60%;float: left;margin-bottom: 0;margin-left: 30px">
	 	<tr>
	 		<th>支付类型</th>
	 		<th>分佣/分润金额</th>
	 		<th>分佣分润状态</th>
	 		<th>简介</th>
	 		<th>添加时间</th>
	 	</tr>

	 	@foreach($list as $k => $v)
		 <tr>
			 <td>@if($v['commission_type'] ==1) 支付分润 
			 	@elseif($v['commission_type'] == 2) 分佣 @else 代还分润 @endif
			 </td>
			 <td>{{$v->commission_money}}</td>
			 <td>@if($v->commission_state == 1 ) 正常 @else 异常许审核 @endif</td>
			  <td>{{$v->commission_desc}}</td>
			  <td>{{$v->commission_creat_time}}</td>
		 </tr>
		@endforeach

	 </table>
	 <img src=""  data-toggle="lightbox" style="width: 25%">
	 </div>
	 
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
	 	$('.toexamine').click(function(){
 		var url=$(this).attr('data-url');
		 bootbox.confirm({
		    title: "审核用户",
		    message: "确定操作吗？",
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