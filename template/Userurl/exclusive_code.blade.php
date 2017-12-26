<!doctype html>
<html  class="bg-w">
	<head>
		<meta charset="UTF-8">
		<title>专属二维码</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
	</head>
	<body>
		<div class="mui-content wrap bg-w bor-bot" id="excCodeContent">
			<div class="exc-code"  id="excCode">
			<div class="code-container bg-w f-br wrap">
				<img src="/static/images/code.png">
			</div>
			</div>
		</div>
		<nav class="mui-bar mui-bar-tab wrap bg-w dis-flex-ar" id="muiBarTab">
		  <a class="main-color f16"><span class="mui-icon iconfont icon-cloud-download space-right"></span>保存到手机</a>
		  <span class="line bg-color"></span>
		  <a class="main-color f16"><span class="mui-icon iconfont icon-fenxiang space-right"></span>分享二维码</a>
		</nav>
		<script src="/static/js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init();
			mui.ready(function(){
				var _h =  window.screen.availHeight;
				document.getElementById("excCode").style.height = _h - 150 +"px";
			});
		</script>
	</body>

</html>