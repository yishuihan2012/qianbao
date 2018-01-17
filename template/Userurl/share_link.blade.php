<!doctype html>
<html class="bg-w">
	<head>
		<meta charset="UTF-8">
		<title>分享推广链接</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
	</head>
	<body>
		<div class="mui-content" style="padding:0;">
			<a class="to-share" id="toShare">
				<img src="/static/images/logo.png"/>
			</a>
		</div>
		
		<script src="/static/js/mui.min.js"></script>
		<script src="/static/js/jquery-2.1.4.min.js"></script>
		<script type="text/javascript">
	      var u = navigator.userAgent;
	      var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
	      var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
			mui.init();
			$(function(){
				//立即去邀请
				// document.getElementById('toShare').addEventListener('tap',function(){
			var imgurl=location.origin+"/static/images/logo.png";
				mui(document).on('tap','img',function(){
					var url="{{$url}}";
					var title='注册链接';
				      if(!isAndroid){
				        window.webkit.messageHandlers.shareUrl.postMessage([url,title,title]);
				      }else{
				        android.shareUrl(url,title,title,imgurl);
				      }
				});
			});
		</script>
	</body>

</html>