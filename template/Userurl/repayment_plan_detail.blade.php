<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>还款详情</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet"/>
	</head>
	<body>
		<div class="mui-content repayment-detail">
			<!--还款详情头部-->
			<div class="wrap bg-w">
			  <div class=" bor-bot" style="fl">
			    <div class="" style="width: 65vw;display: inline-block;">
			      <p class="invalid-color f16">还款总金额(含手续费{{$order_pound}})</p>
			      <p class="f24 space-up3 space-bot"><strong>{{$generation['generation_total']+$order_pound}}</strong></p>
			    </div>
			    <div class=" fc" style="width: 25vw;display: inline-block;">
			      <p class="invalid-color f16">消费笔数</p>
			      <p class="f24 space-up3 space-bot"><strong>{{$generation['generation_count']}}</strong></p>
			    </div>
			  </div>
			  <div>
				<p class="space-up2 f16">
				  <span class="invalid-color">还款日期为:</span>
				  <span class="blue-color-th">{{date('m月d日',strtotime($generation['generation_start']))}}-{{date('m月d日',strtotime($generation['generation_end']))}}</span>
				</p>
				<p class="invalid-color space-up3 f16">订单号：{{$generation['generation_no']}}</p>
				<p class="invalid-color space-up3 f16">银行卡：{{$generation['card_bankname']}}({{substr($generation['generation_card'], -4)}})</p>
			  </div>
			</div>
			<!--还款详情列表-->
			<ul style="margin-bottom: 100px;">

				@foreach($order as $key=>$list)
				<li class="bg-w wrap2 space-up2">
				<!-- 还款详情列表头 -->
					<div class="dis-flex-be wrap-bt bor-bot">
					  <p>
						<span class="iconfont icon-jihua blue-color-th f16"></span>
				        <span class="blue-color-th f14">{{$key}}</span>
				       </p>
				       <p class="invalid-color f-tex-l f14"><span>还款:{{$list['get']}}元</span><span class="space-lr">|</span><span>消费:{{$list['pay']}}元</span></p>
					</div>
					@foreach($list as $v)
						 
						@if($v['order_status']==1)
						<!-- 还款 -->
						<div class="dis-flex-be wrap-bt bor-bot">
							<p class="f15">
								@if($v['order_type']==1)
								<span class="my-badge-inpro">消费</span>
								@elseif($v['order_type']==2)
								<span class="my-badge-success">还款</span>
								@endif
								<span class="invalid-color space-lr2">{{date('H:i',strtotime($v['order_time']))}}</span>
								<span><strong>{{$v['order_money']}}元</strong></span>
							</p>
							<p class="f16 yellow-color">
							  <span class="">进行中</span>
							  <span class="iconfont icon-shijian-copy-copy f20 v-m"></span>
					        </p>
						</div>
						@elseif($v['order_status']==-1)
						<!-- 还款失败 -->
						<div class="dis-flex-be wrap-bt bor-bot">
							<p class="f15">
								@if($v['order_type']==1)
								<span class="my-badge-inpro">消费</span>
								@elseif($v['order_type']==2)
								<span class="my-badge-success">还款</span>
								@endif
								<span class="invalid-color space-lr2">{{date('H:i',strtotime($v['order_time']))}}</span>
								<span><strong>{{$v['order_money']}}元</strong></span>
							</p>
							<p class="f16 red-color">
							  <span class="">执行失败</span>
							  <span class="iconfont icon-zhifuyouwenti f20 v-m"></span>
					        </p>
 					</div>
						@elseif($v['order_status']==2)
						<!-- 取消还款 -->
						<div class="dis-flex-be wrap-bt bor-bot">
							<p class="f15">
								@if($v['order_type']==1)
								<span class="my-badge-inpro">消费</span>
								@elseif($v['order_type']==2)
								<span class="my-badge-success">还款</span>
								@endif
								<span class="invalid-color space-lr2">{{date('H:i',strtotime($v['order_time']))}}</span>
								<span><strong>{{$v['order_money']}}元</strong></span>
							</p>
							<p class="f16 green-color2">
							  <span>执行成功</span>
							  <span class="iconfont icon-successful f20 v-m"></span>
					        </p>
						</div>
						@elseif($v['order_status']==3)
						<div class="dis-flex-be wrap-bt bor-bot">
							<p class="f15">
								@if($v['order_type']==1)
								<span class="my-badge-inpro">消费</span>
								@elseif($v['order_type']==2)
								<span class="my-badge-success">还款</span>
								@endif
								<span class="invalid-color space-lr2">{{date('H:i',strtotime($v['order_time']))}}</span>
								<span><strong>{{$v['order_money']}}元</strong></span>
							</p>
							<p class="f16 red-color2">
							  <span>计划取消</span>
							  <span class="iconfont icon-quxiao f20 v-m"></span>
					        </p>
						</div>
						@endif
					@endforeach
					
				</li>
		@endforeach

			</ul>
		@if(in_array($generation['generation_state'],[1,2]))
	<a class="my-confirm" id="regBtn" style="position: fixed;bottom: 50px;">取消整体计划</a>
		@endif
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script src="/static/js/jquery-2.1.4.min.js"></script>
		<script type="text/javascript">
			mui.init();
	$(function(){
		$('#regBtn').click(function(){
			$.post('/api/userurl/cancel_repayment',{generation_id:{{$generation['generation_id']}}},function(res){
				res=JSON.parse(res);
				if(res.code==200){
					alert('取消成功！');
					location.reload();
				}else{
					alert("取消失败！\n"+res.msg);
				}
			})
			console.log(666);
		})
	})
		</script>
	</body>
<style type="text/css">
	.green{
		color: green;
	}
</style>
</html>