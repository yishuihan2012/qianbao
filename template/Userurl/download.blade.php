<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>下载APP</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet"/>
	</head>
	<body>
		<div class="mui-content down-load">
			<div class="prompt-message" id="promptMessage">
		      <p class="f14">点击右上角按钮，然后在弹出的菜单中，点击在浏览器中打开，即可安装</p>
		      <div class="pic-cont">
		         <img src="/static/images/alert_arrow.png">
		      </div>
		    </div>
			<div class="pic-container">
			  <img src="/static/images/logo.png">
		    </div>
		    <p class="fc"><a class="my-btn-blue5" id="android"><span class="mui-icon iconfont icon-android space-right f20"></span>安卓版本下载</a></p>
		    <p class="fc space-up-down"><a class="my-btn-blue5" id="ios"><span class="mui-icon iconfont icon-ios space-right f20"></span>iOS版本下载</a></p>
		    <div class="pic-container2"><img src="/static/images/home_pic.png" style="width:100%;"></div>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script src="/static/js/jquery-2.1.4.min.js"></script>
		<script type="text/javascript">
			mui.init();
			mui.ready(function(){
				//判断是否是qq或微信打开
			    var BrowserUtil = (function() {
			      var ua = navigator.userAgent.toLowerCase();
			      function isWeChat() {//微信
			        return (/micromessenger/.test(ua)) ? true : false;
			      }
			      function isQQ() {//qq
			        return (/qq\//.test(ua)) ? true : false;
			      }
			      return {
			        isWechat: isWeChat,
			        isQQ: isQQ,
			      };
			    })();
			    if(BrowserUtil.isWechat() || BrowserUtil.isQQ()){
		          $("#promptMessage").show();
		      }
			    $("#android").click(function(){
			        if(BrowserUtil.isWechat() || BrowserUtil.isQQ()){
			        	mui.toast("请在浏览器打开");
			        }else{
			           //android端下载地址
			           window.location.href="{{$data['android_url']}}"; 
			        }
			    });
			    $("#ios").click(function(){
			        if(BrowserUtil.isWechat() || BrowserUtil.isQQ()){
			        	mui.toast("请在浏览器打开");
			        }else{
			           //ios端下载地址
			           window.location.href="{{$data['ios_url']}}"; 
			        }
			      });
			});
		</script>
	</body>

</html>