<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>还款计划列表</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet"/>
	</head>
	<body>
		<div class="mui-content repay-plan-list">
			<!--还款计划列表头部-->
			<div class="repay-plan-list-top" id="repayPlanTop">
				<div class="dis-flex-be bor-bot-das">
					<div class="space-bot">
						<p class="f15">
							<span class="invalid-color">计划编号：</span>
							<span>165189744130484103</span>
						</p>
						<p class="f15 invalid-color space-up3">
							<span>提交时间：</span>
							<span>2017-12-08 15:32:25</span>
						</p>
					</div>
					<!--计划状态为已完成时显示，未完成时不显示-->
					<p class="f16 blue-color-th">已完成</p>
				</div>
				<div class="dis-flex-ar space-up2 fc">
					<div>
					    <p class="f15 invalid-color">已还金额(元)</p>
					    <p class="space-up3">3000.00</p>
					</div>
					<div>
					    <p class="f15 invalid-color">未还金额(元)</p>
					    <p class="blue-color-th space-up3">0.00</p>
					</div>
					<div>
					    <p class="f15 invalid-color">应还总金额(元)</p>
					    <p class="space-up3">3000.00</p>
					</div>
				</div>
			</div>
			<!--还款计划列表-->
			<div id="slider" class="mui-slider">
				<div id="sliderSegmentedControl" class="mui-slider-indicator mui-segmented-control mui-segmented-control-inverted bg-w space-up3">
					<a class="mui-control-item mui-active" href="#item1mobile">
						全部记录
					</a>
					<a class="mui-control-item" href="#item2mobile">
						已执行
					</a>
					<a class="mui-control-item" href="#item3mobile">
						未执行
					</a>
				</div>
				<div id="sliderProgressBar" class="mui-slider-progress-bar mui-col-xs-4"></div>
				<div class="mui-slider-group space-up3" id="itemmobile">


					<div id="item1mobile" class="mui-slider-item mui-control-content bg-w mui-active">
						<div id="scroll1" class="mui-scroll-wrapper bg-color">
							<div class="mui-scroll repay-plan-list-wrap">
								<ul class="mui-table-view bg-color">
								@foreach ($order as $list)
									<li class="mui-table-view-cell bg-w bor-bot">
										<a class="mui-navigate-right" href="repayment_plan_detail.html">
										  <div class="dis-flex-be">
											<p class="f16 dis-flex fc">{{$list['order_add_time']}}</p>
											<p class="f16 dis-flex fc">{{$list['order_money']}}元</p>
											<p class="f16 dis-flex ftr space-right2">@if ($list['order_status']==-1) 失败 @elseif ($list['order_status']==2)成功 @else 待执行 @endif</p>
										  </div>
										</a>
									</li>
								@endforeach
								</ul>
							</div>
						</div>
					</div>



					<div id="item2mobile" class="mui-slider-item mui-control-content bg-w">
						<div id="scroll2" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<ul class="mui-table-view bg-color">
								@foreach ($order2 as $list)
									<li class="mui-table-view-cell bg-w bor-bot">
										<a class="mui-navigate-right" href="repayment_plan_detail.html">
										  <div class="dis-flex-be">
											<p class="f16 dis-flex fc">{{$list['order_add_time']}}</p>
											<p class="f16 dis-flex fc">{{$list['order_money']}}元</p>
											<p class="f16 dis-flex ftr space-right2">@if ($list['order_status']==-1) 失败 @elseif ($list['order_status']==2)成功 @else 待执行 @endif</p>
										  </div>
										</a>
									</li>
								@endforeach
								</ul>
							</div>
						</div>

					</div>
					<div id="item3mobile" class="mui-slider-item mui-control-content bg-w">
						<div id="scroll3" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<ul class="mui-table-view bg-color">
								@foreach ($order1 as $list)
									<li class="mui-table-view-cell bg-w bor-bot">
										<a class="mui-navigate-right" href="repayment_plan_detail.html">
										  <div class="dis-flex-be">
											<p class="f16 dis-flex fc">{{$list['order_add_time']}}</p>
											<p class="f16 dis-flex fc">{{$list['order_money']}}元</p>
											<p class="f16 dis-flex ftr space-right2">@if ($list['order_status']==-1) 失败 @elseif ($list['order_status']==2)成功 @else 待执行 @endif</p>
										  </div>
										</a>
									</li>
								@endforeach
								</ul>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init();
			mui('.mui-table-view-cell').on('tap','a',function(){
		      window.top.location.href=this.href;
		    });
			mui.ready(function(){
			    var _h =  window.screen.availHeight;
				var topH = document.getElementById("repayPlanTop").offsetHeight;
				document.getElementById("item1mobile").style.minHeight = _h-topH-150 + 'px';
				document.getElementById("item2mobile").style.minHeight = _h-topH-150 + 'px';
				document.getElementById("item3mobile").style.minHeight = _h-topH-150 + 'px';
		
				mui('.mui-scroll-wrapper').scroll({
					indicators: false //是否显示滚动条
				});
				//已执行
				var html2 = '<ul class="mui-table-view">'+
								'<li class="mui-table-view-cell bg-w bor-bot">'+
									'<a class="mui-navigate-right" href="repayment_plan_detail.html">'+
									  '<div class="dis-flex-be">'+
										'<p class="f16 dis-flex fc">2017-12-09 08:45:53</p>'+
										'<p class="f16 dis-flex fc">211元</p>'+
										'<p class="f16 dis-flex ftr space-right2">执行成功</p>'+
									  '</div>'+
									'</a>'+
								'</li>'+
								'</ul>';
				//未执行
				var html3 = '<ul class="mui-table-view">'+
								'<li class="mui-table-view-cell bg-w bor-bot">'+
									'<a class="mui-navigate-right" href="repayment_plan_detail.html">'+
									  '<div class="dis-flex-be">'+
										'<p class="f16 dis-flex fc">2017-12-09 08:45:53</p>'+
										'<p class="f16 dis-flex fc">211元</p>'+
										'<p class="f16 dis-flex ftr space-right2">执行成功</p>'+
									  '</div>'+
									'</a>'+
								'</li>'+
								'</ul>';
				var item2 = document.getElementById('item2mobile');
				var item3 = document.getElementById('item3mobile');
				//
				document.getElementById('slider').addEventListener('slide', function(e) {
					if (e.detail.slideNumber === 1) {
						if (item2.querySelector('.mui-loading')) {
							setTimeout(function() {
								item2.querySelector('.mui-scroll').innerHTML = html2;
							}, 500);
						}
					} else if (e.detail.slideNumber === 2) {
						if (item3.querySelector('.mui-loading')) {
							setTimeout(function() {
								item3.querySelector('.mui-scroll').innerHTML = html3;
							}, 500);
						}
					}
				});
				var sliderSegmentedControl = document.getElementById('sliderSegmentedControl');
				mui('.mui-input-group').on('change', 'input', function() {
					if (this.checked) {
						sliderSegmentedControl.className = 'mui-slider-indicator mui-segmented-control mui-segmented-control-inverted mui-segmented-control-' + this.value;
						//force repaint
						sliderProgressBar.setAttribute('style', sliderProgressBar.getAttribute('style'));
					}
				});
			});
		</script>
	</body>
</html>