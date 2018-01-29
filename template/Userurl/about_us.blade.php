<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>关于我们</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet"/>
	</head>
	<body>
		<div class="mui-content">
			<div id="slider" class="mui-slider">
		        <div id="sliderSegmentedControl" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted bg-w">  
		            <a class="mui-control-item bor-rad-l mui-active" href="#item1mobile">公司介绍</a>  
		            <a class="mui-control-item bor-rad-r" href="#item2mobile">资质证书</a>  
		            <a class="mui-control-item bor-rad-r" href="#item3mobile">联系我们</a>  
		        </div>  
		        <div id="sliderProgressBar" class="mui-slider-progress-bar mui-col-xs-4"></div>  
		        <div class="mui-slider-group space-up"> 
		        	<!--公司介绍-->
		            <div id="item1mobile" class="base-info md-f1 mui-slider-item mui-control-content detailInfos md-box md-ver bg-w mui-active">
		            	<h4 class="main-color fc about-tit space-up"><span class="mui-icon iconfont icon-gongsijianjie space-right"></span>
		            		公司介绍</h4>
		            	<div class="about-info wrap2">
		            		{!! $data->page_content !!}
		            	</div>
		            </div>
		            <!--资质证书-->
		            <div id="item2mobile" class="process bg md-f1 mui-slider-item mui-control-content detailInfos1 md-box md-ver bg-w">
		            	<h4 class="main-color fc about-tit space-up"><span class="mui-icon iconfont icon-zizhizhengshu space-right"></span>资质证书</h4>
		            	<div class="about-info">
		            		<!--卡片视图-->
		            		{!! $data->page_content !!}
		            		<!-- <div class="my-card">
								<div class="my-card-header wrap2">
									<span class="my-badge">01</span>
		            		        <span>某某证书</span>
								</div>
								<div class="my-card-content">
									 <img src="/static/images/certificate.png">
								</div>
							</div>
							<div class="my-card">
								<div class="my-card-header wrap2">
									<span class="my-badge">01</span>
		            		        <span>某某证书</span>
								</div>
								<div class="my-card-content">
									 <img src="/static/images/certificate.png">
								</div>
							</div> -->
		            	</div>
		            </div> 
		            <!--联系我们-->
		            <div id="item3mobile" class="process bg md-f1 mui-slider-item mui-control-content detailInfos1 md-box md-ver bg-w">
		            	<h4 class="main-color fc about-tit space-up"><span class="mui-icon iconfont icon-kefu space-right"></span>联系我们</h4>
		            	<div class="about-info">
		            		<!--列表-->
		            		<ul class="my-pad bor-bot dis-flex-ar">
		            		  <li>
		            		  	<a tel="{{$server['weixin']['service_contact']}}" id="wexNumber001">
		            		  		<div class="icon-container bg-color">
		            		  			<span class="mui-icon iconfont icon-weixin blue-color-th f36">
		            		  		</div>
		            		  		<div class="icon-con-into">
		            		  			<h4 class="main-color">微信客服</h4>
		            		  			<p class="invalid-color">点击复制微信号码</p>
		            		  		</div>
		            		  	</a>
		            		  </li>
		            		  <li>
		            		  	<a id="qqService001" tel="{{$server['qq']['service_contact']}}">
		            		  		<div class="icon-container bg-color">
		            		  			<span class="mui-icon iconfont icon-qq blue-color-th f36">
		            		  		</div>
		            		  		<div class="icon-con-into">
		            		  			<h4 class="main-color">QQ客服</h4>
		            		  			<p class="invalid-color">点击在线咨询</p>
		            		  		</div>
		            		  	</a>
		            		  </li>
		            		  <li>
		            		  	<a tel="{{$server['tel']['service_contact']}}" id="telPhone001">
		            		  		<div class="icon-container bg-color">
		            		  			<span class="mui-icon iconfont icon-dianhua blue-color-th f36">
		            		  		</div>
		            		  		<div class="icon-con-into">
		            		  			<h4 class="main-color">电话客服</h4>
		            		  			<p class="invalid-color">点击拨打电话</p>
		            		  		</div>
		            		  	</a>
		            		  </li>
		            		</ul>
		            	</div>
		            	<div class="my-pad2">
		            		<dl class="space-bot">
		            			<dt><h4><span class="mui-icon iconfont icon-icon104 blue-color-th v-m space-right"></span>公司地址</dt>
		            			<dd>{{$server['company_address']}}</dd>
		            		</dl>
		            		<ul class="dis-flex-be">
		            			<li>
		            				<h4><span class="mui-icon iconfont icon-my-phone blue-color-th v-m space-right"></span>商务合作</h4>
		            				<p class="f14">{{$server['phone']}}</p>
		            			</li>
		            			<li>
		            				<h4><span class="mui-icon iconfont icon-shijian-copy-copy blue-color-th v-m space-right"></span>工作时间</h4>
		            				<p class="f14">{{$server['working_hours']}}</p>
		            			</li>
		            		</ul>
		            	</div>
		            </div> 
		        </div>  
	        </div>  
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init();
		    mui.ready(function(){
		    	//qq客服
		    	document.getElementById("qqService001").addEventListener('tap',function(){
					var qq = document.getElementById("qqService001").getAttribute("tel");
		    		if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {  //判断iPhone|iPad|iPod|iOS
		    		 	window.webkit.messageHandlers.qqService.postMessage(qq);
		    		}else{
		    			android.qqService(qq);
		    		 //复制成功后提示  “内容已复制到粘贴板”
		    		}
				})
		    	//点击复制微信号码
		    	document.getElementById('wexNumber001').addEventListener('tap',function(){
		    		var wexNum = document.getElementById('wexNumber001').getAttribute('tel');
		    		 
		    		if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {  //判断iPhone|iPad|iPod|iOS
		    		 	window.webkit.messageHandlers.copyArticle.postMessage(wexNum);
		    		}else{

		    			android.copyArticle(wexNum);
		    		 //复制成功后提示  “内容已复制到粘贴板”
		    		}
		    		 //复制成功后提示  “内容已复制到粘贴板”
		    	});
		    	
		    	// document.getElementById('qqService001').addEventListener('tap',function(){
		    	// 	window.location.href="http://wpa.qq.com/msgrd?v=3&uin=174668774&site=qq&menu=yes";
		    	// });
		    	//android、ios交互 拨打电话
		    	document.getElementById('telPhone001').addEventListener('tap',function(){
                    var tel=document.getElementById('telPhone001').getAttribute('tel');
		            if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {  //判断iPhone|iPad|iPod|iOS
		                 window.webkit.messageHandlers.drialPhone.postMessage(tel);
		            } else if (/(Android)/i.test(navigator.userAgent)) {   //判断Android
		                 // window.AndroidMessage.call(tel);
		                 android.drialPhone(tel);
		            }
	                  
		            
		        });
		    });
		</script>
	</body>
</html>