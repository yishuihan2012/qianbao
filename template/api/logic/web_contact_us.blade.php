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
			  	<a id="qqService002" tel="{{$qqInfo['service_contact']}}">
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
			  	<a tel="{{$phoneInfo['service_contact']}}" id="telPhone002">
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
			       <p class="poa-r invalid-color">{{$server['working_hours']}}</p>
			    </li>
			    <li class="mui-table-view-cell">
			        <a class="mui-navigate-right" tel="{{$server['phone']}}" id="telPhone003">
			        	<p>合作意向咨询</p>
			        	<p class="invalid-color">请联系{{$server['phone']}}</p>
			        </a>
			    </li>
			</ul>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init();
			mui.ready(function(){
				//qq客服
				document.getElementById("qqService002").addEventListener('tap',function(){

					var qq = document.getElementById("qqService002").getAttribute("tel");

		    		if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {  //判断iPhone|iPad|iPod|iOS

		    		 	window.webkit.messageHandlers.qqService.postMessage(qq);
		    		}else{

		    			android.qqService(qq);
		    		 //复制成功后提示  “内容已复制到粘贴板”
		    		}
				})
		    	//点击复制微信号码
		    	document.getElementById('wexNumber002').addEventListener('tap',function(){
		    		var wexNum = document.getElementById('wexNumber002').getAttribute('tel');

		    		if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {  //判断iPhone|iPad|iPod|iOS
		    		 	window.webkit.messageHandlers.copyArticle.postMessage(wexNum);
		    		}else{

		    			android.copyArticle(wexNum);
		    		 //复制成功后提示  “内容已复制到粘贴板”
		    		}
		    	});
		    	//qq客服
		    	// document.getElementById('qqService002').addEventListener('tap',function(){
		    	// 	window.location.href="http://wpa.qq.com/msgrd?v=3&uin={{$qqInfo['service_contact']}}&site=qq&menu=yes";
		    	// });
		    	//android、ios交互 拨打电话
		    	document.getElementById('telPhone002').addEventListener('tap',function(){	                   
	                        var tel=document.getElementById('telPhone002').getAttribute('tel');
				            if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {  //判断iPhone|iPad|iPod|iOS
				            	window.webkit.messageHandlers.drialPhone.postMessage(tel);

				            } else {   //判断Android
				                 android.drialPhone(tel);
				            }

		        });
		        //android、ios交互 拨打电话
		    	document.getElementById('telPhone003').addEventListener('tap',function(){
	
                        var tel=document.getElementById('telPhone003').getAttribute('tel');
			            if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {  //判断iPhone|iPad|iPod|iOS
			            	window.webkit.messageHandlers.drialPhone.postMessage(tel);

			            } else {   //判断Android

			                 android.drialPhone(tel);
			            }
               
                });
		    });
		</script>
	</body>

</html>