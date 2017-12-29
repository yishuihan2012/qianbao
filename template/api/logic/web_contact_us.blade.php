<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>联系客服</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet" />
	</head>
	<body>
		<div class="mui-content cus-ser">
			<ul class="my-pad dis-flex-ar space-bot bg-w">
			  <li>
			  	<a tel="{{$wxInfo['service_contact']}}" id="wexNumber002">
			  		<div class="icon-container bg-color">
			  			<span class="mui-icon iconfont icon-weixin f36">
			  		</div>
			  		<div class="icon-con-into">
			  			<h4 class="main-color">微信客服</h4>
			  			<p class="invalid-color">点击复制微信号码</p>
			  		</div>
			  	</a>
			  </li>
			  <li>
			  	<a id="qqService002">
			  		<div class="icon-container bg-color">
			  			<span class="mui-icon iconfont icon-qq f36">
			  		</div>
			  		<div class="icon-con-into">
			  			<h4 class="main-color">QQ客服</h4>
			  			<p class="invalid-color">点击在线咨询</p>
			  		</div>
			  	</a>
			  </li>
			  <li>
			  	<a tel="4006750789" id="telPhone002">
			  		<div class="icon-container bg-color">
			  			<span class="mui-icon iconfont icon-dianhua f36">
			  		</div>
			  		<div class="icon-con-into">
			  			<h4 class="main-color">电话客服</h4>
			  			<p class="invalid-color">点击拨打电话</p>
			  		</div>
			  	</a>
			  </li>
			</ul>
			<ul class="mui-table-view">
			    <li class="mui-table-view-cell">
			       <p>工作时间</p>
			       <p class="poa-r invalid-color">{{$qqInfo['service_time']}}</p>
			    </li>
			    <li class="mui-table-view-cell">
			        <a class="mui-navigate-right" tel="{{$phoneInfo['service_contact']}}" id="telPhone003">
			        	<p>合作意向咨询</p>
			        	<p class="invalid-color">请联系{{$phoneInfo['service_contact']}}</p>
			        </a>
			    </li>
			</ul>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init();
			mui.ready(function(){
		    	//点击复制微信号码
		    	document.getElementById('wexNumber002').addEventListener('tap',function(){
		    		var wexNum = document.getElementById('wexNumber002').getAttribute('tel');
		    		if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {  //判断iPhone|iPad|iPod|iOS
		    		 	window.webkit.messageHandlers.copyArticle.postMessage(wexNum);
		    		}else{
		    			// alert();
		    			android.copyArticle(tel);
		    		 //复制成功后提示  “内容已复制到粘贴板”
		    		}
		    	});
		    	//qq客服
		    	document.getElementById('qqService002').addEventListener('tap',function(){
		    		window.location.href="http://wpa.qq.com/msgrd?v=3&uin={{$qqInfo['service_contact']}}&site=qq&menu=yes";
		    	});
		    	//android、ios交互 拨打电话
		    	document.getElementById('telPhone002').addEventListener('tap',function(){
		    		var btnArray = ['否', '是'];
	                mui.confirm('是否拨打？', '{{$phoneInfo["service_contact"]}}', btnArray, function(e) {
	                    if (e.index == 1) {
	                        var tel=document.getElementById('telPhone002').getAttribute('tel');
				            if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {  //判断iPhone|iPad|iPod|iOS
				                 window.webkit.AndroidMessage.drialPhone.postMessage(tel);
				            } else if (/(Android)/i.test(navigator.userAgent)) {   //判断Android
				                 android.drialPhone(tel);
				            }
	                    }
	                });
		        });
		        //android、ios交互 拨打电话
		    	document.getElementById('telPhone003').addEventListener('tap',function(){
	    		 var btnArray = ['否', '是'];
                  mui.confirm('是否拨打？', '{{$phoneInfo["service_contact"]}}', btnArray, function(e) {
                    if (e.index == 1) {
                        var tel=document.getElementById('telPhone003').getAttribute('tel');
			            if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {  //判断iPhone|iPad|iPod|iOS
			                 window.webkit.AndroidMessage.drialPhone.postMessage(tel);
			            } else if (/(Android)/i.test(navigator.userAgent)) {   //判断Android
			                 android.drialPhone(tel);
			            }
                    }
                  });
                });
		    });
		</script>
	</body>

</html>