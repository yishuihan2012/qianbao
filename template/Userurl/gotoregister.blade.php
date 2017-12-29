<!doctype html>
<html class="bg-w">
	<head>
		<meta charset="UTF-8">
		<title>分享下载链接</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
	</head>
	<body>
		<div class="mui-content bg-w wrap bor-bot">
			<div class="exc-code" id="shareLink">
			</div>
		</div>
		<nav class="mui-bar mui-bar-tab wrap bg-w dis-flex-ar" id="muiBarTab">
		  <a class="main-color f16" id="shareCode"><span class="mui-icon iconfont icon-fenxiang space-right"></span>分享链接</a>
		</nav>
		<script src="js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init();
			mui.ready(function(){
				var _h =  window.screen.availHeight;
				document.getElementById("shareLink").style.height = _h - 150 +"px";
				//分享二维码
				document.getElementById('shareCode').addEventListener('tap',function(){
					
				});
			});
		</script>
	</body>

</html>