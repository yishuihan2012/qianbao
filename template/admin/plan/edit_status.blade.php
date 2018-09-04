 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>修改状态</h4></div>
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
	 <form action="{{url('/index/Plan/edit_status')}}" method="post" class="form-group" id="myform">
	 <input type="hidden" name="id" value="">


	 <div class="help-block"><code>(订单状态)</code></div>
	 <div style="margin-bottom: 5px">
	 <input type="radio" name="order_status" value="-1" style="margin-left:100px"checked="checked" @if($order_status==-1) checked @endif>失败
	  <input type="radio" name="order_status" value="1" style="margin-left:100px" @if($order_status==1) checked @endif>待执行
	  <input type="radio" name="order_status" value="2" style="margin-left:100px" @if($order_status==2) checked @endif>成功
	  <input type="radio" name="order_status" value="3" style="margin-left:100px" @if($order_status==3) checked @endif>取消
	  <input type="radio" name="order_status" value="4" style="margin-left:100px" @if($order_status==4) checked @endif>待查证
	  <input type="radio" name="order_status" value="5" style="margin-left:100px" @if($order_status==5) checked @endif>已执行(已处理)
	 </div>

	  <input type="hidden" name="id" value="{{$id}}">
 </form>
 <!--dialog Button-->
 <div class="modal-footer animated fadeInLeft">
	  <button type="button" class="btn btn-primary save" >修改状态</button>
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