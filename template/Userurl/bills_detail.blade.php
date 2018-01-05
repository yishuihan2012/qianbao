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
				@if($wallet_log['log_relation_type']==1)
				@endif
			    <li class="mui-table-view-cell dis-flex-be">
			    	<p class="invalid-color">交易状态</p>
			    	<p>已成功</p>
			    </li>
			    <li class="mui-table-view-cell dis-flex-be">
			    	<p class="invalid-color">实际到账</p>
			    	<p>19998.56元</p>
			    </li>
			    <li class="mui-table-view-cell dis-flex-be">
			    	<p class="invalid-color">收款方式</p>
			    	<p>银联快捷</p>
			    </li>
			    <li class="mui-table-view-cell dis-flex-be bor-bot">
			    	<p class="invalid-color">支付通道</p>
			    	<p>银联支付通道一</p>
			    </li>
			    <li class="mui-table-view-cell dis-flex-be">
			    	<p class="invalid-color">付款信用卡</p>
			    	<p>工商银行(尾号2586)</p>
			    </li>
			    <li class="mui-table-view-cell dis-flex-be">
			    	<p class="invalid-color">到账储蓄卡</p>
			    	<p>建设银行（尾号7821）</p>
			    </li>
			    <li class="mui-table-view-cell dis-flex-be">
			    	<p class="invalid-color">交易时间</p>
			    	<p>2017-12-06 18:52:04</p>
			    </li>
			    <li class="mui-table-view-cell dis-flex-be">
			    	<p class="invalid-color">订单号</p>
			    	<p>2017120618520485462</p>
			    </li>
			    
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