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
	 <form action="{{url('/index/Passageway/cashout/id/'.$data['cashout_passageway_id'])}}" method="post" class="form-horizontal" id="myform">
		<input type="hidden" name="cashout_id" value="{{$data['cashout_id']}}">
	 <div style="margin-left: 100px; ">
		 
		 <!-- <div class="input-group"">
		 	<span class="input-group-addon">支付通道：</span>
		 	<input type="text" class="form-control" style="width: 200px;" disabled="" value="{{$data['passageway_name']}}">
		 </div>	 -->
		 <div class="switch switch-inline">
		  <input type="checkbox" name="cashout_open" value="1" @if ($data['cashout_open']==1 ) checked @endif>
		  <label>是否开启</label>
		</div>
        <div class="input-group switch-inline" style="margin-top:10px;">
          <span class="input-group-addon">最小提现额度</span>
          <input type="text" class="form-control" style="width: 200px;" name="cashout_min" value="{{$data['cashout_min']}}">
        </div>
        <div class="input-group" style="margin-top:10px;">
          <span class="input-group-addon">最大提现额度</span>
          <input type="text" class="form-control" style="width: 200px;" name="cashout_max" value="{{$data['cashout_max']}}">
        </div>
        <div class="input-group switch-inline" style="margin-top:10px;">
          <span class="input-group-addon">通道开启时间</span>
          <input type="time" class="form-control" style="width: 200px;" name="cashout_begintime" value="{{$data['cashout_begintime']}}">
        </div>
        <div class="input-group" style="margin-top:10px;">
          <span class="input-group-addon">通道关闭时间</span>
          <input type="time" class="form-control" style="width: 200px;" name="cashout_endtime" value="{{$data['cashout_endtime']}}">
        </div>

		<div class="input-group" style="margin-top:10px;">
		  <span class="input-group-addon">提现调用类</span>
		  <input type="text" class="form-control" style="width: 200px;" name="cashout_action" value="{{$data['cashout_action']}}">
		</div>

		<div class="input-group" style="margin-top:10px;">
		  <span class="input-group-addon">提现调用方法</span>
		  <input type="text" class="form-control" style="width: 200px;" name="cashout_method" value="{{$data['cashout_method']}}">
		</div>

		<div class="input-group" style="margin-top:10px;">
		  <span class="input-group-addon">提现调用url</span>
		  <input type="text" class="form-control" style="width: 200px;" name="cashout_url" value="{{$data['cashout_url']}}">
		</div>
		<div class="input-group" style="margin-top:10px;">
		  <span class="input-group-addon">回调地址</span>
		  <input type="text" class="form-control" style="width: 200px;" name="cashout_callback" value="{{$data['cashout_callback']}}">
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