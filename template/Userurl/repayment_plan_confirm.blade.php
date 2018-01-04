<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>确认提交</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet" />
	</head>
	<body>
		<div class="mui-content">
			<div class="bg-blue wrap3">
				<p class="white-color">信用卡内最低可用余额(元)</p>
				<p class="f36 space-up2 white-color">{{$generationorder['order_money']}}</p>
			</div>
			<p class="space-up3 blue-color-th wrap">注：如果低于此余额还款计划将失败</p>
			<div class="bg-w dis-flex-be wrap conf-space-bot">
				<p>信用卡</p>
				<p>
					<span class="confirm-box">
						<!-- <img src="/static/images/card.png"></span> -->
						<img style="max-width: 50px" src="{{$creaditcard['card_bankicon']}}">
					</span>
					<span>{{$creaditcard['card_bankname']}} 尾号{{substr($creaditcard['card_bankno'],-4)}}</span>
				</p>
			</div>
			<a class="my-btn-blue2" id='sub'>确认提交</a>
		</div>
		<script src="/static/js/jquery-2.1.4.min.js"></script>
		<script src="/static/js/mui.min.js"></script>
		<script type="text/javascript">
			var res='';
			$(function(){
				mui(document).on('tap','#sub',function(){
					var url='/api/userurl/confirmPlan/uid/{{$uid}}/token/{{$token}}/id/{{$generationorder["order_no"]}}';
					$.post(url,'',function(res){
						res=JSON.parse(res);
						if(res.code==200){
							window.location='/api/userurl/repayment_plan_detail/uid/{{$uid}}/token/{{$token}}/order_no/{{$generationorder["order_no"]}}';
						}else{
							alert(res.msg);
						}
					})
				});
			})
		</script>
	</body>

</html>