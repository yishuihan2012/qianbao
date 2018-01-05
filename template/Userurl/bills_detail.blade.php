<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>账单详情</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet" />
	</head>
	<body>
		<div class="mui-content bills-detail">
			<div class="bg-w fc bills-detail-tit">
			  <p class="f14 space-bot">{{$wallet_log['log_wallet_type']==1 ? '收款' : '出款'}}交易金额</p>
			  <p class="f24 f-bold">{{substr($wallet_log['log_wallet_amount'],0,-2)}}</p>
			</div>
			<ul class="mui-table-view">
				@if($wallet_log['log_relation_type']==1)//分润分佣
					
				@elseif($wallet_log['log_relation_type']==2)//提现
					@if($withdraw)
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">交易状态</p>
				    	<p>{{$withdraw['info']}}</p>
				    </li>
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">实际到账</p>
				    	<p>{{$withdraw['withdraw_amount']}}元</p>
				    </li>
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">收款方式</p>
				    	<p>{{$withdraw['withdraw_method']}}</p>
				    </li>
<!-- 				    <li class="mui-table-view-cell dis-flex-be bor-bot">
				    	<p class="invalid-color">支付通道</p>
				    	<p></p>
				    </li>
 -->				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">交易时间</p>
				    	<p>{{$withdraw['withdraw_update_time']}}</p>
				    </li>
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">订单号</p>
				    	<p>{{$withdraw['withdraw_no']}}</p>
				    </li>
				    @endif
				@elseif($wallet_log['log_relation_type']==3)
				@elseif($wallet_log['log_relation_type']==4)
				@elseif($wallet_log['log_relation_type']==5)
				@elseif($wallet_log['log_relation_type']==6)
				@endif
<!-- 				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">付款信用卡</p>
				    	<p>工商银行(尾号2586)</p>
				    </li>
				    <li class="mui-table-view-cell dis-flex-be">
				    	<p class="invalid-color">到账储蓄卡</p>
				    	<p>建设银行（尾号7821）</p>
				    </li>
 -->			    
			    <!--对此账单有疑问？跳转至客服页面-->
			    <li class="mui-table-view-cell">
			    	<a class="mui-navigate-right" id="toConsult">对此账单有疑问?</a>
			    </li>
			</ul>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init();
			mui.ready(function(){
				document.getElementById('toConsult').addEventListener('tap',function(){
					mui.openWindow({
						url:'customer_service.html',
						id:'customer_service'
					});
				});
			});
		</script>
	</body>

</html>