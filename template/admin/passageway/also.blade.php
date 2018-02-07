 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>通道提现设置</h4></div>
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
<style>
	.input-group[class*=col-]{float:left; margin-left: 20px;}
</style>
 <!--dialog Content-->
 <div class="modal-content animated fadeInLeft">
	 <form action="{{url('/index/Passageway/also/id/')}}" method="post" class="form-horizontal" id="myform">
		<input type="hidden" name="cashout_id" value="">
	 <div style="margin-left: 100px; ">
		 
		 <!-- <div class="input-group"">
		 	<span class="input-group-addon">支付通道：</span>
		 	<input type="text" class="form-control" style="width: 200px;" disabled="" value="}">
		 </div>	 -->
		 <div class="switch switch-inline">
		  <!-- <input type="checkbox" name="cashout_open" value="1">
		  <label>是否开启</label> -->
		</div>
		<input type="hidden" name="item_passageway" value="{{$item_passageway}}">
		<div class="input-group"  style="margin-top:10px;">
			<span class="input-group-addon">用户分组</span>
				<select class="form-control" name="item_group" style="width:200px">
					@foreach($member_group_info as $k => $v)
				  	    <option value="{{$v['group_id']}}" >{{$v['group_name']}}</option>
				  	@endforeach
				</select>
			
		</div>
		<div class="input-group" style="margin-top:10px;">
		  <span class="input-group-addon">快捷支付税率</span>
		  <input type="text" class="form-control" style="width: 200px;" name="item_rate" value="">
		</div>

		<div class="input-group" style="margin-top:10px;">
		  <span class="input-group-addon">代还费率</span>
		  <input type="text" class="form-control" style="width: 200px;" name="item_also" value="">
		</div>

		<div class="input-group" style="margin-top:10px;">
		  <span class="input-group-addon">固定收费单位分</span>
		  <input type="text" class="form-control" style="width: 200px;" name="item_charges" value="">
		</div>

		<div class="input-group" style="margin-top:10px;">
		  <span class="input-group-addon">封顶值</span>
		  <input type="text" class="form-control" style="width: 200px;" name="item_max" value="">
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
	 $("#myform").submit()
 })
</script>