<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>还款记录</title>
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
						进行中
					</a>
					<a class="mui-control-item" href="#item2mobile">
						已取消
					</a>
					<a class="mui-control-item" href="#item3mobile">
						已完成
					</a>
				</div>
				<div id="sliderProgressBar" class="mui-slider-progress-bar mui-col-xs-4"></div>
				<div class="mui-slider-group">
					<!--执行中-->
					<div id="item1mobile" class="mui-slider-item mui-control-content mui-active">
						<div id="scroll1" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<ul class="mui-table-view bg-color wrap">

								@foreach($generation as $list)
									<li class="mui-table-view-cell bg-w space-up f-br2">
									  <a href="/api/Userurl/repayment_plan_detail/order_no/{{$list['generation_id']}}">	
										<div class="dis-flex-be wrap bor-bot-das">
											<div class="dis-flex-fs">
												<p class="card-pic-container">
													<img src="/static/images/card.png">
												</p>
												<span class="f16">{{$list['card_bankname']}}(尾号{{$list['generation_card']}})</span>
											</div>
											<div class="green-color"><span class="iconfont icon-shijian-copy-copy space-right"></span><span class="f16">@if($list['generation_state']==2)执行中@elseif($list['generation_state']==1)待确认@elseif($list['generation_state']==3)还款结束@elseif($list['generation_state']==-1)还款失败@endif</span></div>
										</div>
										<div class="wrap">
											<p class="invalid-color f15">还款总金额(含手续费{{$list['generation_pound']}}元)</p>
											<p class="f24 space-up3"><strong>{{$list['generation_total']}}</strong><span class="f15">元</span></p>
											<p class="invalid-color f15 space-up3 f-tex-n">还款计划时间：<span class="blue-color-th space-right">{{date('m月d日',strtotime($list['generation_start']))}}-{{date('m月d日',strtotime($list['generation_end']))}} </span><span class="blue-color-th">{{$list['count']}}笔</span></p>
										</div>
										<div class="dis-flex-be invalid-color wrap bor-top-das">
											<span class="f16">查看详情</span>
											<span class="mui-icon mui-icon-arrowright f20"></span>
										</div>
									  </a>
									</li>
								@endforeach

								</ul>
							</div>
						</div>
					</div>
					<div id="item2mobile" class="mui-slider-item mui-control-content">
						<div id="scroll2" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<ul class="mui-table-view bg-color wrap">

								@foreach($generation1 as $list)
									<li class="mui-table-view-cell bg-w space-up f-br2">
									  <a href="/api/Userurl/repayment_plan_detail/order_no/{{$list['generation_id']}}">	
										<div class="dis-flex-be wrap bor-bot-das">
											<div class="dis-flex-fs">
												<p class="card-pic-container">
													<img src="/static/images/card.png">
												</p>
												<span class="f16">{{$list['card_bankname']}}(尾号{{$list['generation_card']}})</span>
											</div>
											<div class="green-color"><span class="iconfont icon-shijian-copy-copy space-right"></span><span class="f16">@if($list['generation_state']==2)执行中@elseif($list['generation_state']==1)待确认@elseif($list['generation_state']==3)还款结束@elseif($list['generation_state']==-1)还款失败@endif</span></div>
										</div>
										<div class="wrap">
											<p class="invalid-color f15">还款总金额(含手续费{{$list['generation_pound']}}元)</p>
											<p class="f24 space-up3"><strong>{{$list['generation_total']}}</strong><span class="f15">元</span></p>
											<p class="invalid-color f15 space-up3 f-tex-n">还款计划时间：<span class="blue-color-th space-right">{{date('m月d日',strtotime($list['generation_start']))}}-{{date('m月d日',strtotime($list['generation_end']))}}  </span><span class="blue-color-th">{{$list['count']}}笔</span></p>
										</div>
										<div class="dis-flex-be invalid-color wrap bor-top-das">
											<span class="f16">查看详情</span>
											<span class="mui-icon mui-icon-arrowright f20"></span>
										</div>
									  </a>
									</li>
								@endforeach

								</ul>
							</div>
						</div>

					</div>
					<div id="item3mobile" class="mui-slider-item mui-control-content">
						<div id="scroll3" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<ul class="mui-table-view bg-color wrap">

								@foreach($generation3 as $list)
									<li class="mui-table-view-cell bg-w space-up f-br2">
									  <a href="/api/Userurl/repayment_plan_detail/order_no/{{$list['generation_id']}}">	
										<div class="dis-flex-be wrap bor-bot-das">
											<div class="dis-flex-fs">
												<p class="card-pic-container">
													<img src="/static/images/card.png">
												</p>
												<span class="f16">{{$list['card_bankname']}}(尾号{{$list['generation_card']}})</span>
											</div>
											<div class="green-color"><span class="iconfont icon-shijian-copy-copy space-right"></span><span class="f16">@if($list['generation_state']==2)执行中@elseif($list['generation_state']==1)待确认@elseif($list['generation_state']==3)还款结束@elseif($list['generation_state']==-1)还款失败@endif</span></div>
										</div>
										<div class="wrap">
											<p class="invalid-color f15">还款总金额(含手续费{{$list['generation_pound']}}元)</p>
											<p class="f24 space-up3"><strong>{{$list['generation_total']}}</strong><span class="f15">元</span></p>
											<p class="invalid-color f15 space-up3 f-tex-n">还款计划时间：<span class="blue-color-th space-right">{{date('m月d日',strtotime($list['generation_start']))}}-{{date('m月d日',strtotime($list['generation_end']))}}  </span><span class="blue-color-th">{{$list['count']}}笔</span></p>
										</div>
										<div class="dis-flex-be invalid-color wrap bor-top-das">
											<span class="f16">查看详情</span>
											<span class="mui-icon mui-icon-arrowright f20"></span>
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
				var topH = document.getElementById("sliderSegmentedControl").offsetHeight;
				document.getElementById("item1mobile").style.minHeight = _h-topH-100 + 'px';
				document.getElementById("item2mobile").style.minHeight = _h-topH-100 + 'px';
				document.getElementById("item3mobile").style.minHeight = _h-topH-100 + 'px';
				mui('.mui-scroll-wrapper').scroll({
					indicators: false //是否显示滚动条
				});
				//已取消
				var html2 = "<ul class='mui-table-view bg-color wrap'>"+
								"<li class='mui-table-view-cell bg-w space-up f-br2'>"+
									  "<a href=''>"+
										"<div class='dis-flex-be wrap bor-bot-das'>"+
											"<div class='dis-flex-fs'>"+
												"<p class='card-pic-container'>"+
													"<img src='images/card.png'>"+
												"</p>"+
												"<span class='f16'>浦发银行(尾号2583)</span>"+
											"</div>"+
											"<div class='red-color'><span class='iconfont icon-shijian-copy-copy space-right'></span><span class='f16'>已取消</span></div>"+
										"</div>"+
										"<div class='wrap'>"+
											"<p class='invalid-color f15'>还款总金额(含手续费79.05元)</p>"+
											"<p class='f24 space-up3'><strong>10079.05</strong><span class='f15'>元</span></p>"+
											"<p class='invalid-color f15 space-up3 f-tex-n'>取消计划时间：<span class='blue-color-th space-right'>12月19日  14:25:58</span><span class='blue-color-th'>已还款2笔</span></p>"+
										"</div>"+
										"<div class='dis-flex-be invalid-color wrap bor-top-das'>"+
											"<span class='f16'>查看详情</span>"+
											"<span class='mui-icon mui-icon-arrowright f20'></span>"+
										"</div>"+
									  "</a>"+
									"</li>"+
								"</ul>";
				//已完成
				var html3 = "<ul class='mui-table-view bg-color wrap'>"+
								"<li class='mui-table-view-cell bg-w space-up f-br2'>"+
									  "<a href=''>"+
										"<div class='dis-flex-be wrap bor-bot-das'>"+
											"<div class='dis-flex-fs'>"+
												"<p class='card-pic-container'>"+
													"<img src='images/card.png'>"+
												"</p>"+
												"<span class='f16'>浦发银行(尾号2583)</span>"+
											"</div>"+
											"<div class='blue-color-th'><span class='iconfont icon-successful space-right'></span><span class='f16'>已完成</span></div>"+
										"</div>"+
										"<div class='wrap'>"+
											"<p class='invalid-color f15'>还款总金额(含手续费79.05元)</p>"+
											"<p class='f24 space-up3'><strong>10079.05</strong><span class='f15'>元</span></p>"+
											"<p class='invalid-color f15 space-up3 f-tex-n'>还款计划开始时间：<span class='blue-color-th space-right'>12月19日 </span><span class='blue-color-th'>6笔</span></p>"+
										"</div>"+
										"<div class='dis-flex-be invalid-color wrap bor-top-das'>"+
											"<span class='f16'>查看详情</span>"+
											"<span class='mui-icon mui-icon-arrowright f20'></span>"+
										"</div>"+
									  "</a>"+
									"</li>"+
								"</ul>";
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