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
					@foreach($class as $key=>$value)
						<a class="mui-control-item @if($key==0) mui-active @endif" href="#item{{$key+1}}mobile">
							{{$value['novice_class_title']}}
						</a>
					@endforeach
					
				</div>

				<!-- <div id="sliderProgressBar" class="mui-slider-progress-bar mui-col-xs-6"></div> -->
				<div id="sliderProgressBar" class="mui-slider mui-col-xs-6"></div>
				<div class="mui-slider-group">
					<!--执行中-->
					@foreach($class as $key=>$value)
					<div id="item{{$key+1}}mobile" class="mui-slider-item mui-control-content @if($key==0) mui-active @endif">
						<div id="scroll{{$key+1}}" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<ul class="mui-table-view bg-color">
									@foreach($value['repaymentList'] as $k => $v )
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
					@endforeach
					
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