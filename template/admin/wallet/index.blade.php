@extends('admin/layout/layout_main')
@section('title','钱包列表管理~')
@section('wrapper')
 <header>
    <h3><i class="icon-list-ul"></i> 钱包列表 <small>共 <strong class="text-danger">{{$count}}</strong> 条</small>
     <i class="icon icon-yen"></i> 钱包余额 <small>共 <strong class="text-danger">{{$wallet_amount}}</strong> 元</small>
      <i class="icon icon-yen"></i> 总收益 <small>共 <strong class="text-danger">{{$wallet_total_revenue}}</strong> 元</small>
      <i class="icon icon-yen"></i> 分佣收益 <small>共 <strong class="text-danger">{{$wallet_commission}}</strong> 元</small>
      <i class="icon icon-yen"></i> 分润收益 <small>共 <strong class="text-danger">{{$wallet_fenrun}}</strong> 元</small></h3>
  </header>
 <div class="panel">
  	 <div class="panel-body">
  		<form action="" name="myform" class="form-group" method="get">
  			 <div class="input-group" style="width: 150px;float: left;margin-right: 20px;">
			    <span class="input-group-addon">会员名</span>
			    <input type="text" class="form-control" name="member_nick" value="{{$r['member_nick']}}" placeholder="会员名">
			  </div>

			  <div class="input-group" style="width: 200px;float: left;margin-right: 20px;">
			    <span class="input-group-addon">手机号</span>
			    <input type="text" class="form-control" name="member_mobile" value="{{$r['member_mobile']}}" placeholder="手机号">
			  </div>
			  <div class="input-group" style="width: 240px;float: left;margin-right: 10px;">
			    <span class="input-group-addon">身份号</span>
			    <input type="text" class="form-control" name="cert_member_idcard" value="{{$r['cert_member_idcard']}}" placeholder="身份号">
			  </div>
			  <div class="input-group" style="width: 150px;float: left;margin-right: 10px;">
			     <span class="input-group-addon">钱包状态</span>
			  <select name="wallet_state" class="form-control">
			    <option value="" >全部</option>
			    <option value="2" @if($r['wallet_state']==2) selected @endif>正常</option>
			    <option value="-2" @if($r['wallet_state']==2) selected @endif>冻结</option>
			  </select>
			 
			  </div>
			  <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
			     <span class="input-group-addon">会员级别</span>
			  <select name="member_group_id" class="form-control">
			      <option value="" @if ($r['member_group_id']=='') selected="" @endif>全部</option>
			    @foreach($member_group as $v)
			      <option value="{{$v['group_id']}}" @if ($r['member_group_id']==$v['group_id']) selected @endif>{{$v['group_name']}}</option>
			    @endforeach
			  </select>
			  </div>
      <div class="input-group" style="width: 360px;float: left; margin-right: 10px;">
        <span class="input-group-addon">创建时间</span>
        <input type="date" name="beginTime" style="width: 140px" class="form-control" value="{{$r['beginTime'] or ''}}" />
        <input type="date" name="endTime" style="width: 140px" class="form-control" value="{{$r['endTime'] or ''}}" /></div>

			  <button class="btn btn-primary" type="submit">搜索</button>
  <input type="hidden" name="is_export" class="is_export" value="0">
  <div class="input-group" style="width: 180px;float: left; margin-right: 10px;">
    <span class="input-group-addon">导出页码,10万/页</span>
    <input type="text" name="start_p" class="form-control start_p" value="">
  </div>
  <button class="btn btn-primary export" type="submit">导出</button>
		</form>
  	 </div>
 </div>

 <table class="table table-striped table-hover">
  	 <thead>
	      <tr>
	      	 <th>会员名</th>
	      	 <th>钱包余额</th>
	      	 <th>总收益</th>
	      	 <th>邀请收益</th>
	      	 <th>分佣收益</th>
	      	 <th>分润收益</th>
	      	 <th>钱包状态</th>
	      	 <th>更新时间</th>
	      	 <th>添加时间</th>
	      	 <th>操作</th>
	    	 </tr>
 	 </thead>
  	 <tbody>
  	 @foreach($list as $wallet)
	    	 <tr>
	      	 <td>{{$wallet->member_nick}}</td>
	      	 <td>{{$wallet->wallet_amount}}</td>
	      	 <td>{{$wallet->wallet_total_revenue}}</td>
	      	 <td>{{$wallet->wallet_invite}}</td>
	      	 <td>{{$wallet->wallet_commission}}</td>
	      	 <td>{{$wallet->wallet_fenrun}}</td>
	      	 <td><span class="label label-{{$wallet->wallet_state=='2' ? 'success' : 'danger'}}">{{$wallet->wallet_state=='2' ? '正常' : '冻结' }}</span></td>
	      	 <td>{{$wallet->wallet_update_time}}</td>
	      	 <td>{{$wallet->wallet_add_time}}</td>
	      	 <td>
	      		 <div class="btn-group">
	  				 <a  class="btn btn-sm"  href="{{url('/index/wallet_log/index/member/'.$wallet->member_mobile)}}">查看日志</a>
	  				 <div class="btn-group">
					      <button type="button" class="btn dropdown-toggle btn-sm" data-toggle="dropdown"><span class="caret"></span></button>
					    	 <ul class="dropdown-menu" role="menu">
					      	 @if($wallet->wallet_state=='2')
					      	 <li><a class="freezing" href="#" data-id="{{$wallet->wallet_id}}" explain="冻结此钱包">冻结</a></li>
					      	 @else
					      	 <li><a class="freezing" href="#" data-id="{{$wallet->wallet_id}}" explain="解冻此钱包">解冻</a></li>
					      	 @endif
					      </ul>
	  				 </div>
				 </div>
	      	 </td>
	      </tr>
	 @endforeach
  	 </tbody>
  	 <tfoot>
	    	 <tr>
	      	 <td colspan="8">{!! $list->render() !!}</td>
          		 
	    	 </tr>
  	 </tfoot>
 </table>
 <script type="text/javascript">
 $(document).ready(function(){
      $('.menu .nav .active').removeClass('active');
      $('.menu .nav li.wallet').addClass('active');
      $('.menu .nav li.wallet-manager').addClass('show');
      $(".freezing").click(function(){
    		 var id = $(this).attr('data-id');
    		 var explain = $(this).attr('explain');
		 bootbox.prompt({
		    	 title: "请输入要"+explain+"的原因",
		    	 inputType: 'text',
		    	 callback: function (result) {
		        	 if(result!=null){
			        	 $.ajax({
			        		 url : "{{url('/index/wallet/freezing')}}",
			        		 data : {id:id,wallet_desc:result},
			        		 type : 'POST',
			        		 dataType : 'Json',
			        		 success:function(data){
			    				 explain+=data ? '成功' : '失败';
			    				 type= data ? 'success' : 'error';
							 new $.zui.Messager(explain, { type: type, close: true, }).show();
							 window.location.reload();
			        		 }
			        	 })
		      	 }
		    	 }
		 });
      })
 });


$('.export').click(function(){
  $(".is_export").val(1);
  setTimeout(function(){
    $(".is_export").val(0);
  },100);
  var start_p=$('.start_p').val();
  var end_p=$('.end_p').val();
  if(start_p){
    var re=/^\d+$/;
    if(!re.test(start_p)){
      alert('导出页码请输入数字');
      return false;
    }
  }
  alert("数据量大的话请耐心等待不要重复点击导出\n单次最大10万条数据\n点击确定开始导出");
})
    
 </script>
 @endsection
