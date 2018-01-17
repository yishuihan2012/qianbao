<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>平台福利列表</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet"/>
	</head>
	<body>
		<div class="mui-content wrap4">
			<ul class="notify-list">
			   <!--li class="space-up4">
			   	  <p class="fc"><span class="my-time-bg">2017-11-28 08:00</span></p>
			   	  <div class="wrap space-up2 f-br2">
			   	  	<p class="bg-red"></p>
			   	  	<div class="wrap bg-top dis-flex-fs">
			   	  		<div class="red-color f30 space-right2"><span class="f20">￥</span><span>2.56</span></div>
			   	  		<div class="wrap bor-left">
			   	  			<h4>邀请用户红包奖励</h4>
			   	  			<p class="f14 normal-color space-up f-tex-l"><span class="my-circle"></span>被邀请人:158****7546</p>
			   	  			<p class="f14 normal-color f-tex-l"><span class="my-circle"></span>发放时间:2017.11.28 28:25</p>
			   	  		</div>
			   	  	</div>
			   	  	<div class="bg-bot wrap red-color f16"><span class="my-red-circle"></span>该红包未领取，请尽快领取！</div>
			   	  </div>
			   </li--> 
			   @foreach ($data as $k=>$v)
			   <li class="space-up4">
			   	  <p class="fc"><span class="my-time-bg">{{$v['recomment_creat_time']}}</span></p>
			   	  <div class="wrap space-up2 f-br2">
			   	  	<p class="bg-gray"></p>
			   	  	<div class="wrap bg-top dis-flex-fs">
			   	  		<div class="red-color f30 space-right2"><span class="f20">￥</span><span>{{$v['recomment_money']}}</span></div>
			   	  		<div class="wrap bor-left">
			   	  			<h4>红包奖励</h4>
			   	  			<p class="f14 normal-color space-up f-tex-l"><span class="my-circle"></span>{{$v['recomment_desc']}}</p>
			   	  			<p class="f14 normal-color f-tex-l"><span class="my-circle"></span>发放时间:{{$v['recomment_creat_time']}}</p>
			   	  		</div>
			   	  	</div>
			   	  	<div class="bg-bot wrap red-color f16"><span class="my-red-circle"></span>红包已成功领取，并存入您的可提现余额中。</div>
			   	  </div>
			   </li> 
			   @endforeach
			</ul>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init();
		</script>
	</body>

</html>