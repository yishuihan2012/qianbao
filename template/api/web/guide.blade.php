<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>新手指引</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="css/mui.min.css" rel="stylesheet" />
		<link href="css/iconfont.css" rel="stylesheet" />
		<link href="css/base.css" rel="stylesheet" />
		<link href="css/page.css" rel="stylesheet" />
		<link href="css/themes.css" rel="stylesheet"/>
	</head>
	<body>
		<div class="mui-content repayment-history">
			<!--还款计划列表-->
			<div id="slider" class="mui-slider">
				<div id="sliderSegmentedControl" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted bg-w">
					<a class="mui-control-item mui-active" href="#item1mobile">
						自动还款
					</a>
					<a class="mui-control-item" href="#item2mobile">
						收款
					</a>
				</div>
				<div id="sliderProgressBar" class="mui-slider-progress-bar mui-col-xs-6"></div>
				<div class="mui-slider-group">
					<!--执行中-->
					<div id="item1mobile" class="mui-slider-item mui-control-content mui-active">
						<div id="scroll1" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<ul class="mui-table-view bg-color">
									<li class="mui-table-view-cell mui-collapse bor-bot">
									  <a class="mui-navigate-right bg-w f16" href="#">
									  	<span class="bor-left-blue wrap-lr"></span>代还功能应该如何使用？</a>
							            <div class="mui-collapse-content">
							                <p>1.选择要还款的信用卡;</p>
							                <p>2.输入还款金额和信用卡余额;</p>
							                <p>3.系统自动生成还款计划;</p>
							                <p>4.输入短信验证码,开始还款计划;</p>
							                <p>5.等待还款结束。</p>
							            </div>
									</li>
									<li class="mui-table-view-cell mui-collapse bor-bot">
									  <a class="mui-navigate-right bg-w f16" href="#">
									  	<span class="bor-left-blue wrap-lr"></span>怎么收费？怎么付费？费率多少？</a>
							            <div class="mui-collapse-content">
							                <p>1.选择要还款的信用卡;</p>
							                <p>2.输入还款金额和信用卡余额;</p>
							                <p>3.系统自动生成还款计划;</p>
							                <p>4.输入短信验证码,开始还款计划;</p>
							                <p>5.等待还款结束。</p>
							            </div>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<div id="item2mobile" class="mui-slider-item mui-control-content">
						<div id="scroll2" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<li class="mui-table-view-cell mui-collapse bor-bot">
									  <a class="mui-navigate-right bg-w f16" href="">
									  	<span class="bor-left-blue wrap-lr"></span>收款</a>
							            <div class="mui-collapse-content">
							                <p>1.选择要还款的信用卡;</p>
							                <p>2.输入还款金额和信用卡余额;</p>
							                <p>3.系统自动生成还款计划;</p>
							                <p>4.输入短信验证码,开始还款计划;</p>
							                <p>5.等待还款结束。</p>
							            </div>
									</li>
									<li class="mui-table-view-cell mui-collapse bor-bot">
									  <a class="mui-navigate-right bg-w f16" href="#">
									  	<span class="bor-left-blue wrap-lr"></span>怎么收费？怎么付费？费率多少？</a>
							            <div class="mui-collapse-content">
							                <p>1.选择要还款的信用卡;</p>
							                <p>2.输入还款金额和信用卡余额;</p>
							                <p>3.系统自动生成还款计划;</p>
							                <p>4.输入短信验证码,开始还款计划;</p>
							                <p>5.等待还款结束。</p>
							            </div>
									</li>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script src="js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init();
			mui.ready(function(){
				var _h =  window.screen.availHeight;
				var topH = document.getElementById("sliderSegmentedControl").offsetHeight;
				document.getElementById("item1mobile").style.minHeight = _h-topH-100 + 'px';
				document.getElementById("item2mobile").style.minHeight = _h-topH-100 + 'px';
				mui('.mui-scroll-wrapper').scroll({
					indicators: false //是否显示滚动条
				});
			});
		</script>
	</body>

</html>