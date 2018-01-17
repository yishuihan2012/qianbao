<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>动账交易列表</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet"/>
	</head>
	<body>
		<div class="mui-content wrap3">
			<ul class="deal-list">
				@foreach($data as $k=>$v)
					@if($v['type']=='MemberCash')
			   <li class="space-up4">
			   	  <p class="fc"><span class="my-time-bg">{{$v['cash_create_at']}}</span></p>
			   	  <div class="wrap3 bg-w space-up deal-list-wrap">
			   	  	<div>
				   	  	<p class="normal-color">套现交易成功提醒</p>
				   	  	<p class="normal-color">交易成功</p>
				   	  	<p class="normal-color fc">交易金额</p>
				   	  	<p class="normal-color fc f36">{{$v['cash_amount']}}<span class="f24">元</span></p>
			   	  	</div>
			   	  	<div class="space-up bor-top wrap5">
			   	  		<p class="normal-color"><span class="space-right2">交易时间</span><span>{{$v['cash_create_at']}}</span></p>
			   	  		<p class="normal-color"><span class="space-right2">实际到账</span><span>{{$v['cash_amount']-$v['service_charge']}}</span></p>
			   	  	</div>
			   	  </div>
			   </li>
					@elseif($v['type']=='Withdraw')
			   <li class="space-up4">
			   	  <p class="fc"><span class="my-time-bg">{{$v['withdraw_add_time']}}</span></p>
			   	  <div class="wrap3 bg-w space-up deal-list-wrap">
			   	  	<div>
				   	  	<p class="normal-color">提现交易成功提醒</p>
				   	  	<p class="normal-color">您尾号{{$v['withdraw_account']}}的银行卡交易成功</p>
				   	  	<p class="normal-color fc">交易金额</p>
				   	  	<p class="normal-color fc f36">{{$v['withdraw_amount']}}<span class="f24">元</span></p>
			   	  	</div>
			   	  	<div class="space-up bor-top wrap5">
			   	  		<p class="normal-color"><span class="space-right2">交易时间</span><span>{{$v['withdraw_add_time']}}</span></p>
			   	  		<p class="normal-color"><span class="space-right2">支付通道</span><span>快捷支付通道</span></p>
			   	  		<p class="normal-color"><span class="space-right2">实际到账</span><span>{{$v['withdraw_amount']-$v['withdraw_charge']}}元</span></p>
			   	  	</div>
			   	  </div>
			   </li>
			   		@else
			   <li class="space-up4">
			   	  <p class="fc"><span class="my-time-bg">{{$v['order_update_time']}}</span></p>
			   	  <div class="wrap3 bg-w space-up deal-list-wrap">
			   	  	<div>
				   	  	<p class="normal-color">收款交易成功提醒</p>
				   	  	<p class="normal-color">您尾号{{$v['order_creditcard']}}的{{$v['card_bankname']}}信用卡交易成功</p>
				   	  	<p class="normal-color fc">交易金额</p>
				   	  	<p class="normal-color fc f36">{{$v['order_charge']+$v['order_money']}}<span class="f24">元</span></p>
			   	  	</div>
			   	  	<div class="space-up bor-top wrap5">
			   	  		<p class="normal-color"><span class="space-right2">交易时间</span><span>{{$v['order_update_time']}}</span></p>
			   	  		<p class="normal-color"><span class="space-right2">支付通道</span><span>快捷支付通道</span></p>
			   	  		<p class="normal-color"><span class="space-right2">实际到账</span><span>{{$v['order_money']}}元</span></p>
			   	  	</div>
			   	  </div>
			   </li>
					@endif
				@endforeach
			</ul>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init();
		</script>
	</body>

</html>