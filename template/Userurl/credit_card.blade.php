
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
				<div class="mui-slider mui-slider1">
				  <div class="mui-slider-group mui-slider-loop">
				    <!--支持循环，需要重复图片节点最后一张-->
				    <div class="mui-slider-item mui-slider-item-duplicate"><a href="#"><img src="/static/images/20180224112730.png" /></a></div>
					    <!-- <div class="mui-slider-item"><a href="#"><img src="/static/images/20180224112001.png" /></a></div> -->
					    <!-- <div class="mui-slider-item"><a href="#"><img src="/static/images/20180224112730.png" /></a></div> -->
				    <!--支持循环，需要重复图片节点第一张-->
				    <div class="mui-slider-item mui-slider-item-duplicate"><a href="http://www.do68.com/"><img src="/static/images/20180224112001.png" /></a></div>
				  </div>
				</div>
			<div class="wrap">
			    <!--轮播2-->
			    	<div class="mui-slider mui-slider2 clear wrap-bt bg-w">
				  <div class="mui-slider-group mui-slider-loop">
				  	<!--支持循环，需要重复图片节点最后一张-->
				    <div class="mui-slider-item mui-slider-item-duplicate">
				    	<div class="my-slide-con fl"> 
			    		  <img src="http://wallet-source.oss-cn-hangzhou.aliyuncs.com/uploads/other/2018-08-29/55895b8658437c887.jpg">
			    		</div>
			    		<div class="my-slide-con fr wrap2">
			    			<div class="f-telli2">
			    			<p class="f14">交通银行卡</p>
			    			<p class="f12 invalid-color">加油88折，全车人员保障、道路救援加油88折，全车人员保障、道路救援</p>
			    			</div>
			    			<a href="http://www.365creditbank.com/index/index?id=2387&card=jt" class="my-slide-btn">立即申请</a>
			    		</div>
				    </div>
				   
				    <!--支持循环，需要重复图片节点第一张-->
<!-- 				    <div class="mui-slider-item mui-slider-item-duplicate">
				    	<div class="my-slide-con fl"> 
			    		  <img src="/static/images/20180224113642.png">
			    		</div>
			    		<div class="my-slide-con fr wrap2">
			    			<div class="f-telli2">
			    			<p class="f14">平安银行卡</p>
			    			<p class="f12 invalid-color">加油88折，全车人员保障、道路救援加油88折，全车人员保障、道路救援</p>
			    			</div>
			    			<a href="http://www.365creditbank.com/index/index?id=2387&card=pa" class="my-slide-btn">立即申请</a>
			    		</div>
				    </div>
 -->				  </div>
				</div>
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