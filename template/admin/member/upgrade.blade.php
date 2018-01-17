 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>会员升级</h4></div>
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
	 <form action="{{url('/index/Member/upgrade')}}" method="post" class="form-group" id="myform">
	 <input type="hidden" name="id" value="">
	 <div class="help-block"><code>(是否分佣)</code></div>
	 <div style="margin-bottom: 5px">
	
	 <input type="radio" name="status" value="0" style="margin-left:100px" checked>否
	  <input type="radio" name="status" value="1" style="margin-left:100px">是
	  <input type="hidden" name="member_id" value="{{$id}}">
	
 </div>
<div class="input-group"  style="margin-top:10px;">
			<span class="input-group-addon">用户分组</span>
			
				<select class="form-control" name="member_group_id" style="width:200px">
					@foreach($member_group_info as $k => $v)
				  	    <option value="{{$v['group_id']}}" @if($member_group_id==$v['group_id']) selected="" @endif>{{$v['group_name']}}</option>
				  	@endforeach
				</select>
			
</div>
 </form>
 <!--dialog Button-->
 <div class="modal-footer animated fadeInLeft">
	  <button type="button" class="btn btn-primary save" >升级会员</button>
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