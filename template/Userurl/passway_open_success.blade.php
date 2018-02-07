<!doctype html>
<html>
	<head>
		<!-- 荣邦快捷支付开通 ishtml=2 时调用本页面 填入验证码调用确认接口 -->
		<meta charset="UTF-8">
		<title>快捷支付开通成功</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet"/>
	</head>
	<body>
		<div class="mui-content wrap3">
			<div class="space-up">
				<h1 style="text-align: center;">快捷支付开通成功</h1>
				<p style="margin-top: 50vh"><a class="my-btn-blue4" id="regBtn">返回首页</a></p>
			</div>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script src="/static/js/jquery-2.1.4.min.js"></script>
		<script src="/static/js/common.js"></script>
		<script type="text/javascript">
			$(function(){
		      var u = navigator.userAgent;
		      var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
		      var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
				mui(document).on('tap','#regBtn',function(){
			      if(!isAndroid){
			        window.webkit.messageHandlers.returnIndex.postMessage(1);
			      }else{
			        android.returnIndex();
			      }
				})
			})
		</script>
	</body>

</html>