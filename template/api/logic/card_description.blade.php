<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>信用卡说明</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
	</head>
	<body>
		<div class="mui-content card-desc wrap">
			<h4 class="f14 f-normal space-bot">快捷支付目前支持以下银行及卡种，各行限额情况如下表</h4>
			<table class="f14">
			  <thead class="bg-table" align="center">
			    <tr>
			      <th>银行</th>
			      <th>卡种</th>
			      <th>单笔(元)</th>
			      <th>单日(元)</th>
			    </tr>
			  </thead>
			  <tbody  class="bg-w" align="center">
			  	@foreach($list as $key => $value)
			    <tr>
			      <td>{{$value->card_name}}</td>
			      <td >信用卡</td>
			      <td>{{$value->bank_single}}{{$value->bank_attrbute}}</td>
			      <td>{{$value->bank_one_day}}{{$value->bank_attrbute}}</td>
			    </tr>
			   @endforeach
			  </tbody>
			</table>
			<div class="space-up">
			  <p class="f15">说明：</p>
			  <p class="f15 normal-color">1.以上额度为银行支持的最高交易额，实际额度与您的商户等级相关</p>
			  <p class="f15 normal-color">2.如遇银行卡未开通认证请用电脑进入银联官网授权，地址：</p>
			  <p class="f15 f-tex-l"><a class="normal-color" href="https://www.95516.com/brstatic/union/pages/card/openFirst.html?entry=openPay">https://www.95516.com/brstatic/union/pages/card/openFirst.html?entry=openPay</a></p>
			</div>
		</div>
		<script src="/static//js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init()
		</script>
	</body>

</html>