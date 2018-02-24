
<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>{{$info->list_name}}</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link href="/static/css/mui.min.css" rel="stylesheet" />
		<link href="/static/css/iconfont.css" rel="stylesheet" />
		<link href="/static/css/base.css" rel="stylesheet" />
		<link href="/static/css/page.css" rel="stylesheet" />
		<link href="/static/css/themes.css" rel="stylesheet" />
	</head>
	<body>
		<div class="mui-content">
			<!--头部轮播图-->
			
			<div class="wrap">
			    <!--轮播2-->
				<!--轮播2结束-->
				<!--列表-->
				<div class="wrap-bt">
					<p>{{$info->list_name}}</p>
				    <div class="mui-row space-up2">
				    	@foreach($list as $k => $v)
				        <div class="mui-col-sm-4 mui-col-xs-4">
				            <li class="mui-table-view-cell wrap-list bg-w">
				                <a class="fc" href="{{$v->list_url}}">
				                    <img src="{{$v->list_icon}}" class="media-img"> 
				                    <div class="bor-top por space-up2">
				                    	<!-- <span class="my-badge-org">秒批</span> -->
					                    <p class="f14 space-up">{{$v->list_name}}</p>
					                    <!-- <p class="f12 invalid-color f-telli">周五加油拿5%优惠</p> -->
				                	</div>
				                </a>
				            </li>
				        </div>
				         @endforeach
				    </div>
				</div>
			 </div>
		</div>
		<script src="/static/js/mui.min.js"></script>
		<script type="text/javascript">
			mui.init();
			mui.ready(function(){
				var gallery1 = mui('.mui-slider1');
				gallery1.slider({
				  interval:6000//自动轮播周期，若为0则不自动播放，默认为0；
				});
				var gallery2 = mui('.mui-slider2');
				gallery2.slider({
				  interval:3000//自动轮播周期，若为0则不自动播放，默认为0；
				});
				mui('.mui-content').on('tap','a',function(){
			      window.top.location.href=this.href;
			    });
			})
		</script>
	</body>

</html>