@extends('admin/layout/layout_main')
@section('title','还款计划~')
@section('wrapper')
<style>
	.text-ellipsis{cursor: pointer;}
 
</style>
<div class="panel">
  	<div class="panel-body">
  		<form action="" name="myform" class="form-group" method="get">

   <form action="" method="post">
      <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
        <span class="input-group-addon">会员</span>
        <input type="text" class="form-control" name="member" value="{{$r['member'] or ''}}" placeholder="用户名/手机号"></div>
  <div class="input-group" style="width: 240px;float: left;margin-right: 10px;">
    <span class="input-group-addon">信用卡</span>
    <input type="text" class="form-control" name="generation_card" value="{{$r['generation_card']}}" placeholder="信用卡">
  </div>
   <div class="input-group" style="width: 140px;float: left;margin-right: 10px;">
    <span class="input-group-addon">计划ID</span>
    <input type="text" class="form-control" name="generation_id" value="{{$r['generation_id'] or ''}}" placeholder="计划ID">
  </div>
  <div class="input-group" style="width: 150px;float: left;margin-right: 10px;">
     <span class="input-group-addon">计划状态</span>
  <select name="generation_state" class="form-control">
    <option value="" >全部</option>
    <option value="2" @if($r['generation_state']==2) selected @endif>还款中</option>
    <option value="3" @if($r['generation_state']==3) selected @endif>还款结束</option>
    <!-- <option value="-1" @if($r['generation_state']==-1) selected @endif>还款失败</option> -->
    <option value="4" @if($r['generation_state']==4) selected @endif>取消</option>
  </select>
 
  </div>
  <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
     <span class="input-group-addon">会员级别</span>
  <select name="member_group_id" class="form-control member_group_id">
      <option value="" >全部</option>
      @foreach($member_group as $v)
        <option value="{{$v['group_id']}}">{{$v['group_name']}}</option>
      @endforeach
  </select>
  </div>
      <div class="input-group" style="width: 360px;float: left;margin-right: 10px;">
        <span class="input-group-addon">创建时间</span>
        <input type="date" name="beginTime" id="beginTime" value="{{$r['beginTime'] or ''}}" />
        <input type="date" name="endTime" id="endTime" value="{{$r['beginTime'] or ''}}" />
      </div>
    <button class="btn btn-primary" type="submit">搜索</button>
  <input type="hidden" name="is_export" class="is_export" value="0">
  <button class="btn btn-primary export" type="submit">导出</button>
</form>


		</form>
  	</div>
</div>
<div class="list">
  <header>
    <h3>
        <i class="icon-list-ul"></i> 订单列表 <small>共 <strong class="text-danger">{{$count}}</strong> 条</small>
          <i class="icon-list-ul"></i> 总笔数 <small>共 <strong class="text-danger">{{$count_plan}}</strong> 笔</small>
        <i class="icon icon-yen"></i> 还款总金额 <small>共 <strong class="text-danger">{{$sum}}</strong> 元</small>
        <i class="icon icon-yen"></i> 剩余还款总金额 <small>共 <strong class="text-danger">{{$surplussum}}</strong> 元</small>
    </h3>
  </header>

<table class="table table-striped table-hover">
  	<thead>
	    <tr>
          <th>ID</th>
	      	<th>还款会员</th>
	      	<th>还款会员手机号</th>
	      	<th>计划代号</th>
	      	<th>需还信用卡</th>
	      	<th>需还款总额</th>
	      	<th>还款次数</th>
	      	<th>已还款总额</th>
	      	<th>剩余总额</th>
	      	<th>手续费</th>
	      	<th>开始还款日期</th>
	      	<th>最后还款日期</th>
          <th>计划状态</th>
          <th>还款失败原因</th>
	      	<th>操作</th>
	      
	    </tr>
 	</thead>
  	<tbody>
  
	    
  	</tbody>
  	<tfoot>
  		@foreach($list as $k => $v)
	    <tr>
	    	  <td>{{$v['generation_id']}}</td>
	      	<td>{{$v['member_nick']}}</td>
	      	<td>{{$v['member_mobile']}}</td>
	      	<td>{{$v['generation_no']}}</td>
	      	<td>{{$v['generation_card']}}</td>
	      	<td>{{$v['generation_total']}}</td>
	      	<td>{{$v['generation_count']}}</td>
	      	<td>{{$v['generation_has']}}</td>
	      	<td>{{$v['generation_left']}}</td>
	      	<td>{{$v['generation_pound']}}</td>
	      	<td>{{$v['generation_start']}}</td>
          <td>{{$v['generation_end']}}</td>
	      	<td>@if($v['generation_state']==2) 还款中 @elseif($v['generation_state']==3)还款结束 @elseif($v['generation_state']==-1)还款失败 @else 取消 @endif</td>
	      	<td>{{$v['generation_desc']}}</td>
	      	<td><a class="btn btn-sm"  href="/index/Plan/detail?order_no={{$v['generation_id']}}" >查看订单</a></td>
	    </tr>
	    	@endforeach
  	</tfoot>
</table>
{!!$list->render()!!}
<script type="text/javascript">
$(document).ready(function(){
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.plan').addClass('active');
    $('.menu .nav li.plan-manager').addClass('show');
    $('.member_group_id').val({{$r['member_group_id'] or ''}})
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
  })
$('.export').click(function(){
  $(".is_export").val(1);
  setTimeout(function(){
    $(".is_export").val(0);
  },100);
})
</script>
<!---->
@endsection
