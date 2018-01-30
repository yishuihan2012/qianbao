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
			     <span class="input-group-addon">红包状态</span>
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

			<div class="input-group" style="width: 200px;float: left; margin-right: 10px;">
			    <input type="text" class="form-control date-picker" id="dateTimeRange" placeholder="红包生成时间"/>
			    <input type="hidden" name="beginTime" id="beginTime" value=""/>
			    <input type="hidden" name="endTime" id="endTime" value=""/>
			    <z class='clearTime'>X</z>
			</div>
			  <button class="btn btn-primary" type="submit">搜索</button>
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
	      	 <td>{{format_money($wallet->wallet_amount)}}</td>
	      	 <td>{{format_money($wallet->wallet_total_revenue)}}</td>
	      	 <td>{{format_money($wallet->wallet_invite)}}</td>
	      	 <td>{{format_money($wallet->wallet_commission)}}</td>
	      	 <td>{{format_money($wallet->wallet_fenrun)}}</td>
	      	 <td><span class="label label-{{$wallet->wallet_state=='2' ? 'success' : 'danger'}}">{{$wallet->wallet_state=='2' ? '正常' : '冻结' }}</span></td>
	      	 <td>{{$wallet->wallet_update_time}}</td>
	      	 <td>{{$wallet->wallet_add_time}}</td>
	      	 <td>
	      		 <div class="btn-group">
	  				 <a  class="btn btn-sm"  href="{{url('/index/wallet/look_log/id/'.$wallet->wallet_id)}}">查看日志</a>
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

$('#dateTimeRange').daterangepicker({
        applyClass : 'btn-sm btn-success',
        cancelClass : 'btn-sm btn-default',
        locale: {
            applyLabel: '确认',
            cancelLabel: '取消',
            fromLabel : '起始时间',
            toLabel : '结束时间',
            customRangeLabel : '自定义',
            firstDay : 1
        },
        ranges : {
            //'最近1小时': [moment().subtract('hours',1), moment()],
            '今日': [moment().startOf('day'), moment()],
            '昨日': [moment().subtract('days', 1).startOf('day'), moment().subtract('days', 1).endOf('day')],
            '最近7日': [moment().subtract('days', 6), moment()],
            '最近30日': [moment().subtract('days', 29), moment()],
            '本月': [moment().startOf("month"),moment().endOf("month")],
            '上个月': [moment().subtract(1,"month").startOf("month"),moment().subtract(1,"month").endOf("month")]
        },
        opens : 'left',    // 日期选择框的弹出位置
        separator : ' 至 ',
        showWeekNumbers : true,     // 是否显示第几周

 
        //timePicker: true,
        //timePickerIncrement : 10, // 时间的增量，单位为分钟
        //timePicker12Hour : false, // 是否使用12小时制来显示时间
 
         
        //maxDate : moment(),           // 最大时间
        format: 'YYYY-MM-DD'
 
    }, function(start, end, label) { // 格式化日期显示框
        $('#beginTime').val(start.format('YYYY-MM-DD'));
        $('#endTime').val(end.format('YYYY-MM-DD'));
    });
begin_end_time_clear();
$('.clearTime').click(begin_end_time_clear);
  //清除时间
    function begin_end_time_clear() {
        $('#dateTimeRange').val('');
        $('#beginTime').val('');
        $('#endTime').val('');
    }
 </script>
 <style type="text/css">
   .clearTime{
    position: absolute;
    right: 5px;
    top: 5px;
    z-index: 99;
    border: 1px solid;
    color: red;
    font-size: .6rem;
    padding: 0 5px;
   }
 </style>
 @endsection
