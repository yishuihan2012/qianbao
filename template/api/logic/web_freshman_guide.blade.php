<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>新手指引</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet"/>
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
									@foreach($repaymentList as $k => $v )
									<li class="mui-table-view-cell mui-collapse bor-bot">
									  <a class="mui-navigate-right bg-w f16" href="#">
									  	<span class="bor-left-blue wrap-lr"></span>{{$v['novice_name']}}</a>
							            <div class="mui-collapse-content">
							                {!!$v['novice_contents']!!}
							            </div>
									</li>
									@endforeach
								</ul>
							</div>
						</div>
					</div>
					<div id="item2mobile" class="mui-slider-item mui-control-content">
						<div id="scroll2" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								@foreach($receivablesList as $k => $v )
								<li class="mui-table-view-cell mui-collapse bor-bot">
								  <a class="mui-navigate-right bg-w f16" href="">
								  	<span class="bor-left-blue wrap-lr"></span>{{$v['novice_name']}}</a>
						            <div class="mui-collapse-content">
						                {!!$v['novice_contents']!!}
						            </div>
								</li>
								@endforeach
									
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script src="/static/js/mui.min.js"></script>
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